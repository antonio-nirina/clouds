<?php 
namespace AdminBundle\Service\MailJet;


use Mailjet\MailjetBundle\Client\MailjetClient;
use Mailjet\MailjetBundle\Manager\ContactsListManager;
use Mailjet\MailjetBundle\Model\Contact;
use Mailjet\Resources;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class MailjetContactList{
	protected $manager;
    protected $mailjet;
    protected $contact;
	
	public function __construct(ContactsListManager $manager, MailjetClient $mailjet)
    {
        $this->manager = $manager;
        $this->mailjet = $mailjet;
    }
	
	/**
     * Retrieve all ContactsList
     * @return array
     */
	 
    public function getAll()
    {
        $response = $this->mailjet->get(Resources::$Contactslist);
        if (!$response->success()) {
            $this->throwError("ContactsListManager:getAll() failed", $response);
        }

        return $response->getData();
    }
	
	
	public function addContactList($listId, $UsersLists){
		/*
		$properties = array(
			'nom' => $UsersLists->getName(),
			'prénom' => $UsersLists->getFirstname(),
			'status' => 'manager'
		);
		*/
		
		$Contact = new Contact($UsersLists->getEmail());
		
		$DataContactList = $this->manager->create($listId, $Contact);
	}
}
?>