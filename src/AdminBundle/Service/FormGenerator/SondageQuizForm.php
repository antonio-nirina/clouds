<?php
namespace AdminBundle\Service\FormGenerator;

use AdminBundle\Entity\Program;
use AdminBundle\Form\DuplicationForm;
use AdminBundle\DTO\DuplicationData;
use AdminBundle\Entity\SondagesQuizQuestionnaireInfos;
use Symfony\Component\Form\FormFactory;


/**
* Generate form for duplicate sondage or Quiz
*/
class SondageQuizForm
{
	
	private $formFactory;

    public function __construct(FormFactory $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    public function generateFormDuplicate($data,$name = "duplicateSondageQuiz")
    {
    	$duplication = new DuplicationData();
        $duplication->setName($data->getTitreQuestionnaire())->setDuplicationSourceId($data->getId());;
        $form = $this->formFactory->createNamed(
            $name,
            DuplicationForm::class,
            $duplication
        );

        return $form;
    }

}