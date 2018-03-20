<?php
namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use AdminBundle\Component\Post\NewsPostAuthorizationType;
use AdminBundle\Exception\NoRelatedProgramException;
use AdminBundle\Manager\ProgramManager;
use Doctrine\ORM\EntityManager;

/**
 * Factoring form field for common data selection
 */
class SelectingCommonDataType extends AbstractType
{
    const AUTHORIZATION_ALL_LABEL = 'TOUS LES PARTICIPANTS';
    const AUTHORIZATION_CUSTOM_LABEL = 'PERSONNALISER';

    protected $program_manager;
    protected $em;

    public function __construct(
        ProgramManager $program_manager,
        EntityManager $em
    ) {
        $this->program_manager = $program_manager;
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('viewer_authorization_type', ChoiceType::class, array(
            'choices' => $this->retrieveAuthorizationList(),
        ))
            ->add('authorized_viewer_role', HiddenType::class)
            ->add('custom_authorized_viewer_list', HiddenType::class);
    }

    /**
     * Retrieve authorization list to be used
     *
     * @return array
     */
    private function retrieveAuthorizationList()
    {
        $authorization_values = array(self::AUTHORIZATION_ALL_LABEL => NewsPostAuthorizationType::ALL);
        $program = $this->program_manager->getCurrent();
        if (empty($program)) {
            throw new NoRelatedProgramException();
        }
        $role_list = $this->em->getRepository('AdminBundle\Entity\Role')->findBy(
            array('program' => $program, 'active' => true)
        );
        if (!empty($role_list)) {
            $i = 0;
            foreach ($role_list as $role) {
                // adding incremented number suffix to prevent generated option tag from losing string value
                // and recognizing this type of authorization type by using regexp (e.g. : role_\d+)
                $authorization_values[$role->getName()] = NewsPostAuthorizationType::BY_ROLE .'_'. $i;
                $i++;
            }
        }

        // TODO - Uncomment this next line when custom list feature development is started
        // $authorization_values[self::AUTHORIZATION_CUSTOM_LABEL] = NewsPostAuthorizationType::CUSTOM_LIST;

        return $authorization_values;
    }
}