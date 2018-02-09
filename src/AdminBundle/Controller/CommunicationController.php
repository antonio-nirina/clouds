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

use \Mailjet\Resources;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Filesystem\Filesystem;
use AdminBundle\Service\Statistique\Common;

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
     * @Route("/edito", name="admin_communication_editorial")
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
     * @Route(
     *     "/edito/suppression/{id}",
     *     name="admin_communication_editorial_delete"),
     *     requirements={"id": "\d+"}
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
        ));
    }

    /**
     * @Route("/emailing/campagne/new", name="admin_communication_emailing_compaign_new")
     * @Method("POST")
     */
    public function emailingCampaignNewAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\CampaignDataProvider');
        if (empty($program)) {
            return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
        }

        $campaign_draft_data = new CampaignDraftData();
        $campaign_draft_data->setProgrammedLaunchDate(new \DateTime('now'));
        $campaign_draft_form = $this->createForm(CampaignDraftType::class, $campaign_draft_data);
        $campaign_draft_form->handleRequest($request);
        if ($campaign_draft_form->isSubmitted() && $campaign_draft_form->isValid()) {
            $campaign_handler = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
            if ($campaign_handler->createAndProcess($campaign_draft_data)) {
                $data = $json_response_data_provider->success();
                return new JsonResponse($data, 200);
            } else {
                $data = $json_response_data_provider->campaignSendingError();
                return new JsonResponse($data, 200);
            }
        }

        $template_manager = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $template_list = $template_manager->listSortedTemplate($program);
        $template_list_data_handler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $template_data_list = $template_list_data_handler->retrieveListDataIndexedById($template_list);

        $view = $this->renderView('AdminBundle:Communication:emailing_campaign_new.html.twig', array(
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
	public function PreviewCampagneTplAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }
		
		$em = $this->getDoctrine()->getManager();
		
		if ($request->isMethod('POST')) {
			$UrlTpl = $request->get('urlTpl');
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
     *     "/sondage-quiz/",
     *     name="admin_communication_sondage_quiz")
     * 
     */
    public function sondageQuizAction(Request $request){
		$program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        return $this->render('AdminBundle:Communication:sondage_quiz.html.twig');
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
        $data=[];
        $date=new \DateTime();
        $now=$date->settime(0,0,0)->format("Y-m-d");
        $filters=["lastactivityat"=>$now];
        $mailjet=$this->get('mailjet.client');
        $response = $mailjet->get(Resources::$Campaignstatistics,['filters' => $filters]);//call of ApiMailjet
        $listsInfoCampaign=$response->getData();
        $data=$this->get('adminBundle.statistique')->getTraitement($listsInfoCampaign); //call of service
        $fromTo=$this->get('adminBundle.statistique')->getContactByCampaign();
        return $this->render('AdminBundle:Communication:emailing_statistique_.html.twig',
        [
            "total"=>$data["total"],
            "delivre"=>$data["delivre"],
            "ouvert"=>$data["ouvert"],
            "cliquer"=>$data["cliquer"],
            "bloque"=>$data["bloque"],
            "spam"=>$data["spam"],
            "desabo"=>$data["desabo"],
            "erreur"=>$data["erreur"],
            "fromSend"=>$fromTo["send"][0],
            "mailTo"=>$fromTo["email"]
        ]);
    }

    /**
     * @Route("/emailing/statistiques/filter/date", name="admin_statistiques_filter")
     * @Method({"POST"})
     */
    public function statistiqueFilterDateAction(Request $request)
    {
        $filtre=$request->request->get('filter');
        $mailjet=$this->get('mailjet.client');
        $date = new \DateTime();
        if ($filtre == "Yesterday") {
            $date->modify('-1 day');
            $format= $date->format("Y-m-d");
            $yest = $date->settime(0,0,0)->getTimestamp();
            $filters = ["fromts"=>(string)$yest];
            $respons = $mailjet->get(Resources::$Campaignstatistics,['filters'=>$filters]);
            $allContactSendCampagne = $this->get('adminBundle.statistique')->getContactByPeriode($filtre);
            $listsInfoCampaignYesterday = $respons->getData();
            foreach ($listsInfoCampaignYesterday as $value) {
                $dateFiter = new \DateTime($value["LastActivityAt"]);
                $time= $dateFiter->format("Y-m-d");
                if ($time == $format) {
                    $listsInfoCampaign[] = $value;
                }
               
            }
            $info = $this->get('adminBundle.statistique')->getTraitement($listsInfoCampaign);
            $data = [
                    "fromTo"=>$allContactSendCampagne,
                    "info"=>$info
                    ]; 
        }        
        $response=new JsonResponse($data);
        return $response;
    }
}
