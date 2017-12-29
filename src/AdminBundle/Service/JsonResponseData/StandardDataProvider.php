<?php
namespace AdminBundle\Service\JsonResponseData;

class StandardDataProvider
{
    const PAGE_NOT_FOUND_MESSAGE = 'Page non trouvée';
    const FORM_ERROR = 'Données de formulaire non valide';

    public function pageNotFound()
    {
        return array('message' => self::PAGE_NOT_FOUND_MESSAGE, 'content' => '');
    }

    public function success()
    {
        return array('message' => '', 'content' => '');
    }

    public function formError()
    {
        return array('message' => self::FORM_ERROR, 'content' => '', 'error' => self::FORM_ERROR);
    }
}