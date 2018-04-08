<?php
namespace AdminBundle\Controller;

use AdminBundle\Component\CommunicationEmail\TemplateContentType;
use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use AdminBundle\Component\CommunicationEmail\TemplateModel;
use AdminBundle\Component\CommunicationEmail\TemplateSortingParameter;
use AdminBundle\Component\Post\PostType;
use AdminBundle\Component\Slide\SlideType;
use AdminBundle\Controller\AdminController;
use AdminBundle\DTO\CampaignDraftData;
use AdminBundle\DTO\ComEmailTemplateDuplicationData;
use AdminBundle\DTO\DuplicationData;
use AdminBundle\Entity\ComEmailTemplate;
use AdminBundle\Entity\HomePagePost;
use AdminBundle\Form\CampaignDateType;
use AdminBundle\Form\CampaignDraftType;
use AdminBundle\Form\ComEmailTemplateType;
use AdminBundle\Form\HomePagePostType;
use AdminBundle\Form\HomePageSlideDataType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Form\DuplicationForm;
use AdminBundle\Form\SondagesQuizType;
use AdminBundle\Form\SondagesQuizQuestionnaireInfosType;
use AdminBundle\Entity\SondagesQuiz;
use AdminBundle\Entity\SondagesQuizQuestionnaireInfos;
use AdminBundle\Entity\SondagesQuizQuestions;
use AdminBundle\Entity\SondagesQuizReponses;
use AdminBundle\Component\Post\NewsPostSubmissionType;

use \Mailjet\Resources;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Filesystem\Filesystem;
use AdminBundle\Service\Statistique\Common;
use AdminBundle\Component\CommunicationEmail\CampaignDraftCreationMode;
use AdminBundle\Component\Post\NewsPostAuthorizationType;
use AdminBundle\Component\GroupAction\GroupActionType;
use AdminBundle\Component\Post\NewsPostTypeLabel;
use AdminBundle\Component\Submission\SubmissionType;
use AdminBundle\Component\ELearning\ELearningContentType;
use AdminBundle\Component\Authorization\AuthorizationType;




/**
 * @Route("/admin/communication")
 */
class CommunicationController extends AdminController
{
    const SIDEBAR_VIEW = 'AdminBundle:Communication:menu_sidebar_communication.html.twig';
    const TEMPLATE_NOT_FOUND_MESSAGE = 'Modèle non trouvé';

    public function __construct()
    {
        $this->active_menu_index = 3;
        $this->sidebar_view = self::SIDEBAR_VIEW;
    }

    /**
     * disabled action
     * route: /edito
     * name: admin_communication_editorial
     */
    public function editorialAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $new_edito_post = new HomePagePost();
        $form_factory = $this->get('form.factory');
        $add_edito_form = $form_factory->createNamed(
            'add_edito_form',
            HomePagePostType::class,
            $new_edito_post
        );

        $em = $this->getDoctrine()->getManager();
        $edito_list = $em->getRepository('AdminBundle\Entity\HomePagePost')
            ->findByProgramAndPostTypeOrdered($program, PostType::EDITO);

        $edito_form_list_generator = $this->get('AdminBundle\Service\FormList\EditoFormListGenerator');
        $edit_edito_form_list = $edito_form_list_generator->generateFormList($edito_list, HomePagePostType::class);
        $edit_edito_form_view_list = $edito_form_list_generator->generateFormViewList($edit_edito_form_list);

        if ("POST" === $request->getMethod()) {
            $edito_manager = $this->get('AdminBundle\Manager\HomePagePostEditoManager');
            if ($request->request->has('add_edito_form')) {
                $add_edito_form->handleRequest($request);
                if ($add_edito_form->isSubmitted() && $add_edito_form->isValid()) {
                    $edito_manager->createEdito($program, $new_edito_post);
                    return $this->redirectToRoute('admin_communication_editorial');
                }
            }

            foreach ($edit_edito_form_list as $edit_edito_form) {
                if ($request->request->has($edit_edito_form->getName())) {
                    $edit_edito_form->handleRequest($request);
                    if ($edit_edito_form->isSubmitted() && $edit_edito_form->isValid()) {
                        $em->flush();
                        return $this->redirectToRoute('admin_communication_editorial');
                    }
                }
            }
        }

        return $this->render('AdminBundle:Communication:edito.html.twig', array(
            'add_edito_form' => $add_edito_form->createView(),
            'edit_edito_form_list' => $edit_edito_form_view_list
        ));
    }

    /**
     * disabled action
     * route: /edito/suppression/{id}
     * name: admin_communication_editorial_delete
     * requirements: {"id": "\d+"}
     */
    public function deleteEditorialAction($id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $edito_manager = $this->get('AdminBundle\Manager\HomePagePostEditoManager');
        $edito_manager->deleteEditoById($program, (int)$id);

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route("/slideshow", name="admin_communication_slideshow")
     */
    public function slideshowAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $slideshow_manager = $this->container->get('admin.slideshow');
        $original_slides = $slideshow_manager->getOriginalSlides($home_page_data);
        $original_slides_image = $slideshow_manager->getOriginalSlidesImage($original_slides);

        $form_factory = $this->get('form.factory');
        $home_page_slide_data_form = $form_factory->createNamed(
            'home_page_slide_data_form',
            HomePageSlideDataType::class,
            $home_page_data
        );

        if ("POST" === $request->getMethod()) {
            if ($request->request->has('home_page_slide_data_form')) {
                $home_page_slide_data_form->handleRequest($request);
                if ($home_page_slide_data_form->isSubmitted() && $home_page_slide_data_form->isValid()) {
                    // checking for "delete image" commands
                    $deleted_image_slide_id_list = $slideshow_manager->checkDeletedImages(
                        $home_page_slide_data_form,
                        $home_page_data,
                        $original_slides_image
                    );
                    // editing existant slide
                    $home_page_data = $slideshow_manager->editHomePageSlides(
                        $home_page_data,
                        $deleted_image_slide_id_list,
                        $original_slides_image
                    );
                    // deleting slides
                    $home_page_data = $slideshow_manager->deleteHomePageSlides($home_page_data, $original_slides);
                    // adding new slide
                    $home_page_data = $slideshow_manager->addNewHomePageSlides($home_page_data);
                    $slideshow_manager->save();

                    return $this->redirectToRoute('admin_communication_slideshow');
                }
            }
        }

        return $this->render('AdminBundle:Communication:slideshow.html.twig', array(
            'home_page_slide_data_form' => $home_page_slide_data_form->createView(),
            'original_slides_image' => $original_slides_image,
            'slide_type' => new SlideType(),
        ));
    }

    /**
     * @Route("/emailing/campagne", name="admin_communication_emailing_compaign")
     */
    public function emailingCampaignAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $campaign = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $filters = array('Limit' => 0);
        $campaign_data_list = $campaign->getAllVisibleWithData($filters);

        return $this->render('AdminBundle:Communication:emailing_campaign.html.twig', array(
            "list" => $campaign_data_list,
            'content_type_class' => new TemplateContentType(),
            'template_model_class' => new TemplateModel(),
            'campaign_draft_creation_mode_class' => new CampaignDraftCreationMode(),
        ));
    }

    /**
     * @Route("/emailing/campagne/new",
     * name="admin_communication_emailing_compaign_new"),
     * @Method("POST")
     */
    public function emailingCampaignNewAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\CampaignDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $creation_mode = $request->get('creation_mode');
        if (is_null($creation_mode)) {
            $creation_mode = CampaignDraftCreationMode::NORMAL;
        }

        if (!in_array($creation_mode, CampaignDraftCreationMode::VALID_CREATION_MODE)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $validation_groups = array('normal_creation_mode');
        if (CampaignDraftCreationMode::BY_HALT == $creation_mode) {
            $validation_groups = array('Default');
        }

        $campaign_draft_data = new CampaignDraftData();
        $campaign_draft_data->setProgrammedLaunchDate(new \DateTime('now'));


        $campaign_draft_form = $this->createForm(
            CampaignDraftType::class,
            $campaign_draft_data,
            array('validation_groups' => $validation_groups)
        );

        $campaign_draft_form->handleRequest($request);
        if ($campaign_draft_form->isSubmitted() && $campaign_draft_form->isValid()) {
            $campaign_handler = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
            if (CampaignDraftCreationMode::NORMAL == $creation_mode) {
                if ($campaign_handler->createAndProcess($campaign_draft_data)) {
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    $data = $json_response_data_provider->campaignSendingError();
                    return new JsonResponse($data, 200);
                }
            } elseif (CampaignDraftCreationMode::BY_HALT == $creation_mode) {
                if (!is_null($campaign_handler->createCampaignDraftByHalt($campaign_draft_data))) {
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    $data = $json_response_data_provider->campaignDraftCreationError();
                    return new JsonResponse($data, 200);
                }
            }
        }

        $template_manager = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $template_list = $template_manager->listSortedTemplate($program);
        $template_list_data_handler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $template_data_list = $template_list_data_handler->retrieveListDataIndexedById($template_list);

        $view = $this->renderView('AdminBundle:Communication/EmailingCampaign:manip_campaign.html.twig', array(
            'campaign_draft_form' => $campaign_draft_form->createView(),
            'template_data_list' => $template_data_list,
        ));
        $data = $json_response_data_provider->success();
        if ($campaign_draft_form->isSubmitted() && !$campaign_draft_form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $view;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/editer/{campaign_draft_id}",
     *  name="admin_communication_emailing_campaign_edit")
     * @Method("POST")
     */
    public function emailingCampaignEditAction(Request $request, $campaign_draft_id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\CampaignDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $edit_mode = $request->get('edit_mode');
        if (is_null($edit_mode)) {
            $edit_mode = CampaignDraftCreationMode::NORMAL;
        }

        if (!in_array($edit_mode, CampaignDraftCreationMode::VALID_CREATION_MODE)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $validation_groups = array('normal_creation_mode');
        if (CampaignDraftCreationMode::BY_HALT == $edit_mode) {
            $validation_groups = array('Default');
        }

        $campaign_handler = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $campaign_draft_data = $campaign_handler->findCampaignDraftAsDTO($campaign_draft_id);
        $campaign_draft_form = $this->createForm(
            CampaignDraftType::class,
            $campaign_draft_data,
            array('validation_groups' => $validation_groups)
        );
        $campaign_draft_form->handleRequest($request);
        if ($campaign_draft_form->isSubmitted() && $campaign_draft_form->isValid()) {
            if (CampaignDraftCreationMode::NORMAL == $edit_mode) {
                if ($campaign_handler->editAndProcess($campaign_draft_data)) {
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    $data = $json_response_data_provider->campaignSendingError();
                    return new JsonResponse($data, 200);
                }
            } elseif (CampaignDraftCreationMode::BY_HALT == $edit_mode) {
                if ($campaign_handler->editCampaignDraftByHalt($campaign_draft_data)) {
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    $data = $json_response_data_provider->campaignDraftEditError();
                    return new JsonResponse($data, 200);
                }
            }
        }

        $template_manager = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $template_list = $template_manager->listSortedTemplate($program);
        $template_list_data_handler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $template_data_list = $template_list_data_handler->retrieveListDataIndexedById($template_list);
        $view = $this->renderView('AdminBundle:Communication/EmailingCampaign:manip_campaign.html.twig', array(
            'campaign_draft_form' => $campaign_draft_form->createView(),
            'template_data_list' => $template_data_list,
            'edit_mode' => true,
        ));
        $data = $json_response_data_provider->success();
        if ($campaign_draft_form->isSubmitted() && !$campaign_draft_form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $view;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/filter", name="admin_communication_emailing_compaign_filter")
     * @Method("POST")
     */
    public function emailingCampaignFilterAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $status = $request->get('status');
        $campaign = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');

        $view_options = array();
        if (!is_null($request->get('archived_campaign_mode'))
            && 'true' == $request->get('archived_campaign_mode')
        ) {
            $campaign_data_list = $campaign->getAllArchivedWithDataFiltered($status);
            $view_options['archived_mode'] = true;
        } else {
            $campaign_data_list = $campaign->getAllVisibleWithDataFiltered($status);
        }
        $view_options['list'] = $campaign_data_list;

        return $this->render('AdminBundle:Communication:emailing_campaign_filtered.html.twig', $view_options);
    }

    /**
     * @Route("/emailing/campagne/archivees", name="admin_communication_emailing_compaign_archived")
     * @Method("POST")
     */
    public function emailingArchivedCampaignAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $campaign = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $filters = array('Limit' => 0);
        $campaign_data_list = $campaign->getAllArchivedWithData($filters);

        return $this->render('AdminBundle:Communication:emailing_campaign_filtered.html.twig', array(
            'list' => $campaign_data_list,
            'archived_mode' => true,
        ));
    }

    /**
     * @Route("/emailing/campagne/archiver", name="admin_communication_emailing_campaign_archive")
     * @Method("POST")
     */
    public function emailingCampaignArchiveAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $to_archive_campaign_ids = $request->get('campaign_checked_ids');
        $to_archive_campaign_ids = explode(',', $to_archive_campaign_ids);

        $campaign_handler = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        if (!empty($to_archive_campaign_ids)) {
            $campaign_handler->archiveCampaignDraftByIdList($to_archive_campaign_ids);
        }

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/emailing/campagne/restaurer", name="admin_communication_emailing_campaign_restore_archived")
     * @Method("POST")
     */
    public function emailingCampaignRestoreArchivedAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $to_restore_ids = $request->get('campaign_checked_ids');
        $to_restore_ids = explode(',', $to_restore_ids);

        $campaign_handler = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        if (!empty($to_restore_ids)) {
            $campaign_handler->restoreArchivedCampaignDraftByIdList($to_restore_ids);
        }

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/emailing/campagne/dupliquer", name="admin_communication_emailing_campaign_duplicate")
     * @Method("POST")
     */
    public function emailingCampaignDuplicateAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $campaign_duplication_source_id = $request->get('campaign_draft_id');
        $campaign_handler = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $campaign_duplication_source = $campaign_handler->retrieveCampaignDraftById($campaign_duplication_source_id);
        if (is_null($campaign_duplication_source_id) || is_null($campaign_duplication_source)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $duplication_data = new DuplicationData();
        $duplication_data->setDuplicationSourceId($campaign_duplication_source['ID'])
            ->setName($campaign_duplication_source['Title']);
        $campaign_duplication_form = $this->createForm(DuplicationForm::class, $duplication_data);
        $campaign_duplication_form->handleRequest($request);

        if ($campaign_duplication_form->isSubmitted() && $campaign_duplication_form->isValid()) {
            if ($campaign_duplication_source_id == $duplication_data->getDuplicationSourceId()) {
                $campaign_handler->duplicateCampaignDraft($campaign_duplication_source, $duplication_data->getName());
                $data = $json_response_data_provider->success();

                return new JsonResponse($data, 200);
            }
        }

        $view = $this->renderView(
            'AdminBundle:Communication/EmailingTemplates:duplicate_campaign.html.twig',
            array('duplicate_campaign_form' => $campaign_duplication_form->createView())
        );
        $data = $json_response_data_provider->success();
        $data['content'] = $view;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/supprimer", name="admin_communication_emailing_campaign_delete")
     * @Method("POST")
     */
    public function emailingCampaignDeleteAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $to_delete_campaign_ids = explode(',', $request->get('campaign_checked_ids'));
        $campaign_handler = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        if (!empty($to_delete_campaign_ids)) {
            $campaign_handler->deleteCampaignDraftByIdList($to_delete_campaign_ids);
        }

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/emailing/campagne/creer-liste-contact", name="admin_communication_emailing_campaign_create_contact_list")
     * @Method("POST")
     */
    public function emailingCampaignCreateContactList(Request $request)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\ContactListDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $list_name = $request->get('ListName');
        $user_ids = $request->get('UserId');
        $arr_user_ids = explode('##_##', $user_ids);

        $em = $this->getDoctrine()->getManager();
        $users_list = array();
        foreach ($arr_user_ids as $user_id) {
            $users_list[] = $em->getRepository('UserBundle\Entity\User')->find($user_id);
        }
        $contact_list_handler = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
        $response = $contact_list_handler->addContactListReturningInfos($list_name, $users_list);
        if (!empty($response)) {
            return new JsonResponse($json_response_data_provider->contactListCreationSuccess(
                $response['contact_list_infos']['ID'],
                ''
            ), 200);
        } else {
            return new JsonResponse($json_response_data_provider->contactListCreationError(), 200);
        }
    }

    /**
     * @Route("/emailing/modeles-emails", name="admin_communication_emailing_templates")
     */
    public function emailingTemplatesAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $template_manager = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $template_list = $template_manager->listSortedTemplate($program);
        $template_list_data_handler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $template_data_list = $template_list_data_handler->retrieveListData($template_list);

        return $this->render('AdminBundle:Communication:emailing_templates.html.twig', array(
            'template_model_class' => new TemplateModel(),
            'template_data_list' => $template_data_list,
            'content_type_class' => new TemplateContentType(),
            'template_sorting_parameter_class' => new TemplateSortingParameter(),
            'campaign_draft_creation_mode_class' => new CampaignDraftCreationMode(),
        ));
    }

    /**
     * @Route(
     *     "/emailing/modeles-emails/tri/{sorting_parameter}",
     *     name="admin_communication_emailing_templates_sort",
     *     defaults={"sorting_parameter"=null})
     */
    public function listSortedEmailingTemplatesAction($sorting_parameter)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $available_sorting_parameter = TemplateSortingParameter::AVAILABLE_SORTING_PARAMETERS;
        if (!in_array($sorting_parameter, $available_sorting_parameter)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $template_manager = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $template_list = $template_manager->listSortedTemplate($program, $sorting_parameter);
        $template_list_data_handler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $template_data_list = $template_list_data_handler->retrieveListData($template_list);

        $template_list_view = $this
            ->renderView('AdminBundle:Communication/EmailingTemplates:sorted_emailing_template.html.twig', array(
                'template_data_list' => $template_data_list
            ));

        $data = $json_response_data_provider->success();
        $data['content'] = $template_list_view;
        return new JsonResponse($data, 200);
    }

    /**
     * @Route(
     *     "/emailling/modeles-emails/ajout-modele/{model}",
     *     name="admin_communication_emailing_templates_add_template",
     *     defaults={"model"=null}
     * )
     */
    public function emailingTemplatesAddTemplateAction(Request $request, $model)
    {
        $auth_checker = $this->get('security.authorization_checker');
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (false === $auth_checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $valid_models = array(TemplateModel::TEXT_AND_IMAGE, TemplateModel::TEXT_ONLY);

        $template_data_initializer = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateDataInitializer');
        $com_email_template = $template_data_initializer->initForNewTemplate();
        $com_email_template->setLogoAlignment(TemplateLogoAlignment::CENTER);
        $form_factory = $this->get('form.factory');
        if ($request->isMethod('GET')) {
            if (!is_null($model) && in_array($model, $valid_models)) {
                $com_email_template->setTemplateModel($model);
            }
        }
        $add_template_form = $form_factory->createNamed(
            'add_template_form',
            ComEmailTemplateType::class,
            $com_email_template
        );

        $template_data_generator = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator');
        if ($request->isMethod('GET')) {
            if (!is_null($model) && in_array($model, $valid_models)) {
                $template_data_generator->setComEmailTemplate($com_email_template);
                $form_view =  $this->renderView(
                    'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                    array(
                        'manip_template_form' => $add_template_form->createView(),
                        'current_template_model' => $model,
                        'template_model_class' => new TemplateModel(),
                        'content_type_class' => new TemplateContentType(),
                        'instantaneous_template_preview' => $template_data_generator
                            ->retrieveContentPartHtml(true, true),
                        'template_logo_alignment_class' => new TemplateLogoAlignment(),
                    )
                );
                $data = $json_response_data_provider->success();
                $data['content'] = $form_view;
                return new JsonResponse($data, 200);
            } else {
                return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
            }
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has("add_template_form")) {
                $add_template_form->handleRequest($request);
                if ($add_template_form->isSubmitted() && $add_template_form->isValid()) {
                    $com_email_template_data_sync = $this
                        ->get('AdminBundle\Service\DataSynchronizer\ComEmailTemplateDataSynchronizer');
                    $created_template_id = $com_email_template_data_sync->createTemplate(
                        $program,
                        $com_email_template,
                        $this->getUser()
                    );

                    if (!is_null($created_template_id)) {
                        $data = $json_response_data_provider->success($created_template_id);
                        return new JsonResponse($data, 200);
                    } else {
                        $data = $json_response_data_provider->apiCommunicationError();
                        return new JsonResponse($data, 500);
                    }
                } else {
                    $data = $json_response_data_provider->formError();
                    $template_data_generator->setComEmailTemplate($com_email_template);
                    $form_view =  $this->renderView(
                        'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                        array(
                            'manip_template_form' => $add_template_form->createView(),
                            'current_template_model' => $com_email_template->getTemplateModel(),
                            'template_model_class' => new TemplateModel(),
                            'content_type_class' => new TemplateContentType(),
                            'instantaneous_template_preview' => $template_data_generator
                                ->retrieveContentPartHtml(true, true),
                            'template_logo_alignment_class' => new TemplateLogoAlignment(),
                        )
                    );
                    $data['content'] = $form_view;
                    return new JsonResponse($data, 200);
                }
            }
        }

        return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
    }

    /**
     * @Route(
     *     "/emailling/modeles-emails/edition-modele/{template_id}",
     *     name="admin_communication_emailing_templates_edit_template",
     * )
     */
    public function emailingTemplatesEditTemplateAction(Request $request, $template_id)
    {
        $auth_checker = $this->get('security.authorization_checker');
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (false === $auth_checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $com_email_template = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(
                array(
                    'program' => $program,
                    'id' => $template_id
                )
            );
        if (is_null($com_email_template)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $form_factory = $this->get('form.factory');
        $edit_template_form = $form_factory->createNamed(
            'edit_template_form',
            ComEmailTemplateType::class,
            $com_email_template
        );

        $original_logo_image = $com_email_template->getLogo();
        $original_contents_image = array();
        foreach ($com_email_template->getContents() as $content) {
            $original_contents_image[$content->getId()] = $content->getImage();
        }
        $original_contents = new ArrayCollection();
        foreach ($com_email_template->getContents() as $content) {
            $original_contents->add($content);
        }

        $template_data_generator = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator');
        if ($request->isMethod('GET')) {
            $template_data_generator->setComEmailTemplate($com_email_template);
            $form_view = $this->renderView(
                'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                array(
                    'manip_template_form' => $edit_template_form->createView(),
                    'current_template_model' => $com_email_template->getTemplateModel(),
                    'template_model_class' => new TemplateModel(),
                    'content_type_class' => new TemplateContentType(),
                    'edit_mode' => true,
                    'instantaneous_template_preview' => $template_data_generator
                        ->retrieveContentPartHtml(true, true),
                    'template_logo_alignment_class' => new TemplateLogoAlignment(),
                )
            );
            $data = $json_response_data_provider->success();
            $data['content'] = $form_view;
            return new JsonResponse($data, 200);
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has("edit_template_form")) {
                $edit_template_form->handleRequest($request);
                if ($edit_template_form->isSubmitted() && $edit_template_form->isValid()) {
                    $com_email_template_data_sync = $this->get('AdminBundle\Service\DataSynchronizer\ComEmailTemplateDataSynchronizer');
                    $edit_result = $com_email_template_data_sync->editTemplate(
                        $com_email_template,
                        $this->getUser(),
                        $original_contents,
                        $original_logo_image,
                        $original_contents_image,
                        $edit_template_form->get('delete_logo_image_command')->getData(),
                        $edit_template_form->get('delete_contents_image_command')->getData()
                    );

                    if ($edit_result) {
                        $data = $json_response_data_provider->success();
                        return new JsonResponse($data, 200);
                    } else {
                        $data = $json_response_data_provider->apiCommunicationError();
                        return new JsonResponse($data, 500);
                    }
                } else {
                    $data = $json_response_data_provider->formError();
                    $template_data_generator->setComEmailTemplate($com_email_template);
                    $form_view =  $this->renderView(
                        'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                        array(
                            'manip_template_form' => $edit_template_form->createView(),
                            'current_template_model' => $com_email_template->getTemplateModel(),
                            'template_model_class' => new TemplateModel(),
                            'content_type_class' => new TemplateContentType(),
                            'edit_mode' => true,
                            'instantaneous_template_preview' => $template_data_generator
                                ->retrieveContentPartHtml(true, true),
                            'template_logo_alignment_class' => new TemplateLogoAlignment(),
                        )
                    );
                    $data['content'] = $form_view;
                    return new JsonResponse($data, 200);
                }

            }
        }

        return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
    }

    /**
     * @Route(
     *     "/emailling/modeles-emails/previsulisation-modele/{template_id}",
     *     name="admin_communication_emailing_templates_preview_template",
     *     requirements={"template_id": "\d+"}
     * )
     */
    public function emailingTemplatesPreviewTemplateAction(Request $request, $template_id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $com_email_template = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(
                array(
                    'program' => $program,
                    'id' => $template_id
                )
            );
        if (is_null($com_email_template)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        if ($request->isMethod('GET')) {
            $view = $this->renderView(
                'AdminBundle:EmailTemplates/Communication:template_content.html.twig',
                array(
                    'com_email_template' => $com_email_template,
                    'template_model_class' => new TemplateModel(),
                    'template_logo_alignment_class' => new TemplateLogoAlignment(),
                    'content_type_class' => new TemplateContentType(),
                    'preview_mode' => true
                )
            );
            $data = $json_response_data_provider->success();
            $data['content'] = $view;
            return new JsonResponse($data, 200);
        }

        return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
    }


    /**
     * @Route("/emailing/campagne/preview",name="admin_communication_emailing_campagne_preview_template")
     */
    public function previewCampagneTplAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        if ($request->isMethod('POST')) {
            $UrlTpl = $request->get('urlTpl');
            if (is_null($UrlTpl) || empty($UrlTpl)) {
                return new Response('', 404);
            }
            $Contents = file_get_contents($UrlTpl);
            return new Response($Contents);
        }

        return new Response('');
    }


    /**
     * @Route(
     *     "/emailling/templates/duplication-template/{template_id}",
     *     name="admin_communication_emailing_templates_duplicate_template",
     *     requirements={"template_id": "\d+"},
     *     defaults={"template_id"=null}
     * )
     */
    public function emailingTemplatesDuplicateTemplateAction(Request $request, $template_id)
    {
        $auth_checker = $this->get('security.authorization_checker');
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (false === $auth_checker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
//            return $this->redirectToRoute('fos_user_security_logout');
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
//            return $this->redirectToRoute('fos_user_security_logout');
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $com_email_template = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(array(
                'id' => $template_id,
                'program' => $program,
            ));

        if (is_null($com_email_template)) {
//            return $this->createNotFoundException(self::TEMPLATE_NOT_FOUND_MESSAGE);
            $data = $json_response_data_provider->pageNotFound();
            $data['message'] = self::TEMPLATE_NOT_FOUND_MESSAGE;
            return new JsonResponse($data, 404);
        }

        $template_duplicator = $this->get('AdminBundle\Service\DataDuplicator\ComEmailTemplateDuplicator');
        $new_name = $template_duplicator->generateTemplateName($program, $com_email_template->getName());

        $form_factory = $this->get('form.factory');
        $duplication_data = new ComEmailTemplateDuplicationData($em);
        $duplication_data->setDuplicationSourceId($com_email_template->getId())
            ->setName($new_name);
        $duplicate_template_form = $form_factory->createNamed(
            'duplicate_template_form',
            DuplicationForm::class,
            $duplication_data
        );

        if ($request->isMethod('GET')) {
            $view = $this
                ->renderView('AdminBundle:Communication/EmailingTemplates:duplicate_template.html.twig', array(
                    'duplicate_template_form' => $duplicate_template_form->createView(),
                ));
            $data = $json_response_data_provider->success();
            $data['content'] = $view;
            return new JsonResponse($data, 200);
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has("duplicate_template_form")) {
                $duplicate_template_form->handleRequest($request);
                if ($duplicate_template_form->isSubmitted() && $duplicate_template_form->isValid()) {
                    if ($template_id == $duplication_data->getDuplicationSourceId()) {
                        $template_duplicator
                            ->duplicate($program, $com_email_template, $this->getUser(), $duplication_data->getName());
                        $data = $json_response_data_provider->success();
                        return new JsonResponse($data, 200);
                    }
                } else {
                    $data = $json_response_data_provider->formError();
                    $view = $this
                        ->renderView('AdminBundle:Communication/EmailingTemplates:duplicate_template.html.twig', array(
                            'duplicate_template_form' => $duplicate_template_form->createView(),
                        ));
                    $data['content'] = $view;
                    return new JsonResponse($data, 200);
                }
            }
        }

        /*$template_duplicator = $this->get('AdminBundle\Service\DataDuplicator\ComEmailTemplateDuplicator');
        $template_duplicator->duplicate($program, $com_email_template, $this->getUser());*/

        return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
    }

    /**
     * @Route(
     *     "/emailing/templates/delete-template/{template_id}",
     *     name="admin_communication_emailing_templates_delete_template",
     * )
     */
    public function emailingTemplateDeleteTemplateAction(Request $request, $template_id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $com_email_template = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(array(
                'id' => $template_id,
                'program' => $program,
            ));

        if (!is_null($com_email_template)) {
            $com_email_template_data_sync = $this
                ->get('AdminBundle\Service\DataSynchronizer\ComEmailTemplateDataSynchronizer');
            $delete_res = $com_email_template_data_sync->deleteTemplate($com_email_template);
            if (true == $delete_res) {
                $data = $json_response_data_provider->success();
                return new JsonResponse($data, 200);
            } else {
                $data = $json_response_data_provider->apiCommunicationError();
                return new JsonResponse($data, 500);
            }
        }

        return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
    }
	
	/**
     * @Route(
     *     "/emailing/liste-contact/{trie}",
     *     name="admin_communication_emailing_list_contact",
     * )
     */
    public function emailingListeContactAction(Request $request, $trie){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
		
		if(empty($trie) || is_null($trie)){
			return $this->redirectToRoute('admin_communication_emailing_list_contact', array('trie' => 'recents'));
		}

        $em = $this->getDoctrine()->getManager();
		
		//Call ContactList manager service
		$AllContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
		
		//Get all contacts Lists
		$ListContact = $AllContactList->getAllList();
		
		// Obtient une liste de colonnes
		foreach ($ListContact as $key => $row) {
			$Name[$key]  = $row['Name'];
			$CreatedAt[$key]  = $row['CreatedAt'];
		}
		
		if($trie == 'a-z'){
			array_multisort($Name, SORT_ASC, $ListContact);
		}elseif($trie == 'z-a'){
			array_multisort($Name, SORT_DESC, $ListContact);
		}elseif($trie == 'recents'){
			array_multisort($CreatedAt, SORT_DESC, $ListContact);
		}
		
		return $this->render('AdminBundle:Communication:emailing_liste_contact.html.twig', array(
			'ListContact' => $ListContact,
			'trie' => $trie
		));
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-edit",
     *     name="admin_communication_emailing_list_contact_edition",
     * )
     */
    public function emailingListeContactEditAction(Request $request){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$IdList = $request->get('IdList');
			$response = $this->forward('AdminBundle:PartialPage:emailingListeContactEditAjax', array('IdList' => $IdList));
			return new Response($response->getContent());
		}
		
		//return $this->render('AdminBundle:Communication:emailing_liste_contact_edit.html.twig');
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-edit-submit",
     *     name="admin_communication_emailing_list_contact_edition_submit",
     * )
     */
    public function emailingListeContactEditSubmitAction(Request $request){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$IdList = $request->get('IdList');
			$UserId = $request->get('UserId');
			
			$response = $this->forward('AdminBundle:PartialPage:emailingListeContactEditSubmitAjax', array(
				'IdList' => $IdList,
				'UserId' => $UserId,
			));
		
			return new Response($response->getContent());
		}
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-creer",
     *     name="admin_communication_emailing_list_contact_creation",
     * )
     */
    public function emailingListeContactCreerAction(Request $request){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
		
		$response = $this->forward('AdminBundle:PartialPage:emailingListeContactCreerAjax');
		
		return new Response($response->getContent());
		
		//return $this->render('AdminBundle:Communication:emailing_liste_contact_edit.html.twig');
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-creer-submit",
     *     name="admin_communication_emailing_list_contact_creation_submit",
     * )
     */
    public function emailingListeContactCreerSubmitAction(Request $request){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$ListName = $request->get('ListName');
			$UserId = $request->get('UserId');
			
			$response = $this->forward('AdminBundle:PartialPage:emailingListeContactCreerSubmitAjax', array(
				'ListName' => $ListName,
				'UserId' => $UserId,
			));
		
			return new Response($response->getContent());
		}
	}
	
	
	/**
     * @Route(
     *     "/emailing/liste-contact-delete",
     *     name="admin_communication_emailing_list_contact_delete",
     * )
     */
    public function emailingListeContactDeleteAction(Request $request){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$IdList = $request->get('IdList');
			
			$response = $this->forward('AdminBundle:PartialPage:emailingListeContactDeleteAjax', array(
				'IdList' => $IdList
			));
		
			return new Response($response->getContent());
		}
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-dupliquer",
     *     name="admin_communication_emailing_list_contact_dupliquer",
     * )
     */
    public function emailingListeContactDupliquerAction(Request $request){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$ListName = $request->get('ListName');
			$ListId = $request->get('ListId');
			
			$response = $this->forward('AdminBundle:PartialPage:emailingListeContactDupliquerAjax', array(
				'ListName' => $ListName,
				'ListId' => $ListId
			));
		
			return new Response($response->getContent());
		}
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-export/{id}",
     *     name="admin_communication_emailing_list_contact_export"),
     *     requirements={"id": "\d+"}
     * 
     */
    public function emailingListeContactExportAction($id){
		$json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
		$program = $this->container->get('admin.program')->getCurrent();
		
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
		
        $em = $this->getDoctrine()->getManager();
		
		$em = $this->getDoctrine()->getManager();
	
		//Call ContactList manager service
		$ContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

		// ask the service for a Excel5
		$objPHPExcel = $this->get('phpexcel')->createPHPExcelObject();

		$objPHPExcel->getProperties()->setCreator('CloudRewards');
		$objPHPExcel->getProperties()->setLastModifiedBy('CloudRewards');
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Listing");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Listing");
		$objPHPExcel->getProperties()->setDescription("Listing for Office 2007 XLSX, generated using Symfony.");
		
		$bordersarray = array(
				'borders'=>array(
				'top'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN), 
				'left'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN),
				'right'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN),
				'bottom'=>array('style'=>\PHPExcel_Style_Border::BORDER_THIN)
			)
		);
		
		$objPHPExcel->getActiveSheet()->SetCellValue('A3','prénom');
		$objPHPExcel->getActiveSheet()->SetCellValue('B3','nom');
		$objPHPExcel->getActiveSheet()->SetCellValue('C3','adresse e-mail');
		$objPHPExcel->getActiveSheet()->SetCellValue('D3','rôle');
		$objPHPExcel->getActiveSheet()->SetCellValue('E3','désabonné(e)');
		
		$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->applyFromArray(array('bold'=>true,'size'=>12,'name' => 'Arial','color' => array('rgb' => '404040')));
		
		$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($bordersarray);
		$objPHPExcel->getActiveSheet()->getStyle('B3')->applyFromArray($bordersarray);
		$objPHPExcel->getActiveSheet()->getStyle('C3')->applyFromArray($bordersarray);
		$objPHPExcel->getActiveSheet()->getStyle('D3')->applyFromArray($bordersarray);
		$objPHPExcel->getActiveSheet()->getStyle('E3')->applyFromArray($bordersarray);
		
		$objPHPExcel->getActiveSheet()->getStyle('A:E')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
		
		$objPHPExcel->getActiveSheet()->setTitle('Liste des contacts');
		
		//array de configuration des bordures
		$center = array('alignment'=>array('horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'=>\PHPExcel_Style_Alignment::VERTICAL_CENTER));
			
		//pour aligner à gauche
		$left = array('alignment'=>array('horizontal'=>\PHPExcel_Style_Alignment::HORIZONTAL_LEFT));
			
		//pour souligner
		$souligner = array('font' => array('underline' => \PHPExcel_Style_Font::UNDERLINE_DOUBLE));	
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e4e6f8');

		
		//Get List form ID 
		$ListInfos = $ContactList->getListById($id);
		
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($bordersarray);
		$objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($bordersarray);
		$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('e4e6f8');
		$objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFont()->applyFromArray(array('bold'=>true,'size'=>12,'name' => 'Arial','color' => array('rgb' => '404040')));
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', $ListInfos[0]['Name']);
		$objPHPExcel->getActiveSheet()->SetCellValue('B1', $ListInfos[0]['SubscriberCount'].' contacts');
		
		//Get All contact By ListName 
		$ContactsInfos = $ContactList->getAllContactByName($ListInfos[0]['Name']);
		
		$cpt = 1;
		$i = 4;
		foreach($ContactsInfos as $Contacts){
			//Get Contact by ID 
			$ContactsDatas = $ContactList->getContactById($Contacts['ContactID']);
			
			//Get Contact datas in db 
			$UsersListes = $em->getRepository('UserBundle\Entity\User')->findUserByMail($ContactsDatas[0]['Email']);
			
			if(isset($UsersListes[0])){
				$Roles = $UsersListes[0]->getRoles();
				if($Roles[0] != 'ROLE_ADMIN' || $Roles[0] != 'ROLE_SUPERADMIN'){
					//Fill excel 
					
					
					$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $UsersListes[0]->getFirstname());
					$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $UsersListes[0]->getName());
					$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $UsersListes[0]->getEmail());
					
					if($Roles[0] == 'ROLE_MANAGER'){
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, 'manager');
					}elseif($Roles[0] == 'ROLE_COMMERCIAL'){
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, 'commercial');
					}elseif($Roles[0] == 'ROLE_PARTICIPANT'){
						$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, 'participant');
					}
					
					if($Contacts['IsUnsubscribed'] == '1'){
						$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i.'')->getFont()->applyFromArray(array('italic'=>true,'color' => array('rgb' => 'a8a8a8')));
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, 'désabonné(e)');
					}else{
						$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, '');
					}
					
					$cpt++;
					$i++;
				}
			}
		}
		
		
		// create the writer
		$writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');
		
		$RootDir = __DIR__.'/../../../web/emailing/liste-contacts-export';
		if(!file_exists($RootDir)){
			mkdir($RootDir, 0777, true);
		}
		$nameFile = 'export-liste-contact-'.date('YmdHi').'-emailing.xlsx';
		$FileDest = $RootDir.'/'.$nameFile;
		$writer->save($FileDest);
		
		return $this->redirectToRoute('admin_communication_emailing_list_contact_export_download', array('filename' => $nameFile));
	}
	
	/**
     * @Route(
     *     "/emailing/liste-contact-export-download/{filename}",
     *     name="admin_communication_emailing_list_contact_export_download"),
     *     requirements={"filename": ".+"}
     * 
     */
    public function emailingListeContactExportDownloadAction($filename){
		/**
		* $basePath can be either exposed (typically inside web/)
		* or "internal"
		*/
		$basePath = $this->container->getParameter('kernel.root_dir').'/../web/emailing/liste-contacts-export';
		$filePath = $basePath.'/'.$filename;
		
		// check if file exists
		$fs = new FileSystem();
		if (!$fs->exists($filePath)) {
			throw $this->createNotFoundException();
		}


		// prepare BinaryFileResponse
		$response = new BinaryFileResponse($filePath);
		$response->trustXSendfileTypeHeader();
		$response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE,$filename,iconv('UTF-8', 'ASCII//TRANSLIT', $filename));
		return $response;
	}
	
	
	/**
     * @Route(
     *     "/sondage-quiz/{id}",
     *     name="admin_communication_sondage_quiz", defaults={"id"=null}),
     *     requirements={"id": "\d*"}
     */
    public function sondageQuizAction($id, Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		
		$IsSondagesQuiz = false;
		$SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
		if(!isset($SondagesQuizArray[0])){
			$SondagesQuiz = new SondagesQuiz();
		}else{
			$SondagesQuiz = $SondagesQuizArray[0];
			$IsSondagesQuiz = true;
		}
		
		//Formulaire d'ajout/edition sondages/quiz
		$formSondagesQuiz = $this->createForm(SondagesQuizType::class, $SondagesQuiz, array(
            'action' => $this->generateUrl('admin_communication_sondage_quiz'),
            'method' => 'POST',
        ));
		
		
		$formSondagesQuiz->handleRequest($request);
		if ($formSondagesQuiz->isSubmitted() && $formSondagesQuiz->isValid()) {
			$SondagesQuizData = $formSondagesQuiz->getData();
			$SondagesQuizData->setProgram($program);
			$SondagesQuizData->upload($program);
			
			if(!isset($SondagesQuizArray[0])){
				$SondagesQuizData->setDateCreation(new \DateTime());
				$em->persist($SondagesQuizData);
			}
			
			$em->flush();
			return $this->redirectToRoute('admin_communication_sondage_quiz');
		}
		
		//Formulaires questionnaires
		if(!is_null($id)){
			//$SondagesQuizQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($id);
			$SondagesQuizQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findOneBy(array(
			   'id' => $id
			));
		}else{
			$SondagesQuizQuestionnaireInfos = new SondagesQuizQuestionnaireInfos();
		}
		
		$SondagesQuizQuestions = new SondagesQuizQuestions();
		$SondagesQuizReponses = new SondagesQuizReponses();
		$formQuestionnaires = $this->createForm(SondagesQuizQuestionnaireInfosType::class, $SondagesQuizQuestionnaireInfos);
		
		$formQuestionnaires->handleRequest($request);
		if ($formQuestionnaires->isSubmitted() && $formQuestionnaires->isValid()) {
			$SondagesQuizQuestionnaireInfosData = $formQuestionnaires->getData();
			$SondagesQuizQuestionnaireInfosData->setSondagesQuiz($SondagesQuiz);
			if($request->get('btn-publier-sondages-quiz') !== null){
				$SondagesQuizQuestionnaireInfosData->setEstPublier(true);
			}else{
				$SondagesQuizQuestionnaireInfosData->setEstPublier(false);
			}
			$em->persist($SondagesQuizQuestionnaireInfosData);
			foreach($SondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $Questions){
				$Questions->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfosData);
				$em->persist($Questions);
				foreach($Questions->getSondagesQuizReponses() as $Reponses){
					$Reponses->setSondagesQuizQuestions($Questions);
				}
			}
			
			$em->flush();
			return $this->redirectToRoute('admin_communication_sondage_quiz');
		}
		
		$IsBanniere = false;
		$BannierePath = "";
		if(!empty($SondagesQuiz->getPath())){
			$IsBanniere = true;
			$BannierePath = $SondagesQuiz->getPath();
		}
		
		
		//On recupere les questions/reponses
		$QuestionsInfosArray = array();
		if(isset($SondagesQuizArray[0])){
			$QuestionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBySondagesQuiz($SondagesQuizArray[0]);
		}
		

        return $this->render('AdminBundle:Communication:sondage_quiz.html.twig', array(
			'formSondagesQuiz' => $formSondagesQuiz->createView(),
			'formQuestionnaires' => $formQuestionnaires->createView(),
			'IsBanniere' => $IsBanniere,
			'BannierePath' => $BannierePath,
			'IsSondagesQuiz' => $IsSondagesQuiz,
			'program' => $program,
			'QuestionsInfosArray' => $QuestionsInfosArray
		));
	}
	
	
	/**
     * @Route(
     *     "/sondage-quiz/delete-sondages-quiz/sondages-quiz",
     *     name="admin_communication_sondage_quiz_delete")
     * 
     */
    public function sondageQuizDeleteAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		if($request->isMethod('POST')){
			$id = $request->get('Id');
			$QuestionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($id);
			$em->remove($QuestionsInfosArray);
			$em->flush();
		}
		return new Response('ok');
	}
	
	/**
     * @Route(
     *     "/sondage-quiz/delete-sondages-quiz/reponses",
     *     name="admin_communication_sondage_quiz_delete_reponse")
     * 
     */
    public function sondageQuizDeleteReponsesAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		
		if($request->isMethod('POST')){
			$id = $request->get('IdReponses');
			$ReponsesInfos = $em->getRepository('AdminBundle:SondagesQuizReponses')->find($id);
			$em->remove($ReponsesInfos);
			$em->flush();
		}
		return new Response('ok');
	}
	
	/**
     * @Route(
     *     "/sondage-quiz/delete-sondages-quiz/questions",
     *     name="admin_communication_sondage_quiz_delete_questions")
     * 
     */
    public function sondageQuizDeleteQuestionsAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		
		if($request->isMethod('POST')){
			$id = $request->get('IdQuestion');
			$QuestionsInfos = $em->getRepository('AdminBundle:SondagesQuizQuestions')->find($id);
            if (!empty($QuestionsInfos)) {
                $em->remove($QuestionsInfos);
                $em->flush();
               
            }
		}
        return new Response("ok");
		
	}
	
	/**
     * @Route(
     *     "/sondage-quiz/edit-sondages-quiz/{id}",
     *     name="admin_communication_sondage_quiz_edit"),
     *     requirements={"id": "\d+"}
     */
    public function sondageQuizEditAction($id){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		
		//Recuperer le questionnaire 
		$QuestionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($id);
		$formQuestionnaires = $this->createForm(SondagesQuizQuestionnaireInfosType::class, $QuestionsInfosArray);
		
		return $this->render('AdminBundle:Communication:edit_sondage_quiz.html.twig',array(
			'formQuestionnaires' => $formQuestionnaires->createView(),
		));
	}
	
	/**
     * @Route(
     *     "/sondage-quiz/delete-banniere/banniere",
     *     name="admin_communication_sondage_quiz_delete_banniere")
     * 
     */
    public function sondageQuizDeleteBanniereAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
		
		$em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
			if(isset($SondagesQuizArray[0])){
				$SondagesQuiz = $SondagesQuizArray[0];
				$SondagesQuiz->setPath(NULL);
				$em->flush();
				return new Response('ok');
			}else{
				return new Response('error');
			}
		}
	}

    /**
     * @Route("/emailing/sur-mesure", name="admin_communication_emailing_custom")
     */
	public function emailingCustomAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        return $this->render('AdminBundle:Communication:emailing_custom.html.twig');
    }

    /**
     * @Route("/emailing/statistiques", name="admin_communication_statistiques")
     * Call of Mailjet Api and traitement of service statistique
     */
    public function statistiqueshowAction(Request $request)
    {
        $data = [];
        $date = new \DateTime();
        $now = $date->settime(0,0,0)->format("Y-m-d");
        $filters = ["lastactivityat"=>$now];
        $mailjet = $this->get('mailjet.client');
        $response = $mailjet->get(Resources::$Campaignstatistics,['filters' => $filters]);//call of ApiMailjet
        $listsInfoCampaign = $response->getData();
        $data = $this->get('adminBundle.statistique')->getTraitement($listsInfoCampaign); //call of service
        $fromTo = $this->get('adminBundle.statistique')->getContactByCampaign();
        $send = !empty($fromTo)?$fromTo:[];
        return $this->render('AdminBundle:Communication:emailing_statistique_.html.twig',
        [
            "total" => $data["res"]["total"],
            "delivre" => $data["res"]["delivre"],
            "ouvert" => $data["res"]["ouvert"],
            "cliquer" => $data["res"]["cliquer"],
            "bloque" => $data["res"]["bloque"],
            "spam" => $data["res"]["spam"],
            "desabo" => $data["res"]["desabo"],
            "erreur" => $data["res"]["erreur"],
            "fromSend" => $send,
            "json" =>$data["json"]->getContent()
        ]);
    }

    /**
     * News post list controller
     *
     * @param Request $request
     * @param string $post_type_label         news post type : standard or welcoming post (from NewsPostTypeLabel constant)
     * @param boolean $archived_state
     *
     * @return Response
     *
     * @Route(
     *     "/actualites/liste/{post_type_label}/{archived_state}",
     *     defaults={
     *          "post_type_label"=AdminBundle\Component\Post\NewsPostTypeLabel::STANDARD,
     *          "archived_state"=false
     *     },
     *     name="admin_communication_news"
     * )
     */
    public function newsAction(Request $request, $post_type_label, $archived_state)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        if (!in_array($post_type_label, NewsPostTypeLabel::VALID_NEWS_POST_TYPE_LABEL)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
        $news_post_data_linker = $this->get('AdminBundle\Service\DataLinker\NewsPostDataLinker');
        $news_post_list = $news_post_manager->findAll(
            $program,
            $news_post_data_linker->linkTypeLabelToType($post_type_label),
            $archived_state
        );

        $view_options = array(
            'news_post_submission_type_class' => new NewsPostSubmissionType(),
            'news_post_authorization_type_class' => new NewsPostAuthorizationType(),
            'news_post_list' => $news_post_list,
            'post_type_label_class' =>  new NewsPostTypeLabel(),
        );
        if (true == $archived_state) {
            $view_options['archived_state'] = true;
        }
        if (NewsPostTypeLabel::WELCOMING == $post_type_label) {
            $view_options['welcoming_news_post_type'] = true;
        }
        return $this->render('AdminBundle:Communication:news.html.twig', $view_options);
    }

    /**
     * @Route("/emailing/statistiques/filter/date", name="admin_statistiques_filter")
     * @Method({"POST"})
     */
    public function statistiqueFilterDateAction(Request $request)
    {
        $filtre = $request->request->get('filter');
        $mailjet = $this->get('mailjet.client');
        if ($filtre == "Yesterday") {
            $date = new \DateTime();
            $date->modify('-1 day');
            $format= $date->format("Y-m-d");
            $yest = $date->settime(0,0,0)->getTimestamp();
            $filters = ["fromts" => (string)$yest];
            $respons = $mailjet->get(Resources::$Campaignstatistics,['filters' => $filters]);
            $listsInfoCampaignYesterday = $respons->getData();
            if (!empty($listsInfoCampaignYesterday)) {
               foreach ($listsInfoCampaignYesterday as $value) {
                    $dateFiter = new \DateTime($value["LastActivityAt"]);
                    $time= $dateFiter->format("Y-m-d");
                    if ($time == $format) {
                        $listsInfoCampaign[] = $value;
                    }
                }
            }
            $listCampaigns = !empty($listsInfoCampaign)?$listsInfoCampaign:"";
            $allContactSendCampagne = $this->get('adminBundle.statistique')->getContactByPeriode($filtre);
            $info = $this->get('adminBundle.statistique')->getTraitement($listCampaigns);
            $data = [
                    "fromTo"=>$allContactSendCampagne,
                    "info"=>$info,
                    "dataGraph"=>$listsInfoCampaignYesterday
                    ];
        } elseif ($filtre == "last7days" ) {
            $date = new \DateTime();
            $last = $date->modify('-6 day');
            $last7 = $date->settime(0,0,0)->getTimestamp();
            $filters = ["fromts"=>(string)$last7];
            $response7 = $mailjet->get(Resources::$Campaignstatistics,['filters'=>$filters])->getData();
            $allContactSendCampagne7 = $this->get('adminBundle.statistique')->getContactByPeriode($filtre);
            $info = $this->get('adminBundle.statistique')->getTraitement($response7);
            $data = [
                    "fromTo" => $allContactSendCampagne7,
                    "info" => $info,
                    "dataGraph"=>$response7
                    ];

        }
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route(
     *     "/actualites-archivees/liste/{post_type_label}",
     *     defaults={
     *          "post_type_label"=AdminBundle\Component\Post\NewsPostTypeLabel::STANDARD
     *     },
     *     name="admin_communication_news_archived"
     * )
     */
    public function archivedNewsAction(Request $request, $post_type_label)
    {
        return $this->forward('AdminBundle:Communication:news', array(
            'archived_state' => true,
            'post_type_label' => $post_type_label,
        ));
    }

    /**
     * @Route("/actualites/creer", name="admin_communication_news_create")
     */
    public function createNewsAction(Request $request)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $post_type_label = $request->get('news_post_type_label');
        if (is_null($post_type_label)
            || !in_array($post_type_label, NewsPostTypeLabel::VALID_NEWS_POST_TYPE_LABEL)
        ) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $form_generator = $this->get('AdminBundle\Service\FormGenerator\NewsPostFormGenerator');
        $news_post_data_linker = $this->get('AdminBundle\Service\DataLinker\NewsPostDataLinker');
        $news_post_form = $form_generator->generateForCreation(
            $program,
            $news_post_data_linker->linkTypeLabelToType($post_type_label),
            'news_post_form'
        );
        $news_post_form->handleRequest($request);
        if ($news_post_form->isSubmitted() && $news_post_form->isValid()) {
            $submission_type = $request->get('submission_type');
            $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
            if ($news_post_manager->create($news_post_form->getData(), $submission_type)) {
                $data = $json_response_data_provider->success();
                return new JsonResponse($data, 200);
            } else {
                return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
            }
        }
        $content_option = array(
            'news_post_form' => $news_post_form->createView(),
            'news_post_submission_type_class' => new NewsPostSubmissionType(),
        );
        if (NewsPostTypeLabel::WELCOMING == $post_type_label) {
            $content_option['welcoming_news_post_type'] = true;
        }
        $content = $this->renderView('AdminBundle:Communication/News:manip_news.html.twig', $content_option);
        $data = $json_response_data_provider->success();
        if ($news_post_form->isSubmitted() && !$news_post_form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/actualites/editer/{id}", requirements={"id": "\d+"}, name="admin_communication_news_edit")
     */
    public function editNewsAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $news_post = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($news_post)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $post_type_label = $request->get('news_post_type_label');
        if (is_null($post_type_label)
            || !in_array($post_type_label, NewsPostTypeLabel::VALID_NEWS_POST_TYPE_LABEL)
        ) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $form_generator = $this->get('AdminBundle\Service\FormGenerator\NewsPostFormGenerator');
        $news_post_data_linker = $this->get('AdminBundle\Service\DataLinker\NewsPostDataLinker');
        $news_post_form = $form_generator->generateForEdit(
            $news_post,
            $news_post_data_linker->linkTypeLabelToType($post_type_label),
            'news_post_form'
        );
        $news_post_form->handleRequest($request);
        if ($news_post_form->isSubmitted() && $news_post_form->isValid()) {
            $submission_type = $request->get('submission_type');
            $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
            if ($news_post_manager->edit($news_post_form->getData(), $submission_type)) {
                $data = $json_response_data_provider->success();
                return new JsonResponse($data, 200);
            } else {
                return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
            }
        }

        $content_option = array(
            'news_post_form' => $news_post_form->createView(),
            'news_post_submission_type_class' => new NewsPostSubmissionType(),
            'edit_mode' => true,
        );
        if (NewsPostTypeLabel::WELCOMING == $post_type_label) {
            $content_option['welcoming_news_post_type'] = true;
        }
        $content = $this->renderView('AdminBundle:Communication/News:manip_news.html.twig', $content_option);
        $data = $json_response_data_provider->success();
        if ($news_post_form->isSubmitted() && !$news_post_form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);

    }

    /**
     * @Route("/actualites/dupliquer/{id}", requirements={"id": "\d+"}, name="admin_communication_news_duplicate")
     */
    public function duplicateNewsAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $news_post = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($news_post)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $form_generator = $this->get('AdminBundle\Service\FormGenerator\NewsPostFormGenerator');
        $news_post_duplication_form = $form_generator->generateForDuplication($news_post, 'duplicate_news_post_form');
        $news_post_duplication_form->handleRequest($request);
        if ($news_post_duplication_form->isSubmitted() && $news_post_duplication_form->isValid()) {
            if ($news_post->getId() == $news_post_duplication_form->getData()->getDuplicationSourceId()) {
                $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
                if ($news_post_manager->duplicate($news_post, $news_post_duplication_form->getData()->getName())) {
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
                }
            } else {
                return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
            }
        }

        $content = $this->renderView('AdminBundle:Communication/News:duplicate_news.html.twig', array(
            'duplicate_news_post_form' => $news_post_duplication_form->createView()
        ));
        $data = $json_response_data_provider->success();
        if ($news_post_duplication_form->isSubmitted() && !$news_post_duplication_form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/actualites/publier/{id}/{state}",
     * defaults={"state"=true},
     * requirements={"id": "\d+"},
     * name="admin_communication_news_publish")
     */
    public function publishNewsAction(Request $request, $id, $state)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $news_post = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($news_post)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
        $news_post_manager->definePublishedState($news_post, $state);

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/actualites/depublier/{id}", requirements={"id": "\d+"}, name="admin_communication_news_unpublish")
     */
    public function unpublishNewsAction(Request $request, $id)
    {
        return $this->forward('AdminBundle:Communication:publishNews', array(
            'id' => $id,
            'state' => false,
        ));
    }

    /**
     * @Route("/actualites/archiver/{id}/{archived_state}", defaults={"archived_state"=true}, name="admin_communication_news_archive")
     */
    public function archiveNewsAction(Request $request, $id, $archived_state)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $news_post = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($news_post)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
        $news_post_manager->defineArchivedState($news_post, $archived_state);

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/actualites-archivees/restaurer/{id}", name="admin_communication_news_restore")
     */
    public function restoreNewsAction(Request $request, $id)
    {
        return $this->forward('AdminBundle:Communication:archiveNews', array(
            'id' => $id,
            'archived_state' => false,
        ));
    }

    /**
     * @Route("/actualites/supprimer/{id}", name="admin_communication_news_delete")
     */
    public function deleteNewsAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $news_post = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($news_post)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
        $news_post_manager->delete($news_post);

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/actualites/action-de-groupe", name="admin_communication_news_group_action")
     */
    public function processGroupActionNewsAction(Request $request)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $str_news_post_id_list = $request->get('news_post_id_list');
        $grouped_action_type = $request->get('grouped_action_type');

        if (is_null($str_news_post_id_list)
            || is_null($grouped_action_type)
            || !in_array($grouped_action_type, GroupActionType::NEWS_POST_VALID_GROUP_ACTION)
        ) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $news_post_manager = $this->get('AdminBundle\Manager\NewsPostManager');
        $news_post_manager->processGroupAction(
            explode(',', $str_news_post_id_list),
            $grouped_action_type,
            $program
        );

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/actualites/previsualisation/{id}", requirements={"id": "\d+"}, name="admin_communication_news_preview")
     */
    public function previewNewsAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $news_post = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($news_post)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $data = $json_response_data_provider->success();
        $data['content'] = $this->renderView('AdminBundle:Communication/News:preview_news.html.twig', array(
            'news_post' => $news_post
        ));

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/statistique", name="admin_communication_emailing_campaign_statistique")
     * @Method({"POST","GET"})
     */
    public function emailingCampaignStatistiqueAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $typeId = $request->request->get("id")["campaign_id"];
        $typeTitle = $request->request->get("id")["title"];
        $id = !empty($typeId)?$typeId:$request->query->get("id");   
        $title = !empty($typeTitle)?$typeTitle:$request->query->get("title");

        $mailjet = $this->get('mailjet.client');
        $filter = ["campaignid" => $id];
        $campaigns = $mailjet->get(Resources::$Campaign,['filters' => $filter])->getData()[0];
        $results = $this->get('adminBundle.statistique')->getOneCampagne($id);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($results["email"],$request->query->getInt('page', 1),5,
            [
                "id"=> $id,
                "title"=> $title,
                "paramId"=>"id",
                "paramTitle"=>"title"
            ]);
        $view = $this->renderView(
        'AdminBundle:Communication/EmailingTemplates:statistique_campaign.html.twig',
        [
            "date" => $campaigns["CreatedAt"],
            "email" => $campaigns["FromEmail"],
            "fromName" => $campaigns["FromName"],
            "sujet" => $campaigns["Subject"],
            "listContact" => $results["listContact"],
            "status"=> $results["status"],
            "emails" =>$pagination,
            "name" => $results["template"],
            "data" =>$results["data"],
            "title" =>$title,
            "id" => $id
        ]);
        $data['content'] = $view;
        return new JsonResponse($data, 200);
    }

     /**
     * @Route("/emailing/campagne/statistique/filter", name="admin_communication_emailing_campaign_filter")
     * @Method("POST")
     */
    public function emailingCampaignStatistiqueFilterAction(Request $request)
    {
        $id = $request->request->get("id");
        $results = $this->get('adminBundle.statistique')->getOneCampagne($id);
        $data = $results["data"];
        $response = new JsonResponse($data);
        return $response;       

    }

    /**
     * @Route(
     * "/emailing/campagne/statistique/download",
     *  name="admin_communication_emailing_campaign_download",
     *  options = { "expose" = true }  
     * )
     *  
     * @Method({"POST","GET"})
     */
    public function emailingCampaignDownloadAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $id = $request->query->get("id");
        $status = $request->query->get("status");
        $objPHPExcel = $this->get("adminBundle.excel")->generateExcel($id,$status);       
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel5');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'CloudRewards.xls'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response; 
        
    }

    /**
     * @Route("/emailing/campagne/statistique/export", name="admin_communication_emailing_campaign_exports",
     * options = { "expose" = true })  
     * @Method({"POST","GET"})
     */
    public function emailingCampaignExportAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $mailjet = $this->get('mailjet.client');
        $id = $request->query->get("id");

        $filter = ["campaignid" => $id];
        $title = $request->query->get("title");
        $campaigns = $mailjet->get(Resources::$Campaign,['filters' => $filter])->getData()[0];
        $results = $this->get('adminBundle.statistique')->getOneCampagne($id);
        $html = $this->renderView('pdf/template.html.twig',[
            "date" => $campaigns["CreatedAt"],
            "email" => $campaigns["FromEmail"],
            "fromName" => $campaigns["FromName"],
            "sujet" => $campaigns["Subject"],
            "listContact" => $results["listContact"],
            "status"=> $results["status"],
            "emails" =>$results["email"],
            "name" => $results["template"],
            "data" =>$results["data"],
            "title" =>$title
        ]);
        $a_date = new \DateTime();
        $filename ='export_statistique'.$a_date->format('dmY');
        $html2pdf = $this->get('html2pdf_factory')->create();
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML($html);
        $html2pdf->pdf->Output($filename.'.pdf');
    }


    /**
     * Listing e-learning
     *
     * @return Response
     *
     * @Route("/e-learning/liste", name="admin_communication_e_learning")
     */
    public function eLearningAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $e_learning_list = $em->getRepository('AdminBundle\Entity\ELearning')->findBy(
            array('program' => $program),
            array('created_at' => 'DESC')
        );

        return $this->render('AdminBundle:Communication:e_learning.html.twig', array(
            'e_learning_content_type_class' => new ELearningContentType(),
            'e_learning_list' => $e_learning_list,
            'authorization_type_class' => new AuthorizationType(),
        ));
    }

    /**
     * Action to call when creating new e-learning
     * (using AJAX call)
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/e-learning/creer", name="admin_communication_e_learning_create")
     */
    public function createELearningAction(Request $request)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $form_generator = $this->get('AdminBundle\Service\FormGenerator\ELearningFormGenerator');
        $e_learning_form = $form_generator->generateForCreation($program);
        $e_learning_form->handleRequest($request);
        if ($e_learning_form->isSubmitted() && $e_learning_form->isValid()) {
            $submission_type = $request->get('submission_type');
            $e_learning_manager = $this->get('AdminBundle\Manager\ELearningManager');
            if (in_array($submission_type, SubmissionType::VALID_SUBMISSION_TYPE)
                && $e_learning_manager->create($e_learning_form->getData(), $submission_type)
            ) {
                $data = $json_response_data_provider->success();
                return new JsonResponse($data, 200);
            } else {
                return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
            }
         }

        $content = $this->renderView('AdminBundle:Communication/ELearning:manip_e_learning.html.twig', array(
            'e_learning_form' => $e_learning_form->createView(),
            'submission_type_class' => new SubmissionType(),
            'e_learning_content_type_class' => new ELearningContentType(),
        ));
        $data = $json_response_data_provider->success();
        if ($e_learning_form->isSubmitted() && !$e_learning_form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * Action to call when previewing e-learning
     * (using AJAX call)
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     *
     * @Route("/e-learning/previsualisation/{id}", requirements={"id": "\d+"}, name="admin_communication_e_learning_preview")
     */
    public function previewELearningAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $e_learning = $em->getRepository('AdminBundle\Entity\ELearning')->findOneBy(array(
            'id' => $id,
            'program' => $program,
        ));
        if (is_null($e_learning)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $e_learning_manager = $this->get('AdminBundle\Manager\ELearningManager');
        $e_learning_data = $e_learning_manager->retrieveELearningContentData($e_learning);

        $data = $json_response_data_provider->success();
        $data['content'] = $this->renderView('AdminBundle:Communication/ELearning:preview_e_learning.html.twig', array(
            'e_learning' => $e_learning,
            'e_learning_media_contents' => $e_learning_data['media_contents'],
            'e_learning_quiz_contents' => $e_learning_data['quiz_contents'],
            'e_learning_button_content' => $e_learning_data['button_content'],
            'content_type_class' => new ELearningContentType(),
        ));

        return new JsonResponse($data, 200);
    }

    /**
     * Configuring e-learning welcoming banner
     *
     * @return Response
     *
     * @Route("/e-learning/banniere-accueil", name="admin_communication_e_learning_welcoming_banner")
     */
    public function eLearningWelcomingBannerAction()
    {
        return $this->render('AdminBundle:Communication:e_learning_welcoming_banner.html.twig');
    }

    /**
     * @Route("/pre-sondage/liste" ,name="admin_communication_pre_sondage")
     * 
     */
    public function preSondageQuizAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $allData = $manager->getAllSondageQuiz();
        $data = $this->get("AdminBundle\Service\SondageQuiz\Common")->renderToJson($allData);
        return $this->render('AdminBundle:Communication:preSondage.html.twig',["data"=>$allData,"obj"=>$data]);
    }

    /**
     * @Route("/pre-sondage/liste/{archived}",defaults={"archived"= false}, name="admin_communication_pre_archived_sondage")
     *
     */
    public function preSondageQuizArchivedAction(Request $request,$archived)
    {
        $obj = [];
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $status = $request->request->get("statut");
        $manager = $this->get("adminBundle.sondagequizManager");
        $allData = $manager->getAllSondageQuizArchived($status);
        $data = $this->get("AdminBundle\Service\SondageQuiz\Common")->renderToJson($allData);
        $obj = ["data"=>$allData,"dataJson"=>$data];
        
        return $this->render('AdminBundle:Communication:preSondage_archived.html.twig',$obj);
    }

    /**
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/pre-sondage/create",name="admin_communication_pre_sondage_create")
     *    
     */
    public function createPreSondageAction(Request $request,$id = null)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $em = $this->getDoctrine()->getManager();
        $IsSondagesQuiz = false;
        $SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        $roleDefault = $em->getRepository('AdminBundle:Role')->findByProgram($program);
        if(!isset($SondagesQuizArray[0])){
            $SondagesQuiz = new SondagesQuiz();
        }else{
            $SondagesQuiz = $SondagesQuizArray[0];
            $IsSondagesQuiz = true;
        }
        $SondagesQuizQuestionnaireInfos = new SondagesQuizQuestionnaireInfos();
        $formQuestionnaires = $this->createForm(SondagesQuizQuestionnaireInfosType::class, $SondagesQuizQuestionnaireInfos);
        $SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        $QuestionsInfosArray = array();
        if(isset( $SondagesQuizArray[0])){
            $QuestionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBySondagesQuiz($SondagesQuizArray[0]);
        }

        $formQuestionnaires->handleRequest($request);
        if ($formQuestionnaires->isSubmitted() && $formQuestionnaires->isValid()) {
            $SondagesQuizQuestionnaireInfosData = $formQuestionnaires->getData();
            if (empty($SondagesQuizQuestionnaireInfosData->getAuthorizedRole())) {
               $SondagesQuizQuestionnaireInfosData->setAuthorizedRole($roleDefault[0]);
            }
            $SondagesQuizQuestionnaireInfosData->setSondagesQuiz($SondagesQuiz);
            if($request->get("data") == "btn-publier-sondages-quiz"){
                $SondagesQuizQuestionnaireInfosData->setEstPublier(true);
            }else{
                $SondagesQuizQuestionnaireInfosData->setEstPublier(false);
            }

            $em->persist($SondagesQuizQuestionnaireInfosData);
            foreach($SondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $Questions){
                $Questions->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfosData);
                $em->persist($Questions);
                foreach($Questions->getSondagesQuizReponses() as $Reponses){
                    $Reponses->setSondagesQuizQuestions($Questions);
                }
            }            
            $em->flush();
            $data = $json_response_data_provider->success();
            return new JsonResponse($data, 200);          
        }
       
        $content = $this->renderView('AdminBundle:Communication:pre_create_sondage.html.twig', array(
            'formQuestionnaires' => $formQuestionnaires->createView(),
            'IsSondagesQuiz' => $IsSondagesQuiz,
            'program' => $program,
        ));
        $data = $json_response_data_provider->success();
        $data['content'] = $content;
        return new JsonResponse($data, 200);
    }


    /**
     * @Route("/pre-sondage/editer/{id}", requirements={"id": "\d+"}, name="admin_communication_pre_sondage_edit")
     */
    public function editPreSondageAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $em = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->find($id);
        if (empty($editSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $IsSondagesQuiz = false;
        $SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        if(!isset($SondagesQuizArray[0])){
            $SondagesQuiz = new SondagesQuiz();
        }else{
            $SondagesQuiz = $SondagesQuizArray[0];
            $IsSondagesQuiz = true;
        }
        $formQuestionnaires = $this->createForm(SondagesQuizQuestionnaireInfosType::class, $editSondage);
        $SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);

        $formQuestionnaires->handleRequest($request);
        if ($formQuestionnaires->isSubmitted() && $formQuestionnaires->isValid()) {
            $SondagesQuizQuestionnaireInfosData = $formQuestionnaires->getData();
            $SondagesQuizQuestionnaireInfosData->setSondagesQuiz($SondagesQuiz);
            if($request->get("data") == "btn-publier-sondages-quiz"){
                $SondagesQuizQuestionnaireInfosData->setEstPublier(true);
            }else{
                $SondagesQuizQuestionnaireInfosData->setEstPublier(false);
            }
            $em->persist($SondagesQuizQuestionnaireInfosData);
            foreach($SondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $Questions){
                $Questions->setSondagesQuizQuestionnaireInfos($SondagesQuizQuestionnaireInfosData);
                $em->persist($Questions);
                foreach($Questions->getSondagesQuizReponses() as $Reponses){
                    $Reponses->setSondagesQuizQuestions($Questions);
                }
            }            
            $em->flush();
            $data = $json_response_data_provider->success();
            return new JsonResponse($data, 200);          
        }
        
        $content = $this->renderView('AdminBundle:Communication:pre_create_sondage.html.twig', array(
            'formQuestionnaires' => $formQuestionnaires->createView(),
            'program' => $program,
            'edit'=> true
        ));
        $data = $json_response_data_provider->success();
        $data['content'] = $content;
        return new JsonResponse($data, 200);

    }

    /**
     * @Route("/pre-sondage/dupliquer/{id}", requirements={"id": "\d+"}, name="admin_communication_pre_sondage_duplicate")
     */
    public function duplicatPreSondageAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $em = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($editSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
    }

    /**
     * @Route("/pre-sondage/publier/{id}/{state}",
     * defaults={"state"=true},
     * requirements={"id": "\d+"},
     * name="admin_communication_pre_sondage_publier")
     */
    public function publishedPreSondageAction(Request $request, $id, $state)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $em = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($editSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $data = $manager->renderToPublished($editSondage,$state);
        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/archiver/{id}/{archived}", defaults={"archived"=true}, name="admin_communication_pre_sondage_archive")
     */
    public function archivePreSondageAction(Request $request, $id, $archived)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($editSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $data = $manager->renderToArchived($editSondage,$archived);
        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/groupe", name="admin_communication_pre_sondage_group_action")
     */
    public function groupPreSondageAction(Request $request)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $idList = $request->get('id_list');
        $actionType = $request->get('action_type');

        if (is_null($idList)
            || is_null($actionType)
            || !in_array($actionType, GroupActionType::NEWS_POST_VALID_GROUP_ACTION)
        ) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $manager = $this->get("adminBundle.sondagequizManager");
        $manager->groupAction(
            explode(',', $idList),
            $actionType,
            $program
        );

        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route(
     *     "/pre-sondage/archivees/liste",name="admin_communication_pre_sondage_news_archived")
     */
    public function archivedListePreSondageAction(Request $request)
    {
        return $this->forward('AdminBundle:Communication:preSondageQuizArchived', array(
            'archived' => true,
        ));
    }

    /**
     * @Route("/pre-sondage/archivees/restaurer/{id}", name="admin_communication_presondage_restore")
     */
    public function restorePreSondageAction(Request $request, $id)
    {
        return $this->forward('AdminBundle:Communication:archivePreSondage', array(
            'id' => $id,
            'archived' => false,
        ));
    }

    /**
     * @Route("/pre-sondage/dupliquer/{id}", requirements={"id": "\d+"}, name="admin_communication_pre_sondage_duplicate")
     */
    public function duplicatePreSondageAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $dataSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($editSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $formBuilder = $this->get("AdminBundle\Service\SondageQuiz\Common");
        $form = $formBuilder->generateForm($dataSondage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($dataSondage->getId() == $form->getData()->getDuplicationSourceId()) {
                $manager = $this->get("adminBundle.sondagequizManager");
                if ($manager->duplicate($dataSondage, $form->getData()->getName())) {
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
                }
            } else {
                return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
            }
        }

        $content = $this->renderView('AdminBundle:Communication/News:duplicate_news.html.twig', array(
            'duplicate_sondage_quiz_form' => $form->createView()
        ));//reste method duplicate and change duplicate.html.twig
        $data = $json_response_data_provider->success();
        if ($form->isSubmitted() && !$form->isValid()) {
            $data = $json_response_data_provider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);

    }

    /**
     * @Route("/pre-sondage/supprimer/{id}", name="admin_communication_pre_sondage_delete")
     */
    public function deletePreSondageAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $sondageQuiz = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->find($id);
        if (empty($sondageQuiz)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $manager = $this->get("adminBundle.sondagequizManager");
        $data = $manager->delete($sondageQuiz);
        return new JsonResponse($json_response_data_provider->success(), 200);

    }

    /**
     * @Route("/pre-sondage/cloture/{id}", name="admin_communication_pre_sondage_cloture")
     */
    public function cloturedPreSondageAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $clotureSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($clotureSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $data = $manager->renderToCloture($clotureSondage);
        return new JsonResponse($json_response_data_provider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/statistiques/{id}", name="admin_communication_pre_sondage_stat")
     */
    public function statistiquesPreSondageAction(Request $request, $id)
    {
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $em = $this->getDoctrine()->getManager();
        $manager = $this->get("adminBundle.sondagequizManager");
        $statSondage = $manager->getElementStatistique($id);
        dump($statSondage);
        if (empty($statSondage)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $content = $this->renderView('AdminBundle:Communication:statistique_sondage.html.twig', array(
            'data' => $statSondage['questions'],
        ));
        $data = $json_response_data_provider->success();
        $data['content'] = $content;
        return new JsonResponse($data, 200);
    }

}
