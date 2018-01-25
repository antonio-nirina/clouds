<?php
namespace AdminBundle\Service\DataSynchronizer;

use AdminBundle\Service\MailJet\MailJetTemplate;
use AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator;
use AdminBundle\Manager\ComEmailTemplateManager;
use AdminBundle\Service\ComEmailingTemplate\TemplateThumbnailGenerator;

class ComEmailTemplateDataSynchronizer
{
    private $mailjet_template_handler;
    private $template_data_generator;
    private $com_email_template_manager;
    private $template_thumbnail_generator;

    public function __construct(
        MailJetTemplate $mailjet_template_handler,
        TemplateDataGenerator $template_data_generator,
        ComEmailTemplateManager $com_email_template_manager,
        TemplateThumbnailGenerator $template_thumbnail_generator
    ) {
        $this->mailjet_template_handler = $mailjet_template_handler;
        $this->template_data_generator = $template_data_generator;
        $this->com_email_template_manager = $com_email_template_manager;
        $this->template_thumbnail_generator = $template_thumbnail_generator;
    }

    public function editTemplate(
        $com_email_template,
        $app_user,
        $original_contents,
        $original_logo_image,
        $original_contents_image,
        $delete_logo_image_command,
        $delete_contents_image_command
    ) {
        $this->com_email_template_manager->editTemplate(
            $com_email_template,
            $app_user,
            $original_contents,
            $original_logo_image,
            $original_contents_image,
            $delete_logo_image_command,
            $delete_contents_image_command,
            $flush = false
        );

        $this->template_data_generator->setComEmailTemplate($com_email_template);
        $distant_template_id = $this->mailjet_template_handler->editTemplate(
            $com_email_template->getDistantTemplateId(),
            $this->template_data_generator->retrieveHtml(),
            $this->template_data_generator->retrieveText()
        );

        if (!is_null($distant_template_id)) {
            $this->com_email_template_manager->flush();
            /*$this->template_thumbnail_generator->setComEmailTemplate($com_email_template);
            $this->template_thumbnail_generator->generate();*/

            return true;
        } else {
            return false;
        }
    }

    public function createTemplate($program, $com_email_template, $app_user)
    {
        $distant_template_id = $this->mailjet_template_handler->createNonExistentDistantTemplate($com_email_template->getName());
        if (is_null($distant_template_id)) {
            return null;
        }

        $com_email_template->setDistantTemplateId($distant_template_id);
        $this->com_email_template_manager->createTemplate(
            $program,
            $com_email_template,
            $app_user,
            $flush = false
        );

        $this->template_data_generator->setComEmailTemplate($com_email_template);
        $distant_template_id = $this->mailjet_template_handler->editDistantTemplateContent(
            $distant_template_id,
            $this->template_data_generator->retrieveHtml(),
            $this->template_data_generator->retrieveText()
        );

        if (is_null($distant_template_id)) {
            $this->mailjet_template_handler->deleteDistantTemplate($distant_template_id);
            return null;
        } else {
            $this->com_email_template_manager->flush();
            /*$this->template_thumbnail_generator->setComEmailTemplate($com_email_template);
            $this->template_thumbnail_generator->generate();*/

            return $distant_template_id;
        }
    }

    public function deleteTemplate($com_email_template)
    {
        $delete_res = $this->mailjet_template_handler
            ->deleteDistantTemplate($com_email_template->getDistantTemplateId());
        if (true == $delete_res) {
            /*$this->template_thumbnail_generator->setComEmailTemplate($com_email_template);
            $this->template_thumbnail_generator->deleteThumbnailFile();*/
            $this->com_email_template_manager->deleteTemplateAndContents($com_email_template);
            
            return true;
        }

        return false;
    }
}