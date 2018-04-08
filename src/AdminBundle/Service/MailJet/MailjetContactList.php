<?php 
namespace AdminBundle\Service\MailJet;


use Mailjet\MailjetBundle\Client\MailjetClient;
use Mailjet\MailjetBundle\Manager\ContactsListManager;
use Mailjet\MailjetBundle\Manager\ContactMetadataManager;
use Mailjet\MailjetBundle\Model\Contact;
use Mailjet\MailjetBundle\Model\ContactsList;
use Mailjet\Resources;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use UserBundle\Entity\User as User;

class MailjetContactList
{
    
    protected $manager;
    protected $mailjet;
    protected $contactmetadata;
    protected $user;
    
    public function __construct(ContactsListManager $manager, MailjetClient $mailjet, ContactMetadataManager $contactmetadata)
    {
        $this->manager = $manager;
        $this->mailjet = $mailjet;
        $this->contactmetadata = $contactmetadata;
    }
    
    public function setUser($users)
    {
        $this->user = $users;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Retrieve all ContactsList
     *
     * @return array
     */
     
    public function getAllList()
    {
        $response = $this->mailjet->get(Resources::$Contactslist, ['filters' => ['Limit' => 0]]);
        if (!$response->success()) {
            $this->throwError("ContactsListManager:getAll() failed", $response);
        }

        return $response->getData();
    }
    
    /**
     * Add ContactsList && contact data
     *
     * @return array
     */
    public function addContactList($listName, $UsersLists)
    {
        
        $ContactInfos = array();
        
        
        //Add list name if is not exist
        $eMessages = array();
        $DataList = $this->createList($listName);
        if(isset($DataList['StatusCode']) && ($DataList['StatusCode'] == 400)) {
            $eMessages['error'] = 'La liste existe déjà!';
        }elseif(isset($DataList[0]['CreatedAt'])) {
            $eMessages['idList'] = $DataList[0]['ID'];
        }
        
        
        $ContactInfos = array();
        if(isset($eMessages['idList']) && $eMessages['idList'] > 0) {
            foreach($UsersLists as $UserConacts){
                $ListRoles = $UserConacts->getRoles();
            
                if($ListRoles[0] == 'ROLE_MANAGER') {
                    $roles = 'manager';
                }elseif($ListRoles[0] == 'ROLE_COMMERCIAL') {
                    $roles = 'commercial';
                }elseif($ListRoles[0] == 'ROLE_PARTICIPANT') {
                    $roles = 'participant';
                }
                
                //Add contact to a list
                $contact = array(
                "Email" =>  $UserConacts->getEmail(),
                "Name" =>  $UserConacts->getName(),
                "Action" =>  Contact::ACTION_ADDFORCE,
                "Properties" =>  array(
                "status" =>  $roles,
                "prénom" =>  $UserConacts->getFirstname(),
                "nom" =>  $UserConacts->getName(),
                "pays" =>  '',
                "newsletter_insc" => '0',
                )
                );
                
                $ObjContacts = new Contact($contact['Email'], $contact['Name'], $contact['Properties']);
                $ContactInfos[] = $this->manager->subscribe($DataList[0]['ID'], $ObjContacts, true);
            }
        }
        
        return $ContactInfos;
    }

    /**
     * Add contact list and contact data, returning contact list and contact information
     *
     * @param $listName
     * @param $UsersLists
     *
     * @return array
     */
    public function addContactListReturningInfos($listName, $UsersLists)
    {
        $eMessages = array();
        $DataList = $this->createList($listName);
        if(isset($DataList['StatusCode']) && ($DataList['StatusCode'] == 400)) {
            $eMessages['error'] = 'La liste existe déjà!';
        }elseif(isset($DataList[0]['CreatedAt'])) {
            $eMessages['idList'] = $DataList[0]['ID'];
        }

           $ContactInfos = array();
        if(isset($eMessages['idList']) && $eMessages['idList'] > 0) {
            foreach($UsersLists as $UserConacts){
                $ListRoles = $UserConacts->getRoles();

                if($ListRoles[0] == 'ROLE_MANAGER') {
                    $roles = 'manager';
                }elseif($ListRoles[0] == 'ROLE_COMMERCIAL') {
                    $roles = 'commercial';
                }elseif($ListRoles[0] == 'ROLE_PARTICIPANT') {
                    $roles = 'participant';
                }

                //Add contact to a list
                $contact = array(
                 "Email" =>  $UserConacts->getEmail(),
                 "Name" =>  $UserConacts->getName(),
                 "Action" =>  Contact::ACTION_ADDFORCE,
                 "Properties" =>  array(
                     "status" =>  $roles,
                     "prénom" =>  $UserConacts->getFirstname(),
                     "nom" =>  $UserConacts->getName(),
                     "pays" =>  '',
                     "newsletter_insc" => '0',
                 )
                );

                $ObjContacts = new Contact($contact['Email'], $contact['Name'], $contact['Properties']);
                $ContactInfos['contact_infos'][] = $this->manager->subscribe($DataList[0]['ID'], $ObjContacts, true);
            }
            $ContactInfos['contact_list_infos'] = $DataList[0];
        }

           return $ContactInfos;
    }
    
    /**
     * Add ContactsList && contact data
     *
     * @return array
     */
    public function editContactList($IdList, $UsersLists)
    {
        
        $ContactInfos = array();
        
        foreach($UsersLists as $UserConacts){
            $ListRoles = $UserConacts->getRoles();
            if($ListRoles[0] != 'ROLE_ADMIN') {
                if($ListRoles[0] == 'ROLE_MANAGER') {
                    $roles = 'manager';
                }elseif($ListRoles[0] == 'ROLE_COMMERCIAL') {
                    $roles = 'commercial';
                }elseif($ListRoles[0] == 'ROLE_PARTICIPANT') {
                    $roles = 'participant';
                }
                
                //Add contact to a list
                $contact = array(
                "Email" =>  $UserConacts->getEmail(),
                "Name" =>  $UserConacts->getName(),
                "Action" =>  Contact::ACTION_ADDFORCE,
                "Properties" =>  array(
                "status" =>  $roles,
                "prénom" =>  $UserConacts->getFirstname(),
                "nom" =>  $UserConacts->getName(),
                "pays" =>  '',
                "newsletter_insc" => '0',
                )
                );
                
                $ObjContacts = new Contact($contact['Email'], $contact['Name'], $contact['Properties']);
                $ContactInfos[] = $this->manager->subscribe($IdList, $ObjContacts, true);
            }
        }
        
        return $ContactInfos;
    }
    
    /**
     * Add new ContactsList
     *
     * @return array
     */
    public function createList($Lname)
    {
        $response = $this->mailjet->post(Resources::$Contactslist, ['body' => ['Name' => $Lname]]);
        return $response->getData();
    }
    
    /**
     * Get All Contacts
     *
     * @return array
     */
    public function getAllContact()
    {
        $response = $this->mailjet->get(Resources::$Contact, ['filters' => ['Limit' => 0]]);
        return $response->getData();
    }
    
    /**
     * Get Contacts By Id
     *
     * @return array
     */
    public function getContactById($idContact)
    {
        $response = $this->mailjet->get(Resources::$Contact, ['id' => $idContact]);
        return $response->getData();
    }
    
    /**
     * Get List with ID
     *
     * @return array
     */
    public function getListById($idList)
    {
        $response = $this->mailjet->get(Resources::$Contactslist, ['id' => $idList]);
        return $response->getData();
    }
    
    /**
     * Get List with Name
     *
     * @return array
     */
    public function getAllContactByName($ListName)
    {
        $response = $this->mailjet->get(Resources::$Listrecipient, ['filters' => ['ListName' => $ListName]]);
        return $response->getData();
    }
    
    /*
    * De-iscrire l'user 
    **/
    public function DesinscritContactList($IdList, $UsersLists)
    {
        $reponses = array();
        foreach($UsersLists as $Email){
            $ObjContacts = new Contact($Email);
            $reponses[] = $this->manager->unsubscribe($IdList, $ObjContacts);
        }
        return $reponses;
    }
    
    /**
     * Delete List with Id
     *
     * @return array
     */
    public function deleteListById($idList)
    {
        //$contact->setAction(Resources::$Contactslist::ACTION_ADDFORCE);
        //$ContactslistObj = new Contactslist($idList, ContactsList::ACTION_REMOVE, array());
        
        $response = $this->mailjet->delete(Resources::$Contactslist, ['id' => $idList]);
        return $response->getData();
    }
    
    /**
     * Create contact with its email
     *
     * @return array
     */
    public function createContactByMail(User $user)
    {
        $response = $this->mailjet->post(Resources::$Contact, ['body' => ['Email' => $user->getEmail()]]);
        return $response->getData();
    }
}
?>