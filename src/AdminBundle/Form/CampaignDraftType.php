<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AdminBundle\Service\MailJet\MailjetContactList;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AdminBundle\DTO\CampaignDraftData;
use Doctrine\ORM\EntityManager;
use AdminBundle\Manager\ProgramManager;
use AdminBundle\Exception\NoRelatedProgramException;
use AdminBundle\Manager\ComEmailTemplateManager;
use AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler;

/**
 * Form type for manipulating campaign draft (e.g.: create campaign draft)
 */
class CampaignDraftType extends AbstractType
{
    private $contact_list_handler;
    private $em;
    private $program_manager;
    private $template_manager;
    private $template_list_data_handler;

    /**
     * CampaignDraftType constructor
     *
     * @param MailjetContactList $contact_list_handler
     * @param EntityManager $em
     * @param ProgramManager $program_manager
     * @param ComEmailTemplateManager $template_manager
     * @param TemplateListDataHandler $template_list_data_handler
     */
    public function __construct(
        MailjetContactList $contact_list_handler,
        EntityManager $em,
        ProgramManager $program_manager,
        ComEmailTemplateManager $template_manager,
        TemplateListDataHandler $template_list_data_handler
    ) {
        $this->contact_list_handler = $contact_list_handler;
        $this->em = $em;
        $this->program_manager = $program_manager;
        $this->template_manager = $template_manager;
        $this->template_list_data_handler = $template_list_data_handler;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class)
            ->add('object', TextType::class)
            ->add('list_id', ChoiceType::class, array(
                'choices' => $this->retrieveListList(),
                'expanded' => false,
                'multiple' => false,
            ))
            ->add('template_id', ChoiceType::class, array(
                'choices' => $this->retrieveTemplateList(),
                'expanded' => true,
                'multiple' => false,
            ))
            ->add('programmed_state', ChoiceType::class, array(
                'choices' => array(
                    'false' => 'false',
                    'true' => 'true',
                ),
                'expanded' => true,
                'multiple' => false,
            ));
    }

    /**
     * Retrieve a list of (contact) list
     *
     * @return array
     */
    private function retrieveListList()
    {
        $contact_list = $this->contact_list_handler->getAllList();
        $arranged_contact_list = array();
        foreach ($contact_list as $contact) {
            $arranged_contact_list[$contact['Name']] = $contact['ID'];
        }

        return $arranged_contact_list;
    }

    /**
     * Retrieve templates list (templates related to current program)
     *
     * @return array
     */
    private function retrieveTemplateList()
    {
        $program = $this->program_manager->getCurrent();
        if (empty($program)) {
            throw new NoRelatedProgramException();
        }
        $template_list = $this->template_manager->listSortedTemplate($program);
        $template_data_list = $this->template_list_data_handler->retrieveListData($template_list);
        $arranged_template_data_list = array();
        foreach ($template_data_list as $template_data) {
            $arranged_template_data_list[$template_data['template_data']->getId()]
                = $template_data['template_data']->getDistantTemplateId();
        }

        return $arranged_template_data_list;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CampaignDraftData::class,
        ));
    }
}