<?php
namespace AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use AdminBundle\Component\SondageQuizConst\ConstanteStatus;


class SondageQuizManager
{
	
	private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }


    /**
     * Retrieve data of sondage Quiz
     * @param string $status
     * @return array
     */
    public function getAllSondageQuiz($status = "")
    {
        if (!empty($status)){
            switch ($status){
                case ConstanteStatus::ARCHIVED :
                    $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")->findOneByEst_archived(["date_creation"=>"desc"]);
                    break;
                case ConstanteStatus::CLOTURE:
                    $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")->findOneByEst_cloture(["date_creation"=>"desc"]);
                    break;
                case ConstanteStatus::PUBLIE:
                    $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")->findOneByEst_publier(["date_creation"=>"desc"]);
                    break;

            }

        } else {
            $data = $this->em->getRepository("AdminBundle\Entity\SondagesQuizQuestionnaireInfos")
                ->findBy([],["date_creation"=>"DESC"]);
        }

        return $data;
    }

    /**
     * @param string $status
     * @return array
     */
    public function getAllSondageQuizArchived($status = "")
    {
        if (!empty($status)){
            switch ($status){
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
                ->findBy(["est_archived"=>true],["date_creation"=>"DESC"]);
        }

        return $data;
    }

    /**
     *RenderTo state Publised
     * @param $data
     * @param $state
     */
    public function renderToPublished($data,$state)
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
     * @param $data
     * @param $form
     */
    public function duplicate($data, $form)
    {

    }

}