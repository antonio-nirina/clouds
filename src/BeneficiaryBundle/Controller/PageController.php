<?php
namespace BeneficiaryBundle\Controller;

use AdminBundle\Component\ELearning\ELearningContentType;
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
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
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

        $homePageData = $program->getHomePageData();
        if (is_null($homePageData)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $tableNetwork = $program->getSiteTableNetworkSetting();
        $hasNetwork = false;
        if ($tableNetwork->getHasFacebook() || $tableNetwork->getHasLinkedin() || $tableNetwork->getHasTwitter()) {
            $hasNetwork = true;
        }

        $backgroundLink = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $backgroundLink = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();
        $orderedSlideList = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findByHomePageDataOrdered($homePageData);

        $parameterEdito = $em->getRepository('AdminBundle\Entity\HomePagePost')->findOneBy(
            array('program' => $program, 'post_type' => PostType::PARAMETER_EDITO)
        );

        $homePagePostList = $em->getRepository('AdminBundle\Entity\HomePagePost')
            ->findPublishedNewsPost($program);
        if (!is_null($parameterEdito)) {
            array_push($homePagePostList, $parameterEdito);
        }
        $postOrdering = $this->get('AdminBundle\Service\DataOrdering\PostOrdering');
        $homePagePostList = $postOrdering->orderByDateDesc($homePagePostList);

        return $this->render(
            'BeneficiaryBundle:Page:home.html.twig',
            array(
            'has_network' => $hasNetwork,
            'table_network' => $tableNetwork,
            'slide_list' => $orderedSlideList,
            'background_link' => $backgroundLink,
            'home_page_post_list' => $homePagePostList,
            'home_page_post_type_class' => new PostType(),
            )
        );
    }

    /**
     * @Route("/beneficiary-home/video/lecture", name="beneficiary_home_video_lecture")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function lectureVideoAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $idVideo = $request->get('video_id');
            $videoSlide = $em->getRepository("AdminBundle:HomePageSlide")->find($idVideo);
            $response = $this->forward('BeneficiaryBundle:PartialPage:afficheLecteurVideo', array('videos' => $videoSlide, 'programm' => $program));

            return new Response($response->getContent());
        } else {
            return new Response('');
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function pageStandardFooterAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $pageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);

        $listePages = array();
        foreach ($pageStandard as $pages) {
            if ($pages->getStatusPage() == '1') {
                if ($pages->getNomPage() == 'mentions légales'
                    || $pages->getNomPage() == 'règlement'
                    || $pages->getNomPage() == 'contact') {
                    $listePages[] = $pages;
                }
            }
        }

        return $this->render(
            'BeneficiaryBundle::page_footer.html.twig',
            array(
            'liste_pages' => $listePages,
            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function pageStandardTopAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        //pages standards
        $em = $this->getDoctrine()->getManager();
        $pageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);
        $listePages = array();
        foreach ($pageStandard as $pages) {
            if ($pages->getStatusPage() == '1') {
                if ($pages->getNomPage() != 'mentions légales' && $pages->getNomPage() != 'règlement' && $pages->getNomPage() != 'contact') {
                    $listePages[] = $pages;
                }
            }
        }
        //Sondages/Quiz
        $sondagesQuiz = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        $isSondagesQuiz = false;
        $objSondagesQuiz = array();
        if (isset($sondagesQuiz[0])) {
            $objSondagesQuiz = $sondagesQuiz[0];
            $isSondagesQuiz = true;
        }
        //show/E-learning
        $elearning = $em->getRepository('AdminBundle\Entity\ELearningHomeBanner')->findOneBy(array('program' => $program));
        $isELearning  = false;
        $objElearning = array();
        if (isset($elearning)) {
            $objElearning = $elearning;
            $isELearning = true;
        }

        return $this->render(
            'BeneficiaryBundle::menu_top_niv_2.html.twig',
            array(
                'liste_pages' => $listePages,
                'IsSondagesQuiz' => $isSondagesQuiz,
                'ObjSondagesQuiz' => $objSondagesQuiz,
                'IsElearning' => $isELearning,
                'ObjElearning' => $objElearning,

            )
        );
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function pageStandardContactAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $pageStandard = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findByProgram($program);

        $listePages = array();
        $estPageContact = false;
        foreach ($pageStandard as $pages) {
            if ($pages->getStatusPage() == '1' && $pages->getNomPage() == 'contact') {
                $listePages = $pages;
                $estPageContact = true;
            }
        }

        return $this->render(
            'BeneficiaryBundle::block-contact.html.twig',
            array(
            'liste_pages' => $listePages,
            'est_page_contact' => $estPageContact,
            )
        );
    }

    /**
     * Affiche page standard
     *
     * @Route(
     *     "/beneficiary-home/pages/{id}",
     *     name="beneficiary_home_pages_standard"),
     *     requirements={"id": "\d+"}
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function affichePagesStandardAction($id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $tableNetwork = $program->getSiteTableNetworkSetting();
        $hasNetwork = false;
        if ($tableNetwork->getHasFacebook() || $tableNetwork->getHasLinkedin() || $tableNetwork->getHasTwitter()) {
            $hasNetwork = true;
        }

        $backgroundLink = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $backgroundLink = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('AdminBundle:SitePagesStandardSetting')->find($id);

        if (is_null($pages)) {
            return $this->redirectToRoute('beneficiary_home');
        }

        return $this->render(
            'BeneficiaryBundle:Page:AffichePagesStandard.html.twig',
            array(
            'has_network' => $hasNetwork,
            'table_network' => $tableNetwork,
            'background_link' => $backgroundLink,
            'pages' => $pages
            )
        );
    }

    /**
     * @Route(
     *     "/pages/sondages-quiz/{slug}/{id}",
     *     name="beneficiary_home_pages_sondages_quiz_slug",
     *     defaults={"id"=null})
     */
    public function affichePagesSondagesQuizSlugAction($slug, $id, Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $tableNetwork = $program->getSiteTableNetworkSetting();
        $hasNetwork = false;
        if ($tableNetwork->getHasFacebook() || $tableNetwork->getHasLinkedin() || $tableNetwork->getHasTwitter()) {
            $hasNetwork = true;
        }

        $backgroundLink = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $backgroundLink = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();

        //Sondages Quiz
        $pagesSondagesQuiz = $em->getRepository('AdminBundle:SondagesQuiz')->findOneBy(
            array(
            'slug' => $slug
            )
        );

        //Sondages questionnaire infos
        if (!is_null($id)) {
            $pagesQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBy(
                array('sondages_quiz' => $pagesSondagesQuiz, 'est_publier' => '1', 'id' => $id)
            );
        } else {
            $pagesQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBy(
                array('sondages_quiz' => $pagesSondagesQuiz, 'est_publier' => '1')
            );
        }

        //Sondages questions
        $pagesReponses = array();
        $pagesQuestions = array();
        $resultats = array();
        foreach ($pagesQuestionnaireInfos as $questionnaireInfos) {
            $questions = $em->getRepository('AdminBundle:SondagesQuizQuestions')->findBy(
                array('sondages_quiz_questionnaire_infos' => $questionnaireInfos),
                array('ordre' => 'ASC')
            );

            $resultats[$questionnaireInfos->getId()] = $em->getRepository('AdminBundle:ResultatsSondagesQuiz')->findBy(
                array(
                'sondages_quiz_questionnaire_infos' => $questionnaireInfos
                )
            );

            foreach ($questions as $questionsData) {
                $reponses = $em->getRepository('AdminBundle:SondagesQuizReponses')->findBy(
                    array('sondages_quiz_questions' => $questionsData),
                    array('ordre' => 'ASC')
                );
                $pagesReponses[$questionsData->getId()] = $reponses;
            }
            $pagesQuestions[$questionnaireInfos->getId()] = $questions;
        }

        if (is_null($pagesSondagesQuiz)) {
            return $this->redirectToRoute('beneficiary_home');
        }

        //Sumbit sondages/quiz
        if ($request->isMethod('POST')) {
            $postReponses = $request->get('reponses');
            foreach ($postReponses as $idQuestInfos => $questionsInfos) {
                //Questionnaire Infos
                $sondagesQuizQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($idQuestInfos);
                foreach ($questionsInfos as $typeQuest => $reponsesInfos) {
                    if ($typeQuest == "'tm'") {
                        foreach ($reponsesInfos as $idQuestionsTm => $idReponsesTm) {
                            //Reponses
                            foreach ($idReponsesTm as $idRep => $niveaux) {
                                //Questions
                                $sondagesQuizQuestionsTm = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($idQuestionsTm);
                                $sondagesQuizReponsesTm = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($idRep);

                                $resultatsSondagesQuiz = new ResultatsSondagesQuiz();
                                $resultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfos);
                                $resultatsSondagesQuiz->setProgram($program);
                                $resultatsSondagesQuiz->setSondagesQuiz($pagesSondagesQuiz);
                                $resultatsSondagesQuiz->setSondagesQuizReponses($sondagesQuizReponsesTm);
                                $resultatsSondagesQuiz->setSondagesQuizQuestions($sondagesQuizQuestionsTm);
                                $resultatsSondagesQuiz->setEchelle($niveaux);
                                $em->persist($resultatsSondagesQuiz);
                            }
                        }
                    } elseif ($typeQuest == "'el'") {
                        foreach ($reponsesInfos as $idQuestionsEl => $idReponsesEl) {
                            $sondagesQuizQuestionsEl = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($idQuestionsEl);
                            $sondagesQuizReponsesEl = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($idReponsesEl);

                            $resultatsSondagesQuiz = new ResultatsSondagesQuiz();
                            $resultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfos);
                            $resultatsSondagesQuiz->setProgram($program);
                            $resultatsSondagesQuiz->setSondagesQuiz($pagesSondagesQuiz);
                            $resultatsSondagesQuiz->setSondagesQuizQuestions($sondagesQuizQuestionsEl);
                            $resultatsSondagesQuiz->setSondagesQuizReponses($sondagesQuizReponsesEl);
                            $em->persist($resultatsSondagesQuiz);
                        }
                    } elseif ($typeQuest == "'ca'") {
                        foreach ($reponsesInfos as $idQuestionsCa => $idReponsesCa) {
                            $sondagesQuizQuestionsCa = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($idQuestionsCa);
                            $sondagesQuizReponsesCa = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($idReponsesCa);
                            $resultatsSondagesQuiz = new ResultatsSondagesQuiz();
                            $resultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfos);
                            $resultatsSondagesQuiz->setProgram($program);
                            $resultatsSondagesQuiz->setSondagesQuiz($pagesSondagesQuiz);
                            $resultatsSondagesQuiz->setSondagesQuizQuestions($sondagesQuizQuestionsCa);
                            $resultatsSondagesQuiz->setSondagesQuizReponses($sondagesQuizReponsesCa);
                            $em->persist($resultatsSondagesQuiz);
                        }
                    } elseif ($typeQuest == "'cm'") {
                        foreach ($reponsesInfos as $idQuestionsCm => $idReponsesCm) {
                            foreach ($idReponsesCm as $idRepCm) {
                                $sondagesQuizQuestionsCm = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($idQuestionsCm);
                                $sondagesQuizReponsesCm = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($idRepCm);
                                $resultatsSondagesQuiz = new ResultatsSondagesQuiz();
                                $resultatsSondagesQuiz->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfos);
                                $resultatsSondagesQuiz->setProgram($program);
                                $resultatsSondagesQuiz->setSondagesQuiz($pagesSondagesQuiz);
                                $resultatsSondagesQuiz->setSondagesQuizQuestions($sondagesQuizQuestionsCm);
                                $resultatsSondagesQuiz->setSondagesQuizReponses($sondagesQuizReponsesCm);
                                $em->persist($resultatsSondagesQuiz);
                            }
                        }
                    }
                }
            }

            $em->flush();
            if (!is_null($id)) {
                return $this->redirectToRoute(
                    'beneficiary_home_pages_sondages_quiz_slug',
                            array(
                                'slug' => $pagesSondagesQuiz->getSlug(),
                                'id' => $id,
                            )
                );
            } else {
                return $this->redirectToRoute(
                    'beneficiary_home_pages_sondages_quiz_slug',
                    array('slug' => $pagesSondagesQuiz->getSlug())
                );
            }
        }

        return $this->render(
            'BeneficiaryBundle:Page:AfficheSondagesQuiz.html.twig',
            array(
            'has_network' => $hasNetwork,
            'table_network' => $tableNetwork,
            'background_link' => $backgroundLink,
            'Pages' => $pagesSondagesQuiz,
            'PagesQuestionnaireInfos' => $pagesQuestionnaireInfos,
            'PagesQuestions' => $pagesQuestions,
            'PagesReponses' => $pagesReponses,
            'Resultats' => $resultats
            )
        );
    }

    /**
     * To show E-learning page listing
     * @Route("/pages/e-learning", name="elearning_page")
     */
    public function affichagePageELearning()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $backgroundLink = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $backgroundLink = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }
        $elearningBanner = $em->getRepository('AdminBundle\Entity\ELearningHomeBanner')->findOneBy(array('program' => $program));
        if (empty($elearningBanner)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $tableNetwork = $program->getSiteTableNetworkSetting();
        $hasNetwork = false;
        if ($tableNetwork->getHasFacebook() || $tableNetwork->getHasLinkedin() || $tableNetwork->getHasTwitter()) {
            $hasNetwork = true;
        }
        $elearningList = $em->getRepository('AdminBundle\Entity\ELearning')->findBy(
            array('program' => $program),
            array('created_at' => 'DESC')
        );
        return $this->render(
            'BeneficiaryBundle:Page:AffichagePageElearning.html.twig',
            array(
                'background_link' => $backgroundLink,
                'elearning_banner' => $elearningBanner,
                'has_network' => $hasNetwork,
                'e_learning_list' => $elearningList,
                'content_type_class' => new ELearningContentType(),
            )
        );
    }

    /**
     * @Route(
     *     "/pages/{slug}",
     *     name="beneficiary_home_pages_standard_slug")
     */
    public function affichePagesStandardSlugAction($slug)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $tableNetwork = $program->getSiteTableNetworkSetting();
        $hasNetwork = false;
        if ($tableNetwork->getHasFacebook() || $tableNetwork->getHasLinkedin() || $tableNetwork->getHasTwitter()) {
            $hasNetwork = true;
        }

        $backgroundLink = '';
        if ($background = $program->getSiteDesignSetting()->getBodyBackground()) {
            $backgroundLink = $this->container->getParameter('background_path') . '/' . $program->getId() . '/' . $background;
        }

        $em = $this->getDoctrine()->getManager();
        $pages = $em->getRepository('AdminBundle:SitePagesStandardSetting')->findOneBy(
            array(
            'slug' => $slug
            )
        );

        if (is_null($pages)) {
            return $this->redirectToRoute('beneficiary_home');
        }

        if ($pages->getStatusPage() != '1') {
            return $this->redirectToRoute('beneficiary_home');
        }

        $options = array();
        $options = $pages->getOptions();

        // Obtient une liste de colonnes
        if (count($options) > 0) {
            foreach ($options as $key => $row) {
                $ordre[$key]  = $row['ordre'];
            }
            array_multisort($ordre, SORT_ASC, $options);
        }

        return $this->render(
            'BeneficiaryBundle:Page:AffichePagesStandard.html.twig',
            array(
            'has_network' => $hasNetwork,
            'table_network' => $tableNetwork,
            'background_link' => $backgroundLink,
            'Pages' => $pages,
            'Options' => $options
            )
        );
    }
}