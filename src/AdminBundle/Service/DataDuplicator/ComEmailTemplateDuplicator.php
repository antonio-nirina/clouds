<?php
namespace AdminBundle\Service\DataDuplicator;

use AdminBundle\Entity\ComEmailTemplate;
use Doctrine\ORM\EntityManager;
use AdminBundle\Entity\Program;
use AdminBundle\Service\MailJet\MailJetTemplate;
use AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator;

class ComEmailTemplateDuplicator
{
    const NEW_DUPLICATED_TEMPLATE_SUFFIX = ' (copie)';

    private $em;
    private $mailjet_template_handler;
    private $template_data_generator;

    public function __construct(
        EntityManager $em,
        MailJetTemplate $mailjet_template_handler,
        TemplateDataGenerator $template_data_generator
    ) {
        $this->em = $em;
        $this->mailjet_template_handler = $mailjet_template_handler;
        $this->template_data_generator = $template_data_generator;
    }

    public function duplicate(Program $program, ComEmailTemplate $com_email_template, $user)
    {
        $new_template = clone $com_email_template;
        $new_template->setIdToNull()
            ->setDistantTemplateId(null)
            ->setName($this->generateTemplateName($program, $new_template->getName()))
            ->setLastEditUser($user)
            ->setLastEdit(new \DateTime('now'))
            ->setContentsToEmpty();

        $this->duplicateTemplateContents($com_email_template, $new_template);

        $distant_duplicated_template_id = $this->createDistantDuplicatedTemplate($new_template);
        if (!is_null($distant_duplicated_template_id)) {
            $new_template->setDistantTemplateId($distant_duplicated_template_id);
            $this->em->persist($new_template);
            $this->em->flush();
            return $new_template->getId();
        }

        return null;
    }

    public function createDistantDuplicatedTemplate(ComEmailTemplate $com_email_template)
    {
        $this->template_data_generator->setComEmailTemplate($com_email_template);
        return $this->mailjet_template_handler->createNonExistentDistantTemplate(
            $com_email_template->getName(),
            $this->template_data_generator->retrieveHtml(),
            $this->template_data_generator->retrieveText()
        );
    }

    public function duplicateTemplateContents(ComEmailTemplate $source, ComEmailTemplate $target, $persist = true)
    {
        foreach ($source->getContents() as $content) {
            $new_content = clone $content;
            $new_content->setIdToNull();
            $target->addContent($new_content);
            $new_content->setTemplate($target);

            if (true == $persist) {
                $this->em->persist($new_content);
            }
        }
    }


    private function generateTemplateName(Program $program, $source_name)
    {
        $same_name_template_state = true;
        $name = $source_name;
        while (true == $same_name_template_state) {
            $name .= self::NEW_DUPLICATED_TEMPLATE_SUFFIX;
            $com_email_template = $this->em->getRepository('AdminBundle\Entity\ComEmailTemplate')
                ->findOneBy(array(
                    'name' => $name,
                    'program' => $program,
                ));
            if (is_null($com_email_template)) {
                $same_name_template_state = false;
            }
        }

        return $name;
    }
}