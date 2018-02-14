<?php
namespace AdminBundle\Service\JsonResponseData;

use AdminBundle\Service\JsonResponseData\StandardDataProvider;

/**
 * Data provider in Contact List manipulation
 * {@inheritdoc}
 */
class ContactListDataProvider extends StandardDataProvider
{
    const CONTACT_LIST_CREATION_ERROR = 'Erreur lors de la création de la liste de contact';

    /**
     * Give data when contact list creation is OK
     *
     * @param int $id
     * @param string $name
     *
     * @return array
     */
    public function contactListCreationSuccess($id, $name)
    {
        return array('id' => $id, 'name' => $name);
    }

    /**
     * Give data when there is/are error(s) when creating contact list
     *
     * @return array
     */
    public function contactListCreationError()
    {
        return array('error' => self::CONTACT_LIST_CREATION_ERROR);
    }
}