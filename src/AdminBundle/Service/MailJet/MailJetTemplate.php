<?php
namespace AdminBundle\Service\MailJet;

use AdminBundle\Service\MailJet\MailJetHandler;
use Mailjet\Resources;

class MailJetTemplate extends MailJetHandler
{
    const OWNER_TYPE = 'apikey';
    const DEFAULT_PURPOSES = array('marketing');

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

    public function createDistantTemplate($name)
    {
        $body = array(
            'Name' => $name,
            'Purposes' => self::DEFAULT_PURPOSES,
        );
        $response = $this->mailjet->post(Resources::$Template, array('body' => $body));
        if (self::STATUS_CODE_CREATED == $response->getStatus()) {
            return $response->getData()[0]['ID'];
        }

        return null;
    }

    public function createNonExistentDistantTemplate($name)
    {
        $existent_distant_template_state = true;
        $template_index = 1;
        $distant_template_id = null;
        $original_name = $name;
        while (true == $existent_distant_template_state) {
            $name = $original_name . ' ' . $template_index;
            $body = array(
                'Name' => $name,
                'Purposes' => self::DEFAULT_PURPOSES,
            );
            $response = $this->mailjet->post(Resources::$Template, array('body' => $body));

            if (self::STATUS_CODE_BAD_REQUEST == $response->getStatus()
                && self::ERROR_CODE_EXISTENT_TEMPLATE == $response->getData()["ErrorCode"]
            ) {
                $template_index++;
                continue;
            } else {
                if (self::STATUS_CODE_CREATED == $response->getStatus()) {
                    $existent_distant_template_state = false;
                    $distant_template_id = $response->getData()[0]['ID'];
                } else {
                    break;
                }
            }
        }

        return $distant_template_id;
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

    public function editDistantTemplateContent($distant_template_id, $html_data, $text_data = '')
    {
        $template_detail_body = array(
            'Html-part' => $html_data,
            'Text-part' => $text_data,
        );
        $template_detail_response = $this->mailjet->post(
            Resources::$TemplateDetailcontent, array(
            'id' => $distant_template_id,
            'body' => $template_detail_body
            )
        );
        if (self::STATUS_CODE_CREATED == $template_detail_response->getStatus()) {
            return $distant_template_id;
        }

        return null;
    }

    public function deleteDistantTemplate($distant_template_id)
    {
        $delete_template_response = $this->mailjet->delete(
            Resources::$Template, array(
            'Id' => $distant_template_id
            )
        );
        if (self::STATUS_CODE_NO_CONTENT == $delete_template_response->getStatus()) {
            return true;
        } elseif (self::STATUS_CODE_NOT_FOUND == $delete_template_response->getStatus()) {
            return false;
        }

        return false;
    }
}