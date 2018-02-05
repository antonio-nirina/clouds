<?php
namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Component\SiteForm\FieldType;
use Mailjet\MailjetBundle\Model\Contact;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
            return $this->render('AdminBundle:PartialPage/SiteFormField:text.html.twig', array(
                'field' => $field,
            ));
        } elseif (in_array($field->getFieldType(), $this->choice_type)) {
            $choices = (!empty($field->getAdditionalData()) && array_key_exists('choices', $field->getAdditionalData()))
                ? $field->getAdditionalData()["choices"]
                : array();
            return $this->render('AdminBundle:PartialPage/SiteFormField:radio.html.twig', array(
                'field' => $field,
                'choices' => $choices,
            ));
        } elseif (in_array($field->getFieldType(), $this->date_type)) {
            $template = 'AdminBundle:PartialPage/SiteFormField:date.html.twig';
            return $this->render($template, array(
                'field' => $field,
            ));
        } elseif (in_array($field->getFieldType(), $this->single_checkbox_type)) {
            return $this->render('AdminBundle:PartialPage/SiteFormField:checkbox.html.twig', array(
                'field' => $field,
            ));
        }

        return new Response('');
    }

    
    public function siteFormFieldRow2Action($field, $personalize = false)
    {
        if (in_array($field->getFieldType(), $this->text_type)) {
            return $this->render('AdminBundle:PartialPage/SiteFormField:text2.html.twig', array(
                'field' => $field,
                'personalize' => $personalize
            ));
        } elseif (in_array($field->getFieldType(), $this->choice_type)) {
            $choices = (!empty($field->getAdditionalData()) && array_key_exists('choices', $field->getAdditionalData()))
                ? $field->getAdditionalData()["choices"]
                : array();
            return $this->render('AdminBundle:PartialPage/SiteFormField:radio2.html.twig', array(
                'field' => $field,
                'choices' => $choices,
                'personalize' => $personalize
            ));
        } elseif (in_array($field->getFieldType(), $this->date_type)) {
            $template = 'AdminBundle:PartialPage/SiteFormField:date2.html.twig';
            return $this->render($template, array(
                'field' => $field,
                'personalize' => $personalize
            ));
        }

        return new Response('');
    }

    public function siteFormManyFieldsRowAction($field, $personalize = false)
    {
        // dump($level);die;
        $em = $this->getDoctrine()->getManager();
        $row = $field->getInRow();
        $form_setting = $field->getSiteFormSetting();
        $all_fields_row = $em->getRepository("AdminBundle:SiteFormFieldSetting")->findAllInRow(
            $row,
            $form_setting->getId(),
            $field->getLevel()
        );

        return $this->render('AdminBundle:PartialPage/SiteFormField:row.html.twig', array(
            'field' => $field,
            'all_fields' => $all_fields_row,
            'personalize' => $personalize
        ));
    }
	
	public function afficheContenuPagesStandardAction(array $datas){
		if(isset($datas['page']) && !empty($datas['page'])){
			
			//On recupere les infos de la page parametrés
			$em = $this->getDoctrine()->getManager();
			
			$PagesSettings = array();
			$PagesSettings = $em->getRepository("AdminBundle:SitePagesStandardSetting")->find($datas['page']);
			
			//On recupere les pages par defaut
			$PagesDefault = array();
			$PagesDefault = $em->getRepository('AdminBundle:SitePagesStandardDefault')->find($datas['page']);
			
			$Page = array();
			if(count($PagesSettings) > 0){
				$Page = $PagesSettings;
			}else{
				$Page = $PagesDefault;
			}
		}
		
		$NewPage = "";
		if(isset($datas['new_page']) && !empty($datas['new_page'])){
			$NewPage = $datas['new_page'];
		}
		
		
		return $this->render('AdminBundle:PartialPage/Ajax:afficheContenuPagesStandard.html.twig', array(
			'Page' => $Page,
			'NewPage' => $NewPage,
			'idpagehtml' => $datas['page']
		));
	}
	
	public function affichePopUpImgEditorAction(array $datas, $programm){
		//On recupere les infos de la page parametrés
		$em = $this->getDoctrine()->getManager();
		
		return $this->render('AdminBundle:PartialPage/Ajax:affichePopUpImgEditor.html.twig', array(
			'datas' => $datas,
			'programm' => $programm
		));
	}
	
	public function afficheListImgEditorAction(array $datas){
		return $this->render('AdminBundle:PartialPage/Ajax:afficheListImgEditor.html.twig', array('images' => $datas));
	}
	
	public function emailingListeContactEditAjaxAction($IdList){
		//Get all user 
		$rôle = array('ROLE_PARTICIPANT', 'ROLE_COMMERCIAL', 'ROLE_MANAGER');
		$em = $this->getDoctrine()->getManager();
        //Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		$AllContact = $ContactList->getAllContact();
		$Users = array();
		foreach($AllContact as $Contacts){
			//Get infos user 
			$Users[] = $em->getRepository('UserBundle\Entity\User')->findUserByMail($Contacts['Email']);
		}
		
		//Get ListInfos
		$Listinfos = $ContactList->getListById($IdList);
		
		//Get All contact by List
		$ListContactSub = array();
		$ListUserUnsubscribed = array();
		$ListContact = $ContactList->getAllContactByName($Listinfos[0]['Name']);
		foreach($ListContact as $ContactId){
			$ContactSub = $ContactList->getContactById($ContactId['ContactID']);
			if($ContactId['IsUnsubscribed'] == '1'){
				$ListUserUnsubscribed[] = $ContactSub[0]['Email'];
			}else{
				$ListContactSub[] = $ContactSub[0]['Email'];
			}
		}
		
		
		return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_edit.html.twig', array(
			'Users' => $Users, 
			'Listinfos' => $Listinfos, 
			'ListContactSub' => $ListContactSub,
			'ListUserUnsubscribed' => $ListUserUnsubscribed
		));
	}
	
	public function emailingListeContactEditSubmitAjaxAction($IdList, $UserId){ 

		$em = $this->getDoctrine()->getManager();
		
		//Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		
		//Infos User 
		$ExplodeUserId = explode('##_##', $UserId);
		$Reponses = array();
		$ListUserSubscribing = array();
		$ListUserSubscribingData = array();
		foreach($ExplodeUserId as $IdUser){
			$UsersListes = $em->getRepository('UserBundle\Entity\User')->find($IdUser);
			$ListUserSubscribing[] = $UsersListes->getEmail();
			$ListUserSubscribingData[$UsersListes->getEmail()] = $UsersListes;
		}
		
		
		/*
		 * Les Users qui sont déjà inscrits sur la liste des contacts
		 **/
		$ReponsesList = $ContactList->getListById($IdList);
		$ReponsesContacts = $ContactList->getAllContactByName($ReponsesList[0]['Name']);
		$ListUserDejaSub = array();
		$ListUserDejaSubdata = array();
		foreach($ReponsesContacts as $ContactsId){
			$ReponsesContactsData = $ContactList->getContactById($ContactsId['ContactID']);
			$ListUserDejaSubdata[$ReponsesContactsData[0]['Email']] = $ReponsesContactsData;
			$ListUserDejaSub[] = $ReponsesContactsData[0]['Email'];
		}
		
		
		/*
		 * Separer les users déjà inscrits et non insscrits
		 */
		$ListUserDejaInscritsListConact = array();
		$ListUserNonInscritsListConact = array();
		foreach($ListUserSubscribing as $EmailsNew){
			//Déjà inscrits
			if(in_array($EmailsNew, $ListUserDejaSub)){
				$ListUserDejaInscritsListConact[] = $EmailsNew;
			}else{//Pas encore inscrits
				$ListUserNonInscritsListConact[] = $EmailsNew;
			}
		}
		
		//Add contactList
		$UsersNonLists = array();
		if(count($ListUserNonInscritsListConact) > 0){
			foreach($ListUserNonInscritsListConact as $EmailNonInscrits){
				$UsersNonLists[] = $ListUserSubscribingData[$EmailNonInscrits];
			}
			$ReponsesListInscriptions = $ContactList->editContactList($IdList, $UsersNonLists);
		}
		
		/*
		 * Isoler les users à enlever de la liste des contacts 
		 **/
		$ListUserAEnleverDeLaListe = array();
		if(count($ListUserDejaSub) > 0){
			foreach($ListUserDejaSub as $EmailDejaInscrits){
				//S'il est encore dans la liste des users à inscrire
				if(!in_array($EmailDejaInscrits, $ListUserSubscribing)){
					$ListUserAEnleverDeLaListe[] = $EmailDejaInscrits;
				}
			}
		}
		
		//Enlever de la liste des contacts
		$ListUserAEnleverDeLaListeData = array();
		if(count($ListUserAEnleverDeLaListe) > 0){
			foreach($ListUserAEnleverDeLaListe as $EmailEnlevers){
				$ListUserAEnleverDeLaListeData[] = $EmailEnlevers;
			}
			$ReponsesListDesinscriptions = $ContactList->DesinscritContactList($IdList, $ListUserAEnleverDeLaListeData);
		}
		
		return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_creer_submit.html.twig');
	}
	
	public function emailingListeContactCreerAjaxAction(){
		//Get all user 
		$rôle = array('ROLE_PARTICIPANT', 'ROLE_COMMERCIAL', 'ROLE_MANAGER');
		$em = $this->getDoctrine()->getManager();
		
		//Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		$AllContact = $ContactList->getAllContact();
		$Users = array();
		foreach($AllContact as $Contacts){
			//Get infos user 
			$Users[] = $em->getRepository('UserBundle\Entity\User')->findUserByMail($Contacts['Email']);
		}
        
		return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_creer.html.twig', array('Users' => $Users));
	}
	
	public function emailingListeContactCreerSubmitAjaxAction($ListName, $UserId){ 

		$em = $this->getDoctrine()->getManager();
		
		//Infos User 
		$ExplodeUserId = explode('##_##', $UserId);
		$UsersLists = array();
		$Reponses = array();
		foreach($ExplodeUserId as $IdUser){
			$UsersLists[] = $em->getRepository('UserBundle\Entity\User')->find($IdUser);
		}
		
		//Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		
		//Add contactList
		$Reponses = $ContactList->addContactList($ListName, $UsersLists);
		
		return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_creer_submit.html.twig');
	}
	
	public function emailingListeContactDeleteAjaxAction($IdList){ 

		$em = $this->getDoctrine()->getManager();
		
		//Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		
		//Add contactList
		$Reponses = $ContactList->deleteListById($IdList);
		
		return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_delete.html.twig');
	}
	
	public function emailingListeContactDupliquerAjaxAction($ListName, $ListId){ 

		$em = $this->getDoctrine()->getManager();
		
		//Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		
		//Verifie list name
		$ReponsesListName = $ContactList->getAllContactByName($ListName);
		if(count($ReponsesListName) == 0){
			//Création du nouveaux liste
			$ReponsesCreateList = $ContactList->createList($ListName);
			
			//Recuperations des contacts de la liste dupliquer
			$ListInfos = $ContactList->getListById($ListId);
			$ListContactsInfos = $ContactList->getAllContactByName($ListInfos[0]['Name']);
			$UsersListes = array();
			$UserListesId = array();
			if(count($ListContactsInfos) > 0){
				foreach($ListContactsInfos as $ContactsInfos){
					$ListContactsDatas = $ContactList->getContactById($ContactsInfos['ContactID']);
					$UsersListes = $em->getRepository('UserBundle\Entity\User')->findUserByMail($ListContactsDatas[0]['Email']);
					$UserListesId[] = $UsersListes[0];
				}
				$ReponsesListInscriptions = $ContactList->editContactList($ReponsesCreateList[0]['ID'], $UserListesId);
			}
		}
		
		return $this->render('AdminBundle:PartialPage/Ajax:emailing_liste_contact_dupliquer.html.twig');
	}
}
