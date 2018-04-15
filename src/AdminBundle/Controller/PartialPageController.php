<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Component\SiteForm\FieldType;

class PartialPageController extends Controller
{
    private $text_type;
    private $choice_type;
    private $single_checkbox_type;

    public function __construct()
    {
        $this->text_type = array(
            FieldType::TEXT,
            FieldType::NUM_TEXT,
            FieldType::ALPHANUM_TEXT,
            FieldType::ALPHA_TEXT,
            FieldType::EMAIL,
            FieldType::PASSWORD,
        );
        $this->choice_type = array(
            FieldType::CHOICE_RADIO,
        );
        $this->date_type = array(
            FieldType::DATE,
        );
        $this->single_checkbox_type = array(
            FieldType::CHECKBOX,
        );
    }

    public function siteFormFieldRowAction($field)
    {
        if (in_array($field->getFieldType(), $this->text_type)) {
            return $this->render(
                'AdminBundle:PartialPage/SiteFormField:text.html.twig',
                array(
                'field' => $field,
                )
            );
        } elseif (in_array($field->getFieldType(), $this->choice_type)) {
            $choices = (!empty($field->getAdditionalData()) && array_key_exists('choices', $field->getAdditionalData()))
                ? $field->getAdditionalData()["choices"]
                : array();
            return $this->render(
                'AdminBundle:PartialPage/SiteFormField:radio.html.twig',
                array(
                'field' => $field,
                'choices' => $choices,
                )
            );
        } elseif (in_array($field->getFieldType(), $this->date_type)) {
            $template = 'AdminBundle:PartialPage/SiteFormField:date.html.twig';
            return $this->render(
                $template,
                array(
                'field' => $field,
                )
            );
        } elseif (in_array($field->getFieldType(), $this->single_checkbox_type)) {
            return $this->render(
                'AdminBundle:PartialPage/SiteFormField:checkbox.html.twig',
                array(
                'field' => $field,
                )
            );
        }

        return new Response('');
    }


    public function siteFormFieldRow2Action($field, $personalize = false)
    {
        if (in_array($field->getFieldType(), $this->text_type)) {
            return $this->render(
                'AdminBundle:PartialPage/SiteFormField:text2.html.twig',
                array(
                'field' => $field,
                'personalize' => $personalize
                )
            );
        } elseif (in_array($field->getFieldType(), $this->choice_type)) {
            $choices = (!empty($field->getAdditionalData()) && array_key_exists('choices', $field->getAdditionalData()))
                ? $field->getAdditionalData()["choices"]
                : array();
            return $this->render(
                'AdminBundle:PartialPage/SiteFormField:radio2.html.twig',
                array(
                'field' => $field,
                'choices' => $choices,
                'personalize' => $personalize
                )
            );
        } elseif (in_array($field->getFieldType(), $this->date_type)) {
            $template = 'AdminBundle:PartialPage/SiteFormField:date2.html.twig';
            return $this->render(
                $template,
                array(
                'field' => $field,
                'personalize' => $personalize
                )
            );
        }

        return new Response('');
    }

    /**
     * @param $field
     * @param bool $personalize
     * @return Response
     */
    public function siteFormManyFieldsRowAction($field, $personalize = false)
    {
        // dump($level);die;
        $em = $this->getDoctrine()->getManager();
        $row = $field->getInRow();
        $formSetting = $field->getSiteFormSetting();
        $allFieldsRow = $em->getRepository("AdminBundle:SiteFormFieldSetting")->findAllInRow(
            $row,
            $formSetting->getId(),
            $field->getLevel()
        );

        return $this->render(
            'AdminBundle:PartialPage/SiteFormField:row.html.twig',
            array(
            'field' => $field,
            'all_fields' => $allFieldsRow,
            'personalize' => $personalize
            )
        );
    }

    /**
     * @param array $datas
     * @return Response
     */
    public function afficheContenuPagesStandardAction(array $datas)
    {
        if (isset($datas['page']) && !empty($datas['page'])) {
            //On recupere les infos de la page parametrés
            $em = $this->getDoctrine()->getManager();

            $pagesSettings = $em->getRepository("AdminBundle:SitePagesStandardSetting")->find($datas['page']);

            //On recupere les pages par defaut
            $pagesDefault = $em->getRepository('AdminBundle:SitePagesStandardDefault')->find($datas['page']);

            $page = array();
            if (count($pagesSettings) > 0) {
                $page = $pagesSettings;
            } else {
                $page = $pagesDefault;
            }
        }

        $newPage = "";
        if (isset($datas['new_page']) && !empty($datas['new_page'])) {
            $newPage = $datas['new_page'];
        }

        return $this->render(
            'AdminBundle:PartialPage/Ajax:afficheContenuPagesStandard.html.twig',
            array(
            'Page' => $page,
            'NewPage' => $newPage,
            'idpagehtml' => $datas['page']
            )
        );
    }


    /**
     * @param array $datas
     * @param $programm
     * @return Response
     */
    public function affichePopUpImgEditorAction(array $datas, $programm)
    {
        //On recupere les infos de la page parametrés
        $em = $this->getDoctrine()->getManager();

        return $this->render(
            'AdminBundle:PartialPage/Ajax:affichePopUpImgEditor.html.twig',
            array(
            'datas' => $datas,
            'programm' => $programm
            )
        );
    }

    public function afficheListImgEditorAction(array $datas)
    {
        return $this->render('AdminBundle:PartialPage/Ajax:afficheListImgEditor.html.twig', array('images' => $datas));
    }

    public function emailingListeContactEditAjaxAction($IdList)
    {
        //Get all user
        $rôle = array('ROLE_PARTICIPANT', 'ROLE_COMMERCIAL', 'ROLE_MANAGER');
        $em = $this->getDoctrine()->getManager();
        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
        $allContact = $contactList->getAllContact();
        $Users = array();
        foreach ($allContact as $contacts) {
            //Get infos user
            $Users[] = $em->getRepository('UserBundle\Entity\User')->findUserByMail($contacts['Email']);
        }

        //Get ListInfos
        $listinfos = $contactList->getListById($IdList);

        //Get All contact by List
        $listContactSub = array();
        $listUserUnsubscribed = array();
        $listContact = $contactList->getAllContactByName($listinfos[0]['Name']);
        foreach ($listContact as $contactId) {
            $contactSub = $contactList->getContactById($contactId['ContactID']);
            if ($contactId['IsUnsubscribed'] == '1') {
                $listUserUnsubscribed[] = $contactSub[0]['Email'];
            } else {
                $listContactSub[] = $contactSub[0]['Email'];
            }
        }

        return $this->render(
            'AdminBundle:PartialPage/Ajax:emailing_liste_contact_edit.html.twig',
            array(
            'Users' => $Users,
            'Listinfos' => $listinfos,
            'ListContactSub' => $listContactSub,
            'ListUserUnsubscribed' => $listUserUnsubscribed
            )
        );
    }


    public function emailingListeContactEditSubmitAjaxAction($IdList, $UserId)
    {

        $em = $this->getDoctrine()->getManager();

        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

        //Infos User
        $explodeUserId = explode('##_##', $UserId);
        $Reponses = array();
        $listUserSubscribing = array();
        $listUserSubscribingData = array();
        foreach ($explodeUserId as $idUser) {
            $usersListes = $em->getRepository('UserBundle\Entity\User')->find($idUser);
            $listUserSubscribing[] = $usersListes->getEmail();
            $listUserSubscribingData[$usersListes->getEmail()] = $usersListes;
        }

        /*
        * Les Users qui sont déjà inscrits sur la liste des contacts
        **/
        $reponsesList = $contactList->getListById($IdList);
        $reponsesContacts = $contactList->getAllContactByName($reponsesList[0]['Name']);
        $listUserDejaSub = array();
        $listUserDejaSubdata = array();
        foreach ($reponsesContacts as $contactsId) {
            $reponsesContactsData = $contactList->getContactById($contactsId['ContactID']);
            $listUserDejaSubdata[$reponsesContactsData[0]['Email']] = $reponsesContactsData;
            $listUserDejaSub[] = $reponsesContactsData[0]['Email'];
        }

        /*
        * Separer les users déjà inscrits et non insscrits
        */
        $listUserDejaInscritsListConact = array();
        $listUserNonInscritsListConact = array();
        foreach ($listUserSubscribing as $emailsNew) {
            //Déjà inscrits
            if (in_array($emailsNew, $listUserDejaSub)) {
                $listUserDejaInscritsListConact[] = $emailsNew;
            } else {//Pas encore inscrits
                $listUserNonInscritsListConact[] = $emailsNew;
            }
        }

        //Add contactList
        $usersNonLists = array();
        if (count($listUserNonInscritsListConact) > 0) {
            foreach ($listUserNonInscritsListConact as $emailNonInscrits) {
                $usersNonLists[] = $listUserSubscribingData[$emailNonInscrits];
            }
            $ReponsesListInscriptions = $contactList->editContactList($IdList, $usersNonLists);
        }

        /*
        * Isoler les users à enlever de la liste des contacts
        **/
        $listUserAEnleverDeLaListe = array();
        if (count($listUserDejaSub) > 0) {
            foreach ($listUserDejaSub as $emailDejaInscrits) {
                //S'il est encore dans la liste des users à inscrire
                if (!in_array($emailDejaInscrits, $listUserSubscribing)) {
                    $listUserAEnleverDeLaListe[] = $emailDejaInscrits;
                }
            }
        }

        //Enlever de la liste des contacts
        $listUserAEnleverDeLaListeData = array();
        if (count($listUserAEnleverDeLaListe) > 0) {
            foreach ($listUserAEnleverDeLaListe as $emailEnlevers) {
                $listUserAEnleverDeLaListeData[] = $emailEnlevers;
            }
            $ReponsesListDesinscriptions = $contactList->DesinscritContactList($IdList, $listUserAEnleverDeLaListeData);
        }

        return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_creer_submit.html.twig');
    }

    public function emailingListeContactCreerAjaxAction()
    {
        //Get all user
        $rôle = array('ROLE_PARTICIPANT', 'ROLE_COMMERCIAL', 'ROLE_MANAGER');
        $em = $this->getDoctrine()->getManager();

        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
        $allContact = $contactList->getAllContact();

        $users = array();
        foreach ($allContact as $Contacts) {
            //Get infos user
            $Users[] = $em->getRepository('UserBundle\Entity\User')->findUserByMail($Contacts['Email']);
        }

        return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_creer.html.twig', array('Users' => $Users));
    }

    public function emailingListeContactCreerSubmitAjaxAction($ListName, $UserId)
    {

        $em = $this->getDoctrine()->getManager();

        //Infos User
        $explodeUserId = explode('##_##', $UserId);
        $usersLists = array();
        foreach ($explodeUserId as $IdUser) {
            $usersLists[] = $em->getRepository('UserBundle\Entity\User')->find($IdUser);
        }

        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

        //Add contactList
        $contactList->addContactList($ListName, $usersLists);

        return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_creer_submit.html.twig');
    }

    public function emailingListeContactDeleteAjaxAction($IdList)
    {

        $em = $this->getDoctrine()->getManager();

        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

        //Add contactList
        $contactList->deleteListById($IdList);

        return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_delete.html.twig');
    }

    public function emailingListeContactDupliquerAjaxAction($ListName, $ListId)
    {

        $em = $this->getDoctrine()->getManager();

        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

        //Verifie list name
        $reponsesListName = $contactList->getAllContactByName($ListName);
        if (count($reponsesListName) == 0) {
            //Création du nouveaux liste
            $reponsesCreateList = $contactList->createList($ListName);

            //Recuperations des contacts de la liste dupliquer
            $listInfos = $contactList->getListById($ListId);
            $listContactsInfos = $contactList->getAllContactByName($listInfos[0]['Name']);
            $userListesId = array();
            if (count($listContactsInfos) > 0) {
                foreach ($listContactsInfos as $ContactsInfos) {
                    $ListContactsDatas = $contactList->getContactById($ContactsInfos['ContactID']);
                    $usersListes = $em->getRepository('UserBundle\Entity\User')->findUserByMail($ListContactsDatas[0]['Email']);
                    $userListesId[] = $usersListes[0];
                }
                $ReponsesListInscriptions = $contactList->editContactList($reponsesCreateList[0]['ID'], $userListesId);
            }
        }

        return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_dupliquer.html.twig');
    }
}
