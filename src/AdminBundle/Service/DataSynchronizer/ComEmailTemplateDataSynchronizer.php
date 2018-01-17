<?php
namespace AdminBundle\Service\DataSynchronizer;

use AdminBundle\Service\MailJet\MailJetTemplate;
use AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator;
use AdminBundle\Manager\ComEmailTemplateManager;

class ComEmailTemplateDataSynchronizer
{
    private $mailjet_template_handler;
    private $template_data_generator;
    private $com_email_template_manager;

    public function __construct(
        MailJetTemplate $mailjet_template_handler,
        TemplateDataGenerator $template_data_generator,
        ComEmailTemplateManager $com_email_template_manager
    ) {
        $this->mailjet_template_handler = $mailjet_template_handler;
        $this->template_data_generator = $template_data_generator;
        $this->com_email_template_manager = $com_email_template_manager;
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
            return true;
        } else {
            return false;
        }
    }
}