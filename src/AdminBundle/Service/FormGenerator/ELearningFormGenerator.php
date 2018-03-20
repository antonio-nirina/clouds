<?php
namespace AdminBundle\Service\FormGenerator;

use AdminBundle\Entity\ELearning;
use AdminBundle\Form\ELearningType;
use AdminBundle\Entity\Program;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

/**
 * Generate form when manipulating e-learning (create/edit) with initial underlying objects (for creation)
 */
class ELearningFormGenerator
{
    private $form_factory;

    public function __construct(FormFactory $form_factory)
    {
        $this->form_factory = $form_factory;
    }

    /**
     * Genearte form for e-learning creation
     *
     * @param Program $program
     * @param string $form_name
     *
     * @return FormInterface
     */
    public function generateForCreation(Program $program, $form_name = 'create_e_learning_form')
    {
        $form = $this->form_factory->createNamed(
            $form_name,
            ELearningType::class,
            $this->initELearningForCreation($program)
        );

        return $form;
    }

    /**
     * Init ELearning
     *
     * @param Program $program
     *
     * @return ELearning
     */
    public function initELearningForCreation(Program $program)
    {
        $eLearning = new ELearning();
        $eLearning->setProgram($program)
            ->setPublishedState(false)
            ->setArchivedState(false)
            ->setViewNumber(0);
        $program->addELearning($eLearning);

        return $eLearning;
    }
}