<?php
namespace AdminBundle\Service\MailChimp;

use AdminBundle\Service\MailChimp\MailChimpHandler;

class MailChimpTemplate extends MailChimpHandler
{
    public function getFolders()
    {
        return $this->mailchimp->get('/template-folders');
    }

    public function getTemplates()
    {
        return $this->mailchimp->get('/templates', array(
            'count' => -1,
            'type' => 'user',
        ));
    }

    public function getTemplateContent($template_id)
    {
        return $this->mailchimp->get('/templates/'.$template_id.'/default-content');
    }

    public function createTemplate(array $options = array())
    {
        return $this->mailchimp->post('/templates', $options);
    }

    public function editTemplate($template_id, array $options = array())
    {
        return $this->mailchimp->patch('/templates/'.$template_id, $options);
    }
}