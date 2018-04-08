<?php

namespace AdminBundle\Service\SondageQuiz;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormFactory;
use AdminBundle\Entity\SondagesQuizQuestionnaireInfos;
use AdminBundle\Form\DuplicationForm;

class Common
{
    private $form;

    /**
     * Common constructor.
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;
    }

    public function renderToJson($data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $array[] = ["id"=>$value->getId(),
                    "type"=>$value->getTypeSondagesQuiz(),
                    "titre"=>$value->getTitreQuestionnaire(),
                    "description"=>$value->getDescriptionQuestionnaire(),
                    "date"=>$value->getDateCreation(),
                    "publie"=>$value->getEstPublier(),
                    "archived"=>$value->getEstArchived(),
                    "cloture"=>$value->getEstCloture(),
                    "number"=>$value->getViewNumber(),
                    "roles"=>$value->getAuthorizedRole()
                ];
            }
        } else {
            $array = [];
        }

        $json = new JsonResponse($array);
        $object = $json->getContent($json);

        return $object;
    }

    /**
     * @param $data
     */
    public function generateForm($data)
    {
        $sondageQuiz = new SondagesQuizQuestionnaireInfos();
        $sondageQuiz->setTitreQuestionnaire($data->getTitre());
        $name = "duplicationForm";
        $form = $this->form->createNamed($name, DuplicationForm::class, $sondageQuiz);
        return $form;
    }
}
