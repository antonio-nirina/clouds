<?php
namespace AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use AdminBundle\Component\SondageQuizConst\ConstanteStatus;
use AdminBundle\Component\GroupAction\GroupActionType;

class SondageQuizManager
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

/**
     * Retrieve data of sondage Quiz
     *
     * @return array
     */
    public function getAllSondageQuiz()
    {
        $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")
            ->findBy([], ["date_creation"=>"DESC"]);
        return $data;
    }

    /**
     * @param string $status
     * @return array
     */
    public function getAllSondageQuizArchived($status = "")
    {
        if (!empty($status)) {
            switch ($status) {
                case ConstanteStatus::CLOTURE:
                    $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")->getStatusClotureArchived();
                    break;
                case ConstanteStatus::PUBLIE:
                    $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")->getStatusPublieArchived();
                    break;
                case ConstanteStatus::ATTENTE:
                    $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")->getStatusAttenteArchived();
                    break;
            }
        } else {
            $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")
                ->findBy(["est_archived"=>true], ["date_creation"=>"DESC"]);
        }

        return $data;
    }

    /**
     * RenderTo state Publised
     *
     * @param $data
     * @param $state
     */
    public function renderToPublished($data, $state)
    {
        $date = new \DateTime('now');
        $data->setDateCreation($date);
        $data->setEstPublier($state);
        $data->setEstCloture(false);
        $data->setEstArchived(false);
        $this->em->persist($data);
        $this->em->flush();
        return;
    }

    /**
     * RenderTo state Archived
     *
     * @param $data
     * @param $archive
     */
    public function renderToArchived($data, $archive)
    {
        $date = new \DateTime('now');
        $data->setDateCreation($date);
        $data->setEstArchived($archive);
        $this->em->persist($data);
        $this->em->flush();
        return;
    }

    /**
     * @param $data
     */
    public function delete($data)
    {
        $this->em->remove($data);
        $this->em->flush();
        return;
    }

    public function renderToCloture($data)
    {
        $date = new \DateTime('now');
        $data->setDateCreation($date);
        $data->setEstCloture(true);
        $data->setEstPublier(false);
        $this->em->persist($data);
        $this->em->flush();
        return;
    }

    /**
     * @param $idList
     * @param $actionType
     */
    public function groupAction($idList, $actionType)
    {
        foreach ($idList as $key => $value) {
            $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")
                ->findOneById($value);
            if (!empty($data)) {
                if (GroupActionType::DELETE == $actionType) {
                    $this->delete($data);
                } elseif (GroupActionType::ARCHIVE == $actionType) {
                    $this->renderToArchived($data, true);
                } elseif (GroupActionType::RESTORE == $actionType) {
                    $this->renderToArchived($data, false);
                }
            }
        }
    
        return;
    }

    /**
     * @param $id
     *
     * @return array
     */
    public function getElementStatistique($id)
    {
        $results =  $this->em->getRepository("AdminBundle\Entity\ResultatsSondagesQuiz")->getResultsQuestions($id);
        if (!empty($results)) {
            $sondageInfos = $results[0]->getSondagesQuizQuestionnaireInfos();
            foreach ($results as $key => $value) {
               $questions[] = $value->getSondagesQuizQuestions();
               $reponses[] = $value->getSondagesQuizReponses();
            }
            foreach ($questions as  $quest) {
                $valId[] = $quest->getId();
            }
            $nbreQuestion = count(array_count_values($valId));
            $nbreReponse = count($reponses);
            
            return [
                "sondageInfos"=>$sondageInfos,
                "questions"=>$questions,
                "reponses"=>$reponses,
                "nbreQuest"=>$nbreQuestion,
                "nbreReponse"=>$nbreReponse];
        } else {
            return "";
        }

       
    }

    /**
     * @param  $data
     * @param $title
     */
    public function duplicate($data,$title)
    {
        $copy = $this->newSondageQuizInfoDuplication($data,$title);
        $this->em->persist($copy);
        $this->em->flush();
        return true;
    }

    /**
     * @param  $obj
     * @param $title
     */
    protected function newSondageQuizInfoDuplication($obj,$title)
    {
        $newSondageQuizInfo = clone $obj; 
        $newSondageQuizInfo->setTitreQuestionnaire($title);
        $newSondageQuizInfo->setEstPublier(false);
        $newSondageQuizInfo->setViewNumber(0);
        return $newSondageQuizInfo;
    }

    
}

