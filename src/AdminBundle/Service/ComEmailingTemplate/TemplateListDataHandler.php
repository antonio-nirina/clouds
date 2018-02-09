<?php
namespace AdminBundle\Service\ComEmailingTemplate;

use AdminBundle\Service\ComEmailingTemplate\TemplateThumbnailGenerator;

class TemplateListDataHandler
{
    private $template_thumbnail_generator;

    public function __construct(TemplateThumbnailGenerator $template_thumbnail_generator)
    {
        $this->template_thumbnail_generator = $template_thumbnail_generator;
    }

    public function retrieveListData(array $template_list)
    {
        $template_data_list = array();
        foreach ($template_list as $template) {
            $this->template_thumbnail_generator->setComEmailTemplate($template);
            array_push($template_data_list, array(
                'template_data' => $template,
                'template_thumbnail_image' => $this->template_thumbnail_generator->retrieveThumbnailFullName(),
            ));
        }

        return $template_data_list;
    }

    /**
     * Retrieve template data list (indexed by template id)
     *
     * @param array $template_list
     *
     * @return array
     */
    public function retrieveListDataIndexedById(array $template_list)
    {
        $template_data_list = array();
        foreach ($template_list as $template) {
            $this->template_thumbnail_generator->setComEmailTemplate($template);
            $template_data_list[$template->getId()] = array(
                'template_data' => $template,
                'template_thumbnail_image' => $this->template_thumbnail_generator->retrieveThumbnailFullName(),
            );
        }

        return $template_data_list;
    }
}