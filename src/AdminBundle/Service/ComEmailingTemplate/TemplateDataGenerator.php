<?php
namespace AdminBundle\Service\ComEmailingTemplate;

use AdminBundle\Entity\ComEmailTemplate;
use AdminBundle\Exception\NoComEmailTemplateSetException;
use Twig\Environment as Twig;
use AdminBundle\Component\CommunicationEmail\TemplateModel;
use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use AdminBundle\Component\CommunicationEmail\TemplateContentType;

class TemplateDataGenerator
{
    private $com_email_template;
    private $twig;

    public function __construct(Twig $twig)
    {
        $this->twig = $twig;
    }

    public function setComEmailTemplate(ComEmailTemplate $com_email_template)
    {
        $this->com_email_template = $com_email_template;

        return $this;
    }

    public function retrieveHtml($preview_mode = false)
    {
        $template_content_view = $this->retrieveContentPartHtml($preview_mode, false);

        return $this->twig->render('AdminBundle:EmailTemplates/Communication:template_container.html.twig', array(
            'template_content' => $template_content_view,
        ));
    }

    public function retrieveContentPartHtml($preview_mode = false, $instantaneous_preview_mode = false)
    {
        $rendering_option = array(
            'com_email_template' => $this->com_email_template,
            'template_model_class' => new TemplateModel(),
            'template_logo_alignment_class' => new TemplateLogoAlignment(),
            'content_type_class' => new TemplateContentType(),
        );

        if (true == $instantaneous_preview_mode) {
            $rendering_option['instantaneous_preview_mode'] = true;
        }

        if (true == $preview_mode) {
            $rendering_option['preview_mode'] = true;
        }

        return  $this->twig->render(
            'AdminBundle:EmailTemplates/Communication:template_content.html.twig',
            $rendering_option
        );
    }

    public function retrieveText()
    {
        if (is_null($this->com_email_template)) {
            throw new NoComEmailTemplateSetException();
        }

        $text = '';
        foreach ($this->com_email_template->getContents() as $content) {
            if (TemplateContentType::TEXT == $content->getContentType()) {
                $text .= strip_tags($content->getTextContent());
                if (!empty($content->getTextContent())) {
                    $text .= PHP_EOL;
                }
            }
        }

        return $text;
    }
}