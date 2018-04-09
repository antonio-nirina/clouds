<?php
namespace AdminBundle\Service\ComEmailingTemplate;

use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use AdminBundle\Entity\ComEmailTemplate;
use AdminBundle\Entity\ComEmailTemplateContent;
use AdminBundle\Component\CommunicationEmail\TemplateContentType;

class TemplateDataInitializer
{
    public function initForNewTemplate()
    {
        $template = new ComEmailTemplate();
        $template->setLogoAlignment(TemplateLogoAlignment::CENTER)
            ->setEmailColor('#ffffff')
            ->setBackgroundColor('#f5f5f5')
            ->setFooterCompanyInfo('copyright / société')
            ->setFooterContactInfo('contact@masociete.com')
            ->setFooterUnsubscribingText('me désabonner');

        $content_img = new ComEmailTemplateContent();
        $content_img->setContentType(TemplateContentType::IMAGE)
            ->setTemplate($template)
            ->setContentOrder(1);
        $template->addContent($content_img);

        $content_text = new ComEmailTemplateContent();
        $content_text->setContentType(TemplateContentType::TEXT)
            ->setTemplate($template)
            ->setContentOrder(2);
        $template->addContent($content_text);

        $content_button = new ComEmailTemplateContent();
        $content_button->setActionButtonText("mon bouton d'action")
            ->setActionButtonBackgroundColor('#ff0000')
            ->setActionButtonTextColor('#ffffff')
            ->setTemplate($template)
            ->setContentOrder(3)
            ->setContentType(TemplateContentType::BUTTON);
        $template->addContent($content_button);

        return $template;
    }
}
