<?php
namespace BeneficiaryBundle\Controller;

use AdminBundle\Component\Post\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use AdminBundle\Entity\HomePageSlide;
use AdminBundle\Entity\SondagesQuiz;
use AdminBundle\Entity\SondagesQuizQuestionnaireInfos;
use AdminBundle\Entity\SondagesQuizQuestions;
use AdminBundle\Entity\SondagesQuizReponses;
use AdminBundle\Entity\ResultatsSondagesQuiz;

class PageController extends Controller
{
    /**
     * @Route("/", name="beneficiary_home")
     */
    public function homePageAction(Request $request)
    {
        $auth = $this->get('security.authorization_checker');
        if (false === $auth->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return  $this->redirectToRoute("fos_user_security_login");
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }

        $background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();
        $ordered_slide_list = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findByHomePageDataOrdered($home_page_data);

        $parameter_edito = $em->getRepository('AdminBundle\Entity\HomePagePost')->findOneBy(
            array('program' => $program, 'post_type' => PostType::PARAMETER_EDITO)
        );

        $home_page_post_list = $em->getRepository('AdminBundle\Entity\HomePagePost')
            ->findPublishedNewsPost($program);
        if (!is_null($parameter_edito)) {
            array_push($home_page_post_list, $parameter_edito);
        }
        $post_ordering = $this->get('AdminBundle\Service\DataOrdering\PostOrdering');
        $home_page_post_list = $post_ordering->orderByDateDesc($home_page_post_list);

        return $this->render(
            'BeneficiaryBundle:Page:home.html.twig',
            array(
            'has_network' => $has_network,
            'table_network' => $table_network,
            'slide_list' => $ordered_slide_list,
            'background_link' => $background_link,
            'home_page_post_list' => $home_page_post_list,
            'home_page_post_type_class' => new PostType(),
            )
        );
    }

    /**
     * @Route("/beneficiary-home/video/lecture", name="beneficiary_home_video_lecture")
     */
    public function LectureVideoAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $IdVideo = $request->get('video_id');
            $VideoSlide = $em->getRepository("AdminBundle:HomePageSlide")->find($IdVideo);
            $response = $this->forward('BeneficiaryBundle:PartialPage:afficheLecteurVideo', array('videos' => $VideoSlide, 'programm' => $program));
            return new Response($response->getContent());
        } else {
            return new Response('');
        }
    }


    public function PageStandardFooterAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $PageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);

        $ListePages = array();
        foreach ($PageStandard as $Pages) {
            if ($Pages->getStatusPage() == '1') {
                if ($Pages->getNomPage() == 'mentions légales' || $Pages->getNomPage() == 'règlement' || $Pages->getNomPage() == 'contact') {
                    $ListePages[] = $Pages;
                }
            }
        }

        return $this->render(
            'BeneficiaryBundle::page_footer.html.twig',
            array(
            'ListePages' => $ListePages
            )
        );
    }

    public function PageStandardTopAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        //Pages standards
        $em = $this->getDoctrine()->getManager();
        $PageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);
        $ListePages = array();
        foreach ($PageStandard as $Pages) {
            if ($Pages->getStatusPage() == '1') {
                if ($Pages->getNomPage() != 'mentions légales' && $Pages->getNomPage() != 'règlement' && $Pages->getNomPage() != 'contact') {
                    $ListePages[] = $Pages;
                }
            }
        }
        //Sondages/Quiz
        $SondagesQuiz = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        $IsSondagesQuiz = false;
        $ObjSondagesQuiz = array();
        if (isset($SondagesQuiz[0])) {
            $ObjSondagesQuiz = $SondagesQuiz[0];
            $IsSondagesQuiz = true;
        }
        //show/E-learning
        $elearning = $em->getRepository('AdminBundle\Entity\ELearningHomeBanner')->findOneBy(array('program' => $program));
        $IsELearning  = false;
        $ObjElearning = array();
        if (isset($elearning)) {
            $ObjElearning = $elearning;
            $IsELearning = true;
        }
        return $this->render(
            'BeneficiaryBundle::menu_top_niv_2.html.twig',
            array(
            'ListePages' => $ListePages,
            'IsSondagesQuiz' => $IsSondagesQuiz,
            'ObjSondagesQuiz' => $ObjSondagesQuiz,
            'IsElearning' => $IsELearning,
            'ObjElearning' => $ObjElearning,
            )
        );
    }

    public function PageStandardContactAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $PageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);

        $ListePages = array();
        $est_page_contact = false;
        foreach ($PageStandard as $Pages) {
            if ($Pages->getStatusPage() == '1' && $Pages->getNomPage() == 'contact') {
                $ListePages = $Pages;
                $est_page_contact = true;
            }
        }

        return $this->render(
            'BeneficiaryBundle::block-contact.html.twig',
            array(
            'ListePages' => $ListePages,
            'est_page_contact' => $est_page_contact
            )
        );
    }

/**
     * @Route(
     *     "/beneficiary-home/pages/{id}",
     *     name="beneficiary_home_pages_standard"),
     *     requirements={"id": "\d+"}
     */
    public function AffichePagesStandardAction($id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }

        $background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();
        $Pages = $em->getRepository('AdminBundle:SitePagesStandardSetting')->find($id);

        if (is_null($Pages)) {
            return $this->redirectToRoute('beneficiary_home');
        }

        return $this->render(
            'BeneficiaryBundle:Page:AffichePagesStandard.html.twig',
            array(
            'has_network' => $has_network,
            'table_network' => $table_network,
            'background_link' => $background_link,
            'Pages' => $Pages
            )
        );
    }

    /**
     * @Route(
     *     "/pages/sondages-quiz/{slug}/{id}",
     *     name="beneficiary_home_pages_sondages_quiz_slug",
     *     defaults={"id"=null})
     */
    public function AffichePagesSondagesQuizSlugAction($slug, $id, Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }

        $background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();

        //Sondages Quiz
        $PagesSondagesQuiz = $em->getRepository('AdminBundle:SondagesQuiz')->findOneBy(
            array(
            'slug' => $slug
            )
        );

        //Sondages questionnaire infos
        if (!is_null($id)) {
            $PagesQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBy(
                array('sondages_quiz' => $PagesSondagesQuiz, 'est_publier' => '1', 'id' => $id)
            );
        } else {
            $PagesQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBy(
                array('sondages_quiz' => $PagesSondagesQuiz, 'est_publier' => '1')
            );
        }

        //Sondages questions
        $PagesReponses = array();
        $PagesQuestions = array();
        $Resultats = array();
        foreach ($PagesQuestionnaireInfos as $QuestionnaireInfos) {
            $Questions = $em->getRepository('AdminBundle:SondagesQuizQuestions')->findBy(
                array('sondages_quiz_questionnaire_infos' => $QuestionnaireInfos),
                array('ordre' => 'ASC')
            );

            $Resultats[$QuestionnaireInfos->getId()] = $em->getRepository('AdminBundle:ResultatsSondagesQuiz')->findBy(
                array(
                'sondages_quiz_questionnaire_infos' => $QuestionnaireInfos
                )
            );

            foreach ($Questions as $Questionsdata) {
                $Reponses = $em->getRepository('AdminBundle:SondagesQuizReponses')->findBy(
                    array('sondages_quiz_questions' => $Questionsdata),
                    array('ordre' => 'ASC')
                );
                $PagesReponses[$Questionsdata->getId()] = $Reponses;
            }
            $PagesQuestions[$QuestionnaireInfos->getId()] = $Questions;
        }

        if (is_null($PagesSondagesQuiz)) {
            return $this->redirectToRoute('beneficiary_home');
        }

        //Sumbit sondages/quiz
        if ($request->isMethod('POST')) {
            $PostReponses = $request->get('reponses');
            foreach ($PostReponses as $IdQuestInfos => $QuestionsInfos) {
                //Questionnaire Infos
                $SondagesQuizQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($IdQuestInfos);
                foreach ($QuestionsInfos as $TypeQuest => $ReponsesInfos) {
                    if ($TypeQuest == "'tm'") {
                        foreach ($ReponsesInfos as $IdQuestionsTm => $IdReponsesTm) {
                            //Reponses
                            foreach ($IdReponsesTm as $IdRep => $Niveaux) {
                                //Questions
                                $SondagesQuizQuestionsTm = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($IdQuestionsTm);
                                $SondagesQuizReponsesTm = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($IdRep);

                                $ResultatsSondagesQuiz = new ResultatsSondagesQuiz();
                                $ResultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfos);
                                $ResultatsSondagesQuiz->setProgram($program);
                                $ResultatsSondagesQuiz->setSondagesQuiz($PagesSondagesQuiz);
                                $ResultatsSondagesQuiz->setSondagesQuizReponses($SondagesQuizReponsesTm);
                                $ResultatsSondagesQuiz->setSondagesQuizQuestions($SondagesQuizQuestionsTm);
                                $ResultatsSondagesQuiz->setEchelle($Niveaux);
                                $em->persist($ResultatsSondagesQuiz);
                            }
                        }
                    } elseif ($TypeQuest == "'el'") {
                        foreach ($ReponsesInfos as $IdQuestionsEl => $IdReponsesEl) {
                            $SondagesQuizQuestionsEl = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($IdQuestionsEl);
                            $SondagesQuizReponsesEl = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($IdReponsesEl);

                            $ResultatsSondagesQuiz = new ResultatsSondagesQuiz();
                            $ResultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfos);
                            $ResultatsSondagesQuiz->setProgram($program);
                            $ResultatsSondagesQuiz->setSondagesQuiz($PagesSondagesQuiz);
                            $ResultatsSondagesQuiz->setSondagesQuizQuestions($SondagesQuizQuestionsEl);
                            $ResultatsSondagesQuiz->setSondagesQuizReponses($SondagesQuizReponsesEl);
                            $em->persist($ResultatsSondagesQuiz);
                        }
                    } elseif ($TypeQuest == "'ca'") {
                        foreach ($ReponsesInfos as $IdQuestionsCa => $IdReponsesCa) {
                            $SondagesQuizQuestionsCa = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($IdQuestionsCa);
                            $SondagesQuizReponsesCa = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($IdReponsesCa);
                            $ResultatsSondagesQuiz = new ResultatsSondagesQuiz();
                            $ResultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfos);
                            $ResultatsSondagesQuiz->setProgram($program);
                            $ResultatsSondagesQuiz->setSondagesQuiz($PagesSondagesQuiz);
                            $ResultatsSondagesQuiz->setSondagesQuizQuestions($SondagesQuizQuestionsCa);
                            $ResultatsSondagesQuiz->setSondagesQuizReponses($SondagesQuizReponsesCa);
                            $em->persist($ResultatsSondagesQuiz);
                        }
                    } elseif ($TypeQuest == "'cm'") {
                        foreach ($ReponsesInfos as $IdQuestionsCm => $IdReponsesCm) {
                            foreach ($IdReponsesCm as $IdRepCm) {
                                $SondagesQuizQuestionsCm = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($IdQuestionsCm);
                                $SondagesQuizReponsesCm = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($IdRepCm);
                                $ResultatsSondagesQuiz = new ResultatsSondagesQuiz();
                                $ResultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfos);
                                $ResultatsSondagesQuiz->setProgram($program);
                                $ResultatsSondagesQuiz->setSondagesQuiz($PagesSondagesQuiz);
                                $ResultatsSondagesQuiz->setSondagesQuizQuestions($SondagesQuizQuestionsCm);
                                $ResultatsSondagesQuiz->setSondagesQuizReponses($SondagesQuizReponsesCm);
                                $em->persist($ResultatsSondagesQuiz);
                            }
                        }
                    }
                }
            }

            $em->flush();
            if (!is_null($id)) {
                return $this->redirectToRoute('beneficiary_home_pages_sondages_quiz_slug', array('slug' => $PagesSondagesQuiz->getSlug(), 'id' => $id));
            } else {
                return $this->redirectToRoute('beneficiary_home_pages_sondages_quiz_slug', array('slug' => $PagesSondagesQuiz->getSlug()));
            }
        }

        return $this->render(
            'BeneficiaryBundle:Page:AfficheSondagesQuiz.html.twig',
            array(
            'has_network' => $has_network,
            'table_network' => $table_network,
            'background_link' => $background_link,
            'Pages' => $PagesSondagesQuiz,
            'PagesQuestionnaireInfos' => $PagesQuestionnaireInfos,
            'PagesQuestions' => $PagesQuestions,
            'PagesReponses' => $PagesReponses,
            'Resultats' => $Resultats
            )
        );
    }

    /**
     * To show E-learning page listing
     * @Route("/pages/e-learning", name="elearning_page")
     */
    public function AffichagePageELearning(){
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $backgroundLink = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $backgroundLink = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }
        $em = $this->getDoctrine()->getManager();
        $ElearningBanner = $em->getRepository('AdminBundle\Entity\ELearningHomeBanner')->findOneBy(array('program' => $program));
        if (empty($ElearningBanner)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }
        $em = $this->getDoctrine()->getManager();
        $e_learning_list = $em->getRepository('AdminBundle\Entity\ELearning')->findBy(
            array('program' => $program),
            array('created_at' => 'DESC')
        );
        return $this->render(
            'BeneficiaryBundle:Page:AffichagePageElearning.html.twig',
            array(
                'background_link' => $backgroundLink,
                'elearning_banner' => $ElearningBanner,
                'has_network' => $has_network,
                'e_learning_list' => $e_learning_list,
            ));
    }

    /**
     * @Route(
     *     "/pages/{slug}",
     *     name="beneficiary_home_pages_standard_slug")
     */
    public function AffichePagesStandardSlugAction($slug)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $table_network = $program->getSiteTableNetworkSetting();
        $has_network = false;
        if ($table_network->getHasFacebook() || $table_network->getHasLinkedin() || $table_network->getHasTwitter()) {
            $has_network = true;
        }

        $background_link = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $background_link = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();
        $Pages = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findOneBy(
            array(
            'slug' => $slug
            )
        );

        if (is_null($Pages)) {
            return $this->redirectToRoute('beneficiary_home');
        }

        if ($Pages->getStatusPage() != '1') {
            return $this->redirectToRoute('beneficiary_home');
        }

        $Options = array();
        $Options = $Pages->getOptions();

        // Obtient une liste de colonnes
        if (count($Options) > 0) {
            foreach ($Options as $key => $row) {
                $ordre[$key]  = $row['ordre'];
            }
            array_multisort($ordre, SORT_ASC, $Options);
        }

        return $this->render(
            'BeneficiaryBundle:Page:AffichePagesStandard.html.twig',
            array(
            'has_network' => $has_network,
            'table_network' => $table_network,
            'background_link' => $background_link,
            'Pages' => $Pages,
            'Options' => $Options
            )
        );
    }
}
