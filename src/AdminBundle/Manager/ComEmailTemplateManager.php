<?php
namespace AdminBundle\Manager;

use AdminBundle\Entity\Program;
use AdminBundle\Entity\ComEmailTemplate;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Component\CommunicationEmail\TemplateModel;
use AdminBundle\Component\CommunicationEmail\TemplateContentType;

class ComEmailTemplateManager
{
    private $em;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function createTemplate(Program $program, ComEmailTemplate $template, $flush = true)
    {
        $logo_image = $template->getLogo();
        if (!is_null($logo_image)) {
            $logo_image->move(
                $this->container->getParameter('emailing_template_logo_upload_dir'),
                $logo_image->getClientOriginalName()
            );
            $template->setLogo($logo_image->getClientOriginalName());
        }
        $template->setProgram($program);

        foreach ($template->getContents() as $content) {
            if (TemplateModel::TEXT_ONLY == $template->getTemplateModel()) {
                if (TemplateContentType::TEXT == $content->getContentType()) {
                    $content->setTemplate($template);
                    $this->em->persist($content);
                } else {
                    $template->removeContent($content);
                    $content->setTemplate(null);
                }
            } elseif (TemplateModel::TEXT_AND_IMAGE == $template->getTemplateModel()) {
                $content->setTemplate($template);
                $this->em->persist($content);
            }
        }
        $this->em->persist($template);

        if (true == $flush) {
            $this->flush();
        }
    }

    public function flush()
    {
        $this->em->flush();
    }
}