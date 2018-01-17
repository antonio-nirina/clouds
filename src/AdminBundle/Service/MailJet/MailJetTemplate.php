<?php
namespace AdminBundle\Service\MailJet;

use AdminBundle\Service\MailJet\MailJetHandler;
use Mailjet\Resources;

class MailJetTemplate extends MailJetHandler
{
    const OWNER_TYPE = 'apikey';

    public function retrieveTemplates($limit = 0)
    {
        $filters = array(
            'Limit' => $limit,
            'OwnerType' => self::OWNER_TYPE,
        );
        $response = $this->mailjet->get(Resources::$Template, array('filters' => $filters));

        return array(
            'status' => $response->getStatus(),
            'data' => $response->getData(),
        );
    }

    public function createTemplate($name, $html_data, $text_data = '')
    {
        $body = array(
            'Name' => $name,
        );
        $response = $this->mailjet->post(Resources::$Template, array('body' => $body));
        if (self::STATUS_CODE_CREATED == $response->getStatus()) {
            $template_id = $response->getData()[0]['ID'];
            return $this->editDistantTemplateContent($template_id, $html_data, $text_data);
        }

        return null;
    }

    public function editTemplate($distant_template_id, $html_data, $text_data = '')
    {
        return $this->editDistantTemplateContent($distant_template_id, $html_data, $text_data);
    }

    private function editDistantTemplateContent($distant_template_id, $html_data, $text_data = '')
    {
        $template_detail_body = array(
            'Html-part' => $html_data,
            'Text-part' => $text_data,
        );
        $template_detail_response = $this->mailjet->post(Resources::$TemplateDetailcontent, array(
            'id' => $distant_template_id,
            'body' => $template_detail_body
        ));
        if (self::STATUS_CODE_CREATED == $template_detail_response->getStatus()) {
            return $distant_template_id;
        }

        return null;
    }
}