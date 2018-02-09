<?php
namespace AdminBundle\Service\JsonResponseData;

class StandardDataProvider
{
    const PAGE_NOT_FOUND_MESSAGE = 'Page non trouvée';
    const FORM_ERROR = 'Données de formulaire non valide';
    const API_COMMUNICATION_ERROR = 'Erreur communication API';

    public function pageNotFound()
    {
        return array('message' => self::PAGE_NOT_FOUND_MESSAGE, 'content' => '');
    }

    public function success($id = false)
    {
        if ($id) {
            return array('message' => '', 'content' => '', "id" => $id);
        } else {
            return array('message' => '', 'content' => '');
        }
    }

    public function formError()
    {
        return array('message' => self::FORM_ERROR, 'content' => '', 'error' => self::FORM_ERROR);
    }

    public function apiCommunicationError()
    {
        return array(
            'message' => self::API_COMMUNICATION_ERROR,
            'content' => '',
            'error' => self::API_COMMUNICATION_ERROR
        );
    }
}
