<?php
namespace AdminBundle\Form\Handler;

/**
 * Class SondageHandler
 * @package AppBundle\Form\Handler
 */
class SondageHandler
{

    protected $formQuestionnaires;
    protected $roleDefault;
    protected $sondagesQuiz;
    protected $request;
    protected $jsonResponseDataProvider;
    protected $em;

    /**
     * SondageHandler constructor.
     * @param $formQuestionnaires
     * @param $roleDefault
     * @param $sondagesQuiz
     * @param $request
     * @param $jsonResponseDataProvider
     * @param $em
     */
    public function __construct($formQuestionnaires, $roleDefault, $sondagesQuiz, $request, $jsonResponseDataProvider, $em)
    {
        $this->formQuestionnaires = $formQuestionnaires;
        $this->roleDefault = $roleDefault;
        $this->sondagesQuiz = $sondagesQuiz;
        $this->request = $request;
        $this->jsonResponseDataProvider = $jsonResponseDataProvider;
        $this->em = $em;
    }

    /**
     * Process Form Sondage created
     * @return JsonResponse
     */
    public function process()
    {
        $sondagesQuizQuestionnaireInfosData = $this->formQuestionnaires->getData();
        if (empty($sondagesQuizQuestionnaireInfosData->getAuthorizedRole())) {
            $sondagesQuizQuestionnaireInfosData->setAuthorizedRole($this->roleDefault[0]);
        }
        $sondagesQuizQuestionnaireInfosData->setSondagesQuiz($this->sondagesQuiz);
        if ($this->request->get("data") == "btn-publier-sondages-quiz") {
            $sondagesQuizQuestionnaireInfosData->setEstPublier(true);
        } else {
            $sondagesQuizQuestionnaireInfosData->setEstPublier(false);
        }

        $this->em->persist($sondagesQuizQuestionnaireInfosData);
        foreach ($sondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $questions) {
            $questions->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfosData);
            $this->em->persist($questions);
            foreach ($questions->getSondagesQuizReponses() as $Reponses) {
                $Reponses->setSondagesQuizQuestions($questions);
            }
        }
        $this->em->flush();
        $data = $this->jsonResponseDataProvider->success();

        return $data;
    }

}