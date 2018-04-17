<?php

namespace AdminBundle\Controller;

use AdminBundle\Component\CommunicationEmail\TemplateContentType;
use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use AdminBundle\Component\CommunicationEmail\TemplateModel;
use AdminBundle\Component\CommunicationEmail\TemplateSortingParameter;
use AdminBundle\Component\Slide\SlideType;
use AdminBundle\DTO\CampaignDraftData;
use AdminBundle\DTO\ComEmailTemplateDuplicationData;
use AdminBundle\DTO\DuplicationData;
use AdminBundle\Form\CampaignDraftType;
use AdminBundle\Form\ComEmailTemplateType;
use AdminBundle\Form\ELearningHomeBannerType;
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
    const SIDEBAR_VIEW               = 'AdminBundle:Communication:menu_sidebar_communication.html.twig';
    const TEMPLATE_NOT_FOUND_MESSAGE = 'Modèle non trouvé';

    /**
     *
     */
    public function __construct()
    {
        $this->activeMenuIndex = 3;
        $this->sidebarView     = self::SIDEBAR_VIEW;
    }

    /**
     * @Route("/slideshow", name="admin_communication_slideshow")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function slideshowAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $homePageData = $program->getHomePageData();
        if (is_null($homePageData)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $slideshowManager    = $this->container->get('admin.slideshow');
        $originalSlides      = $slideshowManager->getOriginalSlides($homePageData);
        $originalSlidesImage = $slideshowManager->getOriginalSlidesImage($originalSlides);

        $formFactory           = $this->get('form.factory');
        $homePageSlideDataForm = $formFactory->createNamed(
            'home_page_slide_data_form',
            HomePageSlideDataType::class,
            $homePageData
        );
        if ("POST" === $request->getMethod()) {
            if ($request->request->has('home_page_slide_data_form')) {
                $homePageSlideDataForm->handleRequest($request);
                if ($homePageSlideDataForm->isSubmitted() && $homePageSlideDataForm->isValid()) {
                    // checking for "delete image" commands
                    $deletedImageSlideIdList = $slideshowManager->checkDeletedImages(
                        $homePageSlideDataForm,
                        $homePageData,
                        $originalSlidesImage
                    );
                    // editing existant slide
                    $homePageData            = $slideshowManager->editHomePageSlides(
                        $homePageData,
                        $deletedImageSlideIdList,
                        $originalSlidesImage
                    );
                    // deleting slides
                    $homePageData            = $slideshowManager->deleteHomePageSlides(
                        $homePageData,
                        $originalSlides
                    );
                    // adding new slide
                    $homePageData            = $slideshowManager->addNewHomePageSlides($homePageData);
                    $slideshowManager->save();

                    return $this->redirectToRoute('admin_communication_slideshow');
                }
            }
        }

        return $this->render(
            'AdminBundle:Communication:slideshow.html.twig',
            array(
                'home_page_slide_data_form' => $homePageSlideDataForm->createView(),
                'original_slides_image' => $originalSlidesImage,
                'slide_type' => new SlideType(),
                )
        );
    }

    /**
     * @Route("/emailing/campagne", name="admin_communication_emailing_compaign")
     * @return type Description
     */
    public function emailingCampaignAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $campaign         = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $filters          = array('Limit' => 0);
        $campaignDataList = $campaign->getAllVisibleWithData($filters);

        return $this->render(
            'AdminBundle:Communication:emailing_campaign.html.twig',
            array(
                "list" => $campaignDataList,
                'content_type_class' => new TemplateContentType(),
                'template_model_class' => new TemplateModel(),
                'campaign_draft_creation_mode_class' => new CampaignDraftCreationMode(),
            )
        );
    }

    /**
     * @Route("/emailing/campagne/new",
     * name="admin_communication_emailing_compaign_new")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignNewAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\CampaignDataProvider');
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $creationMode = $request->get('creation_mode');
        if (is_null($creationMode)) {
            $creationMode = CampaignDraftCreationMode::NORMAL;
        }
        if (!in_array($creationMode, CampaignDraftCreationMode::VALID_CREATION_MODE)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $validationGroups = array('normal_creation_mode');
        if (CampaignDraftCreationMode::BY_HALT == $creationMode) {
            $validationGroups = array('Default');
        }

        $campaignDraftData = new CampaignDraftData();
        $campaignDraftData->setProgrammedLaunchDate(new \DateTime('now'));

        $campaignDraftForm = $this->createForm(
            CampaignDraftType::class,
            $campaignDraftData,
            array('validation_groups' => $validationGroups)
        );

        $campaignDraftForm->handleRequest($request);
        if ($campaignDraftForm->isSubmitted() && $campaignDraftForm->isValid()) {
            $campaignHandler = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
            if (CampaignDraftCreationMode::NORMAL == $creationMode) {
                if ($campaignHandler->createAndProcess($campaignDraftData)) {
                    $data = $jsonResponseDataProvider->success();

                    return new JsonResponse($data, 200);
                } else {
                    $data = $jsonResponseDataProvider->campaignSendingError();

                    return new JsonResponse($data, 200);
                }
            } elseif (CampaignDraftCreationMode::BY_HALT == $creationMode) {
                if (!is_null($campaignHandler->createCampaignDraftByHalt($campaignDraftData))) {
                    $data = $jsonResponseDataProvider->success();

                    return new JsonResponse($data, 200);
                } else {
                    $data = $jsonResponseDataProvider->campaignDraftCreationError();

                    return new JsonResponse($data, 200);
                }
            }
        }

        $templateManager         = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $templateList            = $templateManager->listSortedTemplate($program);
        $templateListDataHandler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $templateDataList        = $templateListDataHandler->retrieveListDataIndexedById($templateList);

        $view = $this->renderView(
            'AdminBundle:Communication/EmailingCampaign:manip_campaign.html.twig',
            array(
            'campaign_draft_form' => $campaignDraftForm->createView(),
            'template_data_list' => $templateDataList,
            )
        );
        $data = $jsonResponseDataProvider->success();
        if ($campaignDraftForm->isSubmitted() && !$campaignDraftForm->isValid()) {
            $data = $jsonResponseDataProvider->formError();
        }
        $data['content'] = $view;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/editer/{campaign_draft_id}",
     *  name="admin_communication_emailing_campaign_edit")
     * @param Request $request           Description
     * @param type    $campaign_draft_id Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignEditAction(Request $request, $campaign_draft_id)
    {
        $program = $this->container->get('admin.program')->getCurrent();

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\CampaignDataProvider');
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $editMode = $request->get('editMode');
        if (is_null($editMode)) {
            $editMode = CampaignDraftCreationMode::NORMAL;
        }
        if (!in_array($editMode, CampaignDraftCreationMode::VALID_CREATION_MODE)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $validationGroups = array('normal_creation_mode');
        if (CampaignDraftCreationMode::BY_HALT == $editMode) {
            $validationGroups = array('Default');
        }

        $campaignHandler   = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $campaignDraftData = $campaignHandler->findCampaignDraftAsDTO($campaign_draft_id);
        $campaignDraftForm = $this->createForm(
            CampaignDraftType::class,
            $campaignDraftData,
            array('validation_groups' => $validationGroups)
        );
        $campaignDraftForm->handleRequest($request);
        if ($campaignDraftForm->isSubmitted() && $campaignDraftForm->isValid()) {
            if (CampaignDraftCreationMode::NORMAL == $editMode) {
                if ($campaignHandler->editAndProcess($campaignDraftData)) {
                    $data = $jsonResponseDataProvider->success();

                    return new JsonResponse($data, 200);
                } else {
                    $data = $jsonResponseDataProvider->campaignSendingError();

                    return new JsonResponse($data, 200);
                }
            } elseif (CampaignDraftCreationMode::BY_HALT == $editMode) {
                if ($campaignHandler->editCampaignDraftByHalt($campaignDraftData)) {
                    $data = $jsonResponseDataProvider->success();

                    return new JsonResponse($data, 200);
                } else {
                    $data = $jsonResponseDataProvider->campaignDraftEditError();

                    return new JsonResponse($data, 200);
                }
            }
        }

        $templateManager         = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $templateList            = $templateManager->listSortedTemplate($program);
        $templateListDataHandler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $templateDataList        = $templateListDataHandler->retrieveListDataIndexedById($templateList);
        $view                    = $this->renderView(
            'AdminBundle:Communication/EmailingCampaign:manip_campaign.html.twig',
            array(
            'campaign_draft_form' => $campaignDraftForm->createView(),
            'template_data_list' => $templateDataList,
            'edit_mode' => true,
            )
        );

        $data = $jsonResponseDataProvider->success();
        if ($campaignDraftForm->isSubmitted() && !$campaignDraftForm->isValid()) {
            $data = $jsonResponseDataProvider->formError();
        }
        $data['content'] = $view;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/filter", name="admin_communication_emailing_compaign_filter")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignFilterAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $status   = $request->get('status');
        $campaign = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');

        $viewOptions = array();
        if (!is_null($request->get('archived_campaign_mode')) && 'true' == $request->get('archived_campaign_mode')
        ) {
            $campaignDataList             = $campaign->getAllArchivedWithDataFiltered($status);
            $viewOptions['archived_mode'] = true;
        } else {
            $campaignDataList = $campaign->getAllVisibleWithDataFiltered($status);
        }
        $viewOptions['list'] = $campaignDataList;

        return $this->render(
            'AdminBundle:Communication:emailing_campaign_filtered.html.twig',
            $viewOptions
        );
    }

    /**
     * @Route("/emailing/campagne/archivees", name="admin_communication_emailing_compaign_archived")
     * @Method("POST")
     * @return type Description
     */
    public function emailingArchivedCampaignAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $campaign = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $filters  = array('Limit' => 0);

        $campaignDataList = $campaign->getAllArchivedWithData($filters);

        return $this->render(
            'AdminBundle:Communication:emailing_campaign_filtered.html.twig',
            array(
                'list' => $campaignDataList,
                'archived_mode' => true,
                )
        );
    }

    /**
     * @Route("/emailing/campagne/archiver", name="admin_communication_emailing_campaign_archive")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignArchiveAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $toArchiveCampaignIds = $request->get('campaign_checked_ids');
        $toArchiveCampaignIds = explode(',', $toArchiveCampaignIds);

        $campaignHandler = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        if (!empty($toArchiveCampaignIds)) {
            $campaignHandler->archiveCampaignDraftByIdList($toArchiveCampaignIds);
        }

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/emailing/campagne/restaurer", name="admin_communication_emailing_campaign_restore_archived")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignRestoreArchivedAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $toRestoreIds = $request->get('campaign_checked_ids');
        $toRestoreIds = explode(',', $toRestoreIds);

        $campaignHandler = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        if (!empty($toRestoreIds)) {
            $campaignHandler->restoreArchivedCampaignDraftByIdList($toRestoreIds);
        }

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/emailing/campagne/dupliquer", name="admin_communication_emailing_campaign_duplicate")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignDuplicateAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $campaignDuplicationSourceId = $request->get('campaign_draft_id');
        $campaignHandler             = $this->get('AdminBundle\Service\MailJet\MailJetCampaign');
        $campaignDuplicationSource   = $campaignHandler->retrieveCampaignDraftById($campaignDuplicationSourceId);
        if (is_null($campaignDuplicationSourceId) || is_null($campaignDuplicationSource)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $duplicationData         = new DuplicationData();
        $duplicationData->setDuplicationSourceId($campaignDuplicationSource['ID'])
            ->setName($campaignDuplicationSource['Title']);
        $campaignDuplicationForm = $this->createForm(
            DuplicationForm::class,
            $duplicationData
        );
        $campaignDuplicationForm->handleRequest($request);

        if ($campaignDuplicationForm->isSubmitted() && $campaignDuplicationForm->isValid()) {
            if ($campaignDuplicationSourceId == $duplicationData->getDuplicationSourceId()) {
                $campaignHandler->duplicateCampaignDraft(
                    $campaignDuplicationSource,
                    $duplicationData->getName()
                );
                $data = $jsonResponseDataProvider->success();

                return new JsonResponse($data, 200);
            }
        }

        $view = $this->renderView(
            'AdminBundle:Communication/EmailingTemplates:duplicate_campaign.html.twig',
            array('duplicate_campaign_form' => $campaignDuplicationForm->createView())
        );

        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $view;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/supprimer", name="admin_communication_emailing_campaign_delete")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignDeleteAction(Request $request)
    {
        $program                  = $this->container->get('admin.program')->getCurrent();
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $toDeleteCampaignIds = explode(
            ',',
            $request->get('campaign_checked_ids')
        );
        $campaignHandler        = $this->container->get('AdminBundle\Service\MailJet\MailJetCampaign');
        if (!empty($toDeleteCampaignIds)) {
            $campaignHandler->deleteCampaignDraftByIdList($toDeleteCampaignIds);
        }

        return new JsonResponse(
            $jsonResponseDataProvider->success(),
            200
        );
    }

    /**
     * @Route("/emailing/campagne/creer-liste-contact", name="admin_communication_emailing_campaign_create_contact_list")
     * @param Request $request Description
     * @Method("POST")
     * @return type Description
     */
    public function emailingCampaignCreateContactList(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\ContactListDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $listName   = $request->get('ListName');
        $userIds    = $request->get('UserId');
        $arrUserIds = explode('##_##', $userIds);

        $em        = $this->getDoctrine()->getManager();
        $usersList = array();
        foreach ($arrUserIds as $userId) {
            $usersList[] = $em->getRepository('UserBundle\Entity\User')->find($userId);
        }
        $contactListHandler = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');
        $response           = $contactListHandler->addContactListReturningInfos(
            $listName,
            $usersList
        );
        if (!empty($response)) {
            return new JsonResponse(
                $jsonResponseDataProvider->contactListCreationSuccess(
                    $response['contact_list_infos']['ID'],
                    ''
                ),
                200
            );
        } else {
            return new JsonResponse(
                $jsonResponseDataProvider->contactListCreationError(),
                200
            );
        }
    }

    /**
     * @Route("/emailing/modeles-emails", name="admin_communication_emailing_templates")
     * @return type Description
     */
    public function emailingTemplatesAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $templateManager         = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $templateList            = $templateManager->listSortedTemplate($program);
        $templateListDataHandler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $templateDataList        = $templateListDataHandler->retrieveListData($templateList);

        return $this->render(
            'AdminBundle:Communication:emailing_templates.html.twig',
            array(
                'template_model_class' => new TemplateModel(),
                'template_data_list' => $templateDataList,
                'content_type_class' => new TemplateContentType(),
                'template_sorting_parameter_class' => new TemplateSortingParameter(),
                'campaign_draft_creation_mode_class' => new CampaignDraftCreationMode(),
            )
        );
    }

    /**
     * @Route(
     *     "/emailing/modeles-emails/tri/{sorting_parameter}",
     *     name="admin_communication_emailing_templates_sort",
     *     defaults={"sorting_parameter"=null})
     * @param type $sorting_parameter Description
     * @return type Description
     */
    public function listSortedEmailingTemplatesAction($sorting_parameter)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $availableSortingParameter = TemplateSortingParameter::AVAILABLE_SORTING_PARAMETERS;
        if (!in_array($sorting_parameter, $availableSortingParameter)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $templateManager         = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
        $templateList            = $templateManager->listSortedTemplate(
            $program,
            $sorting_parameter
        );
        $templateListDataHandler = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateListDataHandler');
        $templateDataList        = $templateListDataHandler->retrieveListData($templateList);

        $templateListView = $this->renderView(
            'AdminBundle:Communication/EmailingTemplates:sorted_emailing_template.html.twig',
            array(
            'template_data_list' => $templateDataList,
            )
        );
        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $templateListView;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route(
     *     "/emailling/modeles-emails/ajout-modele/{model}",
     *     name="admin_communication_emailing_templates_add_template",
     *     defaults={"model"=null}
     * @param Request $request Description
     * @param type    $model   Description
     * @return type Description
     * )
     */
    public function emailingTemplatesAddTemplateAction(Request $request, $model)
    {
        $authChecker = $this->get('security.authorization_checker');

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (false === $authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $validModels = array(TemplateModel::TEXT_AND_IMAGE, TemplateModel::TEXT_ONLY);

        $templateDataInitializer = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateDataInitializer');
        $comEmailTemplate        = $templateDataInitializer->initForNewTemplate();
        $comEmailTemplate->setLogoAlignment(TemplateLogoAlignment::CENTER);
        $formFactory               = $this->get('form.factory');
        if ($request->isMethod('GET')) {
            if (!is_null($model) && in_array($model, $validModels)) {
                $comEmailTemplate->setTemplateModel($model);
            }
        }
        $addTemplateForm = $formFactory->createNamed(
            'add_template_form',
            ComEmailTemplateType::class,
            $comEmailTemplate
        );

        $templateDataGenerator = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator');
        if ($request->isMethod('GET')) {
            if (!is_null($model) && in_array($model, $validModels)) {
                $templateDataGenerator->setComEmailTemplate($comEmailTemplate);
                $formView = $this->renderView(
                    'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                    array(
                    'manip_template_form' => $addTemplateForm->createView(),
                    'current_template_model' => $model,
                    'template_model_class' => new TemplateModel(),
                    'content_type_class' => new TemplateContentType(),
                    'instantaneous_template_preview' => $templateDataGenerator
                        ->retrieveContentPartHtml(true, true),
                    'template_logo_alignment_class' => new TemplateLogoAlignment(),
                    )
                );

                $data            = $jsonResponseDataProvider->success();
                $data['content'] = $formView;

                return new JsonResponse($data, 200);
            } else {
                return new JsonResponse(
                    $jsonResponseDataProvider->pageNotFound(),
                    404
                );
            }
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has("add_template_form")) {
                $addTemplateForm->handleRequest($request);
                if ($addTemplateForm->isSubmitted() && $addTemplateForm->isValid()) {
                    $comEmailTemplateDataSync = $this
                        ->get('AdminBundle\Service\DataSynchronizer\ComEmailTemplateDataSynchronizer');
                    $createdTemplateId          = $comEmailTemplateDataSync->createTemplate(
                        $program,
                        $comEmailTemplate,
                        $this->getUser()
                    );

                    if (!is_null($createdTemplateId)) {
                        $data = $jsonResponseDataProvider->success(
                            $createdTemplateId
                        );

                        return new JsonResponse($data, 200);
                    } else {
                        $data = $jsonResponseDataProvider->apiCommunicationError();

                        return new JsonResponse($data, 500);
                    }
                } else {
                    $data            = $jsonResponseDataProvider->formError();
                    $templateDataGenerator->setComEmailTemplate($comEmailTemplate);
                    $formView       = $this->renderView(
                        'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                        array(
                        'manip_template_form' => $addTemplateForm->createView(),
                        'current_template_model' => $comEmailTemplate->getTemplateModel(),
                        'template_model_class' => new TemplateModel(),
                        'content_type_class' => new TemplateContentType(),
                        'instantaneous_template_preview' => $templateDataGenerator
                            ->retrieveContentPartHtml(true, true),
                        'template_logo_alignment_class' => new TemplateLogoAlignment(),
                        )
                    );
                    $data['content'] = $formView;

                    return new JsonResponse($data, 200);
                }
            }
        }

        return new JsonResponse($jsonResponseDataProvider->pageNotFound(), 404);
    }

    /**
     * @Route(
     *     "/emailling/modeles-emails/edition-modele/{template_id}",
     *     name="admin_communication_emailing_templates_edit_template",
     * @param Request $request     Description
     * @param type    $template_id Description
     * @return type Description
     * )
     */
    public function emailingTemplatesEditTemplateAction(Request $request, $template_id)
    {
        $authChecker = $this->get('security.authorization_checker');

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (false === $authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $em                 = $this->getDoctrine()->getManager();
        $comEmailTemplate = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(
                array(
                'program' => $program,
                'id' => $template_id,
                )
            );
        if (is_null($comEmailTemplate)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }
        $formFactory        = $this->get('form.factory');
        $editTemplateForm = $formFactory->createNamed(
            'edit_template_form',
            ComEmailTemplateType::class,
            $comEmailTemplate
        );

        $originalLogoImage     = $comEmailTemplate->getLogo();
        $originalContentsImage = array();
        foreach ($comEmailTemplate->getContents() as $content) {
            $originalContentsImage[$content->getId()] = $content->getImage();
        }
        $originalContents = new ArrayCollection();
        foreach ($comEmailTemplate->getContents() as $content) {
            $originalContents->add($content);
        }

        $templateDataGenerator = $this->get('AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator');
        if ($request->isMethod('GET')) {
            $templateDataGenerator->setComEmailTemplate($comEmailTemplate);
            $formView = $this->renderView(
                'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                array(
                'manip_template_form' => $editTemplateForm->createView(),
                'current_template_model' => $comEmailTemplate->getTemplateModel(),
                'template_model_class' => new TemplateModel(),
                'content_type_class' => new TemplateContentType(),
                'edit_mode' => true,
                'instantaneous_template_preview' => $templateDataGenerator
                    ->retrieveContentPartHtml(true, true),
                'template_logo_alignment_class' => new TemplateLogoAlignment(),
                )
            );

            $data            = $jsonResponseDataProvider->success();
            $data['content'] = $formView;

            return new JsonResponse($data, 200);
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has("edit_template_form")) {
                $editTemplateForm->handleRequest($request);
                if ($editTemplateForm->isSubmitted() && $editTemplateForm->isValid()) {
                    $comEmailTemplateDataSync = $this->get(
                        'AdminBundle\Service\DataSynchronizer\ComEmailTemplateDataSynchronizer'
                    );
                    $editResult                  = $comEmailTemplateDataSync->editTemplate(
                        $comEmailTemplate,
                        $this->getUser(),
                        $originalContents,
                        $originalLogoImage,
                        $originalContentsImage,
                        $editTemplateForm->get('delete_logo_image_command')->getData(),
                        $editTemplateForm->get('delete_contents_image_command')->getData()
                    );

                    if ($editResult) {
                        $data = $jsonResponseDataProvider->success();

                        return new JsonResponse($data, 200);
                    } else {
                        $data = $jsonResponseDataProvider->apiCommunicationError();

                        return new JsonResponse($data, 500);
                    }
                } else {
                    $data            = $jsonResponseDataProvider->formError();
                    $templateDataGenerator->setComEmailTemplate($comEmailTemplate);
                    $formView       = $this->renderView(
                        'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                        array(
                        'manip_template_form' => $editTemplateForm->createView(),
                        'current_template_model' => $comEmailTemplate->getTemplateModel(),
                        'template_model_class' => new TemplateModel(),
                        'content_type_class' => new TemplateContentType(),
                        'edit_mode' => true,
                        'instantaneous_template_preview' => $templateDataGenerator
                            ->retrieveContentPartHtml(true, true),
                        'template_logo_alignment_class' => new TemplateLogoAlignment(),
                        )
                    );
                    $data['content'] = $formView;

                    return new JsonResponse($data, 200);
                }
            }
        }

        return new JsonResponse(
            $jsonResponseDataProvider->pageNotFound(),
            404
        );
    }

    /**
     * @Route(
     *     "/emailling/modeles-emails/previsulisation-modele/{template_id}",
     *     name="admin_communication_emailing_templates_preview_template",
     *     requirements={"template_id": "\d+"}
     * )
     * @param Request $request     Description
     * @param type    $template_id Description
     * @return type Description
     */
    public function emailingTemplatesPreviewTemplateAction(Request $request, $template_id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $em                 = $this->getDoctrine()->getManager();
        $comEmailTemplate = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(
                array(
                    'program' => $program,
                    'id' => $template_id,
                )
            );
        if (is_null($comEmailTemplate)) {
            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        if ($request->isMethod('GET')) {
            $view = $this->renderView(
                'AdminBundle:EmailTemplates/Communication:template_content.html.twig',
                array(
                'com_email_template' => $comEmailTemplate,
                'template_model_class' => new TemplateModel(),
                'template_logo_alignment_class' => new TemplateLogoAlignment(),
                'content_type_class' => new TemplateContentType(),
                'preview_mode' => true,
                )
            );

            $data            = $jsonResponseDataProvider->success();
            $data['content'] = $view;

            return new JsonResponse($data, 200);
        }

        return new JsonResponse($jsonResponseDataProvider->pageNotFound(), 404);
    }

    /**
     * @Route("/emailing/campagne/preview",name="admin_communication_emailing_campagne_preview_template")
     * @param Request $request Description
     * @return type Description
     */
    public function previewCampagneTplAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        if ($request->isMethod('POST')) {
            $urlTpl = $request->get('urlTpl');
            if (is_null($urlTpl) || empty($urlTpl)) {
                return new Response('', 404);
            }
            $contents = file_get_contents($urlTpl);

            return new Response($contents);
        }

        return new Response('');
    }

    /**
     * @Route(
     *     "/emailling/templates/duplication-template/{template_id}",
     *     name="admin_communication_emailing_templates_duplicate_template",
     *     requirements={"template_id": "\d+"},
     *     defaults={"template_id"=null}
     * @param Request $request     Description
     * @param type    $template_id Description
     * @return type Description
     * )
     */
    public function emailingTemplatesDuplicateTemplateAction(Request $request, $template_id)
    {
        $authChecker = $this->get('security.authorization_checker');

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        if (false === $authChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            //            return $this->redirectToRoute('fos_user_security_logout');

            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            //            return $this->redirectToRoute('fos_user_security_logout');

            return new JsonResponse(
                $jsonResponseDataProvider->pageNotFound(),
                404
            );
        }

        $em                 = $this->getDoctrine()->getManager();
        $com_email_template = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(
                array(
                    'id' => $template_id,
                    'program' => $program,
                )
        );

        if (is_null($com_email_template)) {
            //            return $this->createNotFoundException(self::TEMPLATE_NOT_FOUND_MESSAGE);

            $data            = $jsonResponseDataProvider->pageNotFound();
            $data['message'] = self::TEMPLATE_NOT_FOUND_MESSAGE;
            return new JsonResponse($data, 404);
        }

        $template_duplicator = $this->get('AdminBundle\Service\DataDuplicator\ComEmailTemplateDuplicator');
        $new_name            = $template_duplicator->generateTemplateName($program,
            $com_email_template->getName());

        $formFactory             = $this->get('form.factory');
        $duplicationData         = new ComEmailTemplateDuplicationData($em);
        $duplicationData->setDuplicationSourceId($com_email_template->getId())
            ->setName($new_name);
        $duplicate_template_form = $formFactory->createNamed(
            'duplicate_template_form', DuplicationForm::class, $duplicationData
        );

        if ($request->isMethod('GET')) {
            $view = $this
                ->renderView(
                'AdminBundle:Communication/EmailingTemplates:duplicate_template.html.twig',
                array(
                'duplicate_template_form' => $duplicate_template_form->createView(),
                )
            );

            $data            = $jsonResponseDataProvider->success();
            $data['content'] = $view;
            return new JsonResponse($data, 200);
        }

        if ($request->isMethod('POST')) {
            if ($request->request->has("duplicate_template_form")) {
                $duplicate_template_form->handleRequest($request);
                if ($duplicate_template_form->isSubmitted() && $duplicate_template_form->isValid()) {
                    if ($template_id == $duplicationData->getDuplicationSourceId()) {
                        $template_duplicator
                            ->duplicate($program, $com_email_template,
                                $this->getUser(), $duplicationData->getName());

                        $data = $jsonResponseDataProvider->success();
                        return new JsonResponse($data, 200);
                    }
                } else {

                    $data            = $jsonResponseDataProvider->formError();
                    $view            = $this
                        ->renderView(
                        'AdminBundle:Communication/EmailingTemplates:duplicate_template.html.twig',
                        array(
                        'duplicate_template_form' => $duplicate_template_form->createView(),
                        )
                    );
                    $data['content'] = $view;
                    return new JsonResponse($data, 200);
                }
            }
        }

        /* $template_duplicator = $this->get('AdminBundle\Service\DataDuplicator\ComEmailTemplateDuplicator');
          $template_duplicator->duplicate($program, $com_email_template, $this->getUser()); */


        return new JsonResponse($jsonResponseDataProvider->pageNotFound(), 404);
    }

    /**
     * @Route(
     *     "/emailing/templates/delete-template/{template_id}",
     *     name="admin_communication_emailing_templates_delete_template",
     * )
     */
    public function emailingTemplateDeleteTemplateAction(Request $request,
                                                         $template_id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em               = $this->getDoctrine()->getManager();
        $comEmailTemplate = $em->getRepository('AdminBundle\Entity\ComEmailTemplate')
            ->findOneBy(
            array(
                'id' => $template_id,
                'program' => $program,
            )
        );

        if (!is_null($comEmailTemplate)) {
            $comEmailTemplateDataSync = $this
                ->get('AdminBundle\Service\DataSynchronizer\ComEmailTemplateDataSynchronizer');
            $deleteRes                = $comEmailTemplateDataSync->deleteTemplate($comEmailTemplate);
            if (true == $deleteRes) {

                $data = $jsonResponseDataProvider->success();
                return new JsonResponse($data, 200);
            } else {

                $data = $jsonResponseDataProvider->apiCommunicationError();
                return new JsonResponse($data, 500);
            }
        }


        return new JsonResponse($jsonResponseDataProvider->pageNotFound(), 404);
    }

    /**
     * @Route(
     *     "/emailing/liste-contact/{trie}",
     *     name="admin_communication_emailing_list_contact",
     * )
     */
    public function emailingListeContactAction(Request $request, $trie)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        if (empty($trie) || is_null($trie)) {
            return $this->redirectToRoute('admin_communication_emailing_list_contact',
                    array('trie' => 'recents'));
        }

        $em = $this->getDoctrine()->getManager();

        //Call ContactList manager service
        $allContactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

        //Get all contacts Lists
        $listContact = $allContactList->getAllList();

        // Obtient une liste de colonnes
        foreach ($listContact as $key => $row) {
            $Name[$key]      = $row['Name'];
            $CreatedAt[$key] = $row['CreatedAt'];
        }

        if ($trie == 'a-z') {
            array_multisort($Name, SORT_ASC, $listContact);
        } elseif ($trie == 'z-a') {
            array_multisort($Name, SORT_DESC, $listContact);
        } elseif ($trie == 'recents') {
            array_multisort($CreatedAt, SORT_DESC, $listContact);
        }

        return $this->render(
                'AdminBundle:Communication:emailing_liste_contact.html.twig',
                array(
                'ListContact' => $listContact,
                'trie' => $trie
                )
        );
    }

    /**
     * @Route(
     *     "/emailing/liste-contact-edit",
     *     name="admin_communication_emailing_list_contact_edition",
     * )
     */
    public function emailingListeContactEditAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $idList   = $request->get('IdList');
            $response = $this->forward('AdminBundle:PartialPage:emailingListeContactEditAjax',
                array('IdList' => $idList));
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
    public function emailingListeContactEditSubmitAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $idList = $request->get('IdList');
            $userId = $request->get('UserId');

            $response = $this->forward(
                'AdminBundle:PartialPage:emailingListeContactEditSubmitAjax',
                array(
                'IdList' => $idList,
                'UserId' => $userId,
                )
            );

            return new Response($response->getContent());
        }
    }

    /**
     * @Route(
     *     "/emailing/liste-contact-creer",
     *     name="admin_communication_emailing_list_contact_creation",
     * )
     */
    public function emailingListeContactCreerAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
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
    public function emailingListeContactCreerSubmitAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $listName = $request->get('ListName');
            $userId   = $request->get('UserId');

            $response = $this->forward(
                'AdminBundle:PartialPage:emailingListeContactCreerSubmitAjax',
                array(
                'ListName' => $listName,
                'UserId' => $userId,
                )
            );

            return new Response($response->getContent());
        }
    }

    /**
     * @Route(
     *     "/emailing/liste-contact-delete",
     *     name="admin_communication_emailing_list_contact_delete",
     * )
     */
    public function emailingListeContactDeleteAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $idList = $request->get('IdList');

            $response = $this->forward(
                'AdminBundle:PartialPage:emailingListeContactDeleteAjax',
                array(
                'IdList' => $idList
                )
            );

            return new Response($response->getContent());
        }
    }

    /**
     * @Route(
     *     "/emailing/liste-contact-dupliquer",
     *     name="admin_communication_emailing_list_contact_dupliquer",
     * )
     */
    public function emailingListeContactDupliquerAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $listName = $request->get('ListName');
            $listId   = $request->get('ListId');

            $response = $this->forward(
                'AdminBundle:PartialPage:emailingListeContactDupliquerAjax',
                array(
                'ListName' => $listName,
                'ListId' => $listId
                )
            );

            return new Response($response->getContent());
        }
    }

    /**
     * @Route(
     *     "/emailing/liste-contact-export/{id}",
     *     name="admin_communication_emailing_list_contact_export"),
     *     requirements={"id": "\d+"}
     */
    public function emailingListeContactExportAction($id)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(), 404);
        }
        //Call ContactList manager service
        $contactList = $this->container->get('AdminBundle\Service\MailJet\MailjetContactList');

        //Service to call excel object
        $em = $this->getDoctrine()->getManager();
        $objPHPExcel = $this->get("adminBundle.excel")->excelListContact($id, $contactList, $em);

        // create the writer
        $writer = $this->get('phpexcel')->createWriter($objPHPExcel, 'Excel2007');

        $rootDir = $this->get('kernel')->getProjectDir().'/web/emailing/liste-contacts-export';
        if (!file_exists($rootDir)) {
            mkdir($rootDir, 0777, true);
        }
        $nameFile = 'export-liste-contact-'.date('YmdHi').'-emailing.xlsx';
        $fileDest = $rootDir.'/'.$nameFile;
        $writer->save($fileDest);

        return $this->redirectToRoute('admin_communication_emailing_list_contact_export_download',
                array('filename' => $nameFile));
    }

    /**
     * @Route(
     *     "/emailing/liste-contact-export-download/{filename}",
     *     name="admin_communication_emailing_list_contact_export_download"),
     *     requirements={"filename": ".+"}
     */
    public function emailingListeContactExportDownloadAction($filename)
    {
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
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE,
            $filename, iconv('UTF-8', 'ASCII//TRANSLIT', $filename));
        return $response;
    }

    /**
     * @Route(
     *     "/sondage-quiz/{id}",
     *     name="admin_communication_sondage_quiz", defaults={"id"=null}),
     *     requirements={"id": "\d*"}
     */
    public function sondageQuizAction($id, Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        $isSondagesQuiz    = false;
        $sondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        if (!isset($sondagesQuizArray[0])) {
            $sondagesQuiz = new SondagesQuiz();
        } else {
            $sondagesQuiz   = $sondagesQuizArray[0];
            $isSondagesQuiz = true;
        }

        //Formulaire d'ajout/edition sondages/quiz
        $formSondagesQuiz = $this->createForm(
            SondagesQuizType::class, $sondagesQuiz,
            array(
            'action' => $this->generateUrl('admin_communication_sondage_quiz'),
            'method' => 'POST',
            )
        );

        $formSondagesQuiz->handleRequest($request);
        if ($formSondagesQuiz->isSubmitted() && $formSondagesQuiz->isValid()) {
            $sondagesQuizData = $formSondagesQuiz->getData();
            $sondagesQuizData->setProgram($program);
            $sondagesQuizData->upload($program);

            if (!isset($sondagesQuizArray[0])) {
                $sondagesQuizData->setDateCreation(new \DateTime());
                $em->persist($sondagesQuizData);
            }

            $em->flush();
            return $this->redirectToRoute('admin_communication_sondage_quiz');
        }

        //Formulaires questionnaires
        if (!is_null($id)) {
            //$SondagesQuizQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($id);
            $sondagesQuizQuestionnaireInfos = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findOneBy(
                array(
                    'id' => $id
                )
            );
        } else {
            $sondagesQuizQuestionnaireInfos = new SondagesQuizQuestionnaireInfos();
        }

        $SondagesQuizQuestions = new SondagesQuizQuestions();
        $SondagesQuizReponses  = new SondagesQuizReponses();
        $formQuestionnaires    = $this->createForm(SondagesQuizQuestionnaireInfosType::class,
            $sondagesQuizQuestionnaireInfos);

        $formQuestionnaires->handleRequest($request);
        if ($formQuestionnaires->isSubmitted() && $formQuestionnaires->isValid()) {
            $sondagesQuizQuestionnaireInfosData = $formQuestionnaires->getData();
            $sondagesQuizQuestionnaireInfosData->setSondagesQuiz($sondagesQuiz);
            if ($request->get('btn-publier-sondages-quiz') !== null) {
                $sondagesQuizQuestionnaireInfosData->setEstPublier(true);
            } else {
                $sondagesQuizQuestionnaireInfosData->setEstPublier(false);
            }
            $em->persist($sondagesQuizQuestionnaireInfosData);
            foreach ($sondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $Questions) {
                $Questions->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfosData);
                $em->persist($Questions);
                foreach ($Questions->getSondagesQuizReponses() as $Reponses) {
                    $Reponses->setSondagesQuizQuestions($Questions);
                }
            }

            $em->flush();
            return $this->redirectToRoute('admin_communication_sondage_quiz');
        }

        $isBanniere   = false;
        $bannierePath = "";
        if (!empty($sondagesQuiz->getPath())) {
            $isBanniere   = true;
            $bannierePath = $sondagesQuiz->getPath();
        }

        //On recupere les questions/reponses
        $questionsInfosArray = array();
        if (isset($sondagesQuizArray[0])) {
            $questionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBySondagesQuiz($sondagesQuizArray[0]);
        }

        return $this->render(
                'AdminBundle:Communication:sondage_quiz.html.twig',
                array(
                'formSondagesQuiz' => $formSondagesQuiz->createView(),
                'formQuestionnaires' => $formQuestionnaires->createView(),
                'IsBanniere' => $isBanniere,
                'BannierePath' => $bannierePath,
                'IsSondagesQuiz' => $isSondagesQuiz,
                'program' => $program,
                'QuestionsInfosArray' => $questionsInfosArray
                )
        );
    }

    /**
     * @Route(
     *     "/sondage-quiz/delete-sondages-quiz/sondages-quiz",
     *     name="admin_communication_sondage_quiz_delete")
     */
    public function sondageQuizDeleteAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        if ($request->isMethod('POST')) {
            $id                  = $request->get('Id');
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
     */
    public function sondageQuizDeleteReponsesAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $id            = $request->get('IdReponses');
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
     */
    public function sondageQuizDeleteQuestionsAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $id             = $request->get('IdQuestion');
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
    public function sondageQuizEditAction($id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        //Recuperer le questionnaire
        $QuestionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->find($id);
        $formQuestionnaires  = $this->createForm(SondagesQuizQuestionnaireInfosType::class,
            $QuestionsInfosArray);

        return $this->render(
                'AdminBundle:Communication:edit_sondage_quiz.html.twig',
                array(
                'formQuestionnaires' => $formQuestionnaires->createView(),
                )
        );
    }

    /**
     * @Route(
     *     "/sondage-quiz/delete-banniere/banniere",
     *     name="admin_communication_sondage_quiz_delete_banniere")
     */
    public function sondageQuizDeleteBanniereAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $SondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
            if (isset($SondagesQuizArray[0])) {
                $SondagesQuiz = $SondagesQuizArray[0];
                $SondagesQuiz->setPath(null);
                $em->flush();
                return new Response('ok');
            } else {
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
        $data              = [];
        $date              = new \DateTime();
        $now               = $date->settime(0, 0, 0)->format("Y-m-d");
        $filters           = ["lastactivityat" => $now];
        $mailjet           = $this->get('mailjet.client');
        $response          = $mailjet->get(Resources::$Campaignstatistics,
            ['filters' => $filters]); //call of ApiMailjet
        $listsInfoCampaign = $response->getData();
        $data              = $this->get('adminBundle.statistique')->getTraitement($listsInfoCampaign); //call of service
        $fromTo            = $this->get('adminBundle.statistique')->getContactByCampaign();
        $send              = !empty($fromTo) ? $fromTo : [];
        return $this->render(
                'AdminBundle:Communication:emailing_statistique_.html.twig',
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
                "json" => $data["json"]->getContent()
                ]
        );
    }

    /**
     * News post list controller
     *
     * @param Request $request
     * @param string  $post_type_label news post type : standard or welcoming post (from NewsPostTypeLabel constant)
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
    public function newsAction(Request $request, $post_type_label,
                               $archived_state)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        if (!in_array($post_type_label,
                NewsPostTypeLabel::VALID_NEWS_POST_TYPE_LABEL)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $newsPostManager    = $this->get('AdminBundle\Manager\NewsPostManager');
        $newsPostDataLinker = $this->get('AdminBundle\Service\DataLinker\NewsPostDataLinker');
        $newsPostList       = $newsPostManager->findAllOrderedByMixedDate(
            $program,
            $newsPostDataLinker->linkTypeLabelToType($post_type_label),
            $archived_state
        );

        $viewOptions = array(
            'news_post_submission_type_class' => new NewsPostSubmissionType(),
            'news_post_authorization_type_class' => new NewsPostAuthorizationType(),
            'news_post_list' => $newsPostList,
            'post_type_label_class' => new NewsPostTypeLabel(),
        );
        if (true == $archived_state) {
            $viewOptions['archived_state'] = true;
        }
        if (NewsPostTypeLabel::WELCOMING == $post_type_label) {
            $viewOptions['welcoming_news_post_type'] = true;
        }
        return $this->render('AdminBundle:Communication:news.html.twig',
                $viewOptions);
    }

    /**
     * @Route("/emailing/statistiques/filter/date", name="admin_statistiques_filter")
     * @Method({"POST"})
     */
    public function statistiqueFilterDateAction(Request $request)
    {
        $filtre  = $request->request->get('filter');
        $mailjet = $this->get('mailjet.client');
        if ($filtre == "Yesterday") {
            $date                       = new \DateTime();
            $date->modify('-1 day');
            $format                     = $date->format("Y-m-d");
            $yest                       = $date->settime(0, 0, 0)->getTimestamp();
            $filters                    = ["fromts" => (string) $yest];
            $respons                    = $mailjet->get(Resources::$Campaignstatistics,
                ['filters' => $filters]);
            $listsInfoCampaignYesterday = $respons->getData();
            if (!empty($listsInfoCampaignYesterday)) {
                foreach ($listsInfoCampaignYesterday as $value) {
                    $dateFiter = new \DateTime($value["LastActivityAt"]);
                    $time      = $dateFiter->format("Y-m-d");
                    if ($time == $format) {
                        $listsInfoCampaign[] = $value;
                    }
                }
            }
            $listCampaigns          = !empty($listsInfoCampaign) ? $listsInfoCampaign
                    : "";
            $allContactSendCampagne = $this->get('adminBundle.statistique')->getContactByPeriode($filtre);
            $info                   = $this->get('adminBundle.statistique')->getTraitement($listCampaigns);
            $data                   = [
                "fromTo" => $allContactSendCampagne,
                "info" => $info,
                "dataGraph" => $listsInfoCampaignYesterday
            ];
        } elseif ($filtre == "last7days") {
            $date                    = new \DateTime();
            $last                    = $date->modify('-6 day');
            $last7                   = $date->settime(0, 0, 0)->getTimestamp();
            $filters                 = ["fromts" => (string) $last7];
            $response7               = $mailjet->get(Resources::$Campaignstatistics,
                    ['filters' => $filters])->getData();
            $allContactSendCampagne7 = $this->get('adminBundle.statistique')->getContactByPeriode($filtre);
            $info                    = $this->get('adminBundle.statistique')->getTraitement($response7);
            $data                    = [
                "fromTo" => $allContactSendCampagne7,
                "info" => $info,
                "dataGraph" => $response7
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
        return $this->forward(
                'AdminBundle:Communication:news',
                array(
                'archived_state' => true,
                'post_type_label' => $post_type_label,
                )
        );
    }

    /**
     * @Route("/actualites/creer", name="admin_communication_news_create")
     */
    public function createNewsAction(Request $request)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $postTypeLabel = $request->get('news_post_type_label');
        if (is_null($postTypeLabel) || !in_array($postTypeLabel,
                NewsPostTypeLabel::VALID_NEWS_POST_TYPE_LABEL)
        ) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $formGenerator      = $this->get('AdminBundle\Service\FormGenerator\NewsPostFormGenerator');
        $newsPostDataLinker = $this->get('AdminBundle\Service\DataLinker\NewsPostDataLinker');
        $newsPostForm       = $formGenerator->generateForCreation(
            $program, $newsPostDataLinker->linkTypeLabelToType($postTypeLabel),
            'news_post_form'
        );
        $newsPostForm->handleRequest($request);
        if ($newsPostForm->isSubmitted() && $newsPostForm->isValid()) {
            $submission_type = $request->get('submission_type');
            $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
            if ($newsPostManager->create($newsPostForm->getData(),
                    $submission_type)) {

                $data = $jsonResponseDataProvider->success();
                return new JsonResponse($data, 200);
            } else {

                return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                    404);
            }
        }
        $contentOption = array(
            'news_post_form' => $newsPostForm->createView(),
            'news_post_submission_type_class' => new NewsPostSubmissionType(),
        );
        if (NewsPostTypeLabel::WELCOMING == $postTypeLabel) {
            $contentOption['welcoming_news_post_type'] = true;
        }
        $content = $this->renderView('AdminBundle:Communication/News:manip_news.html.twig',
            $contentOption);

        $data = $jsonResponseDataProvider->success();
        if ($newsPostForm->isSubmitted() && !$newsPostForm->isValid()) {

            $data = $jsonResponseDataProvider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/actualites/editer/{id}", requirements={"id": "\d+"}, name="admin_communication_news_edit")
     */
    public function editNewsAction(Request $request, $id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em       = $this->getDoctrine()->getManager();
        $newsPost = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($newsPost)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $postTypeLabel = $request->get('news_post_type_label');
        if (is_null($postTypeLabel) || !in_array($postTypeLabel,
                NewsPostTypeLabel::VALID_NEWS_POST_TYPE_LABEL)
        ) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $formGenerator      = $this->get('AdminBundle\Service\FormGenerator\NewsPostFormGenerator');
        $newsPostDataLinker = $this->get('AdminBundle\Service\DataLinker\NewsPostDataLinker');
        $newsPostForm       = $formGenerator->generateForEdit(
            $newsPost, $newsPostDataLinker->linkTypeLabelToType($postTypeLabel),
            'news_post_form'
        );
        $newsPostForm->handleRequest($request);
        if ($newsPostForm->isSubmitted() && $newsPostForm->isValid()) {
            $submissionType  = $request->get('submission_type');
            $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
            if ($newsPostManager->edit($newsPostForm->getData(), $submissionType)) {

                $data = $jsonResponseDataProvider->success();
                return new JsonResponse($data, 200);
            } else {

                return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                    404);
            }
        }

        $contentOption = array(
            'news_post_form' => $newsPostForm->createView(),
            'news_post_submission_type_class' => new NewsPostSubmissionType(),
            'edit_mode' => true,
        );
        if (NewsPostTypeLabel::WELCOMING == $postTypeLabel) {
            $contentOption['welcoming_news_post_type'] = true;
        }
        $content = $this->renderView('AdminBundle:Communication/News:manip_news.html.twig',
            $contentOption);

        $data = $jsonResponseDataProvider->success();
        if ($newsPostForm->isSubmitted() && !$newsPostForm->isValid()) {

            $data = $jsonResponseDataProvider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/actualites/dupliquer/{id}", requirements={"id": "\d+"}, name="admin_communication_news_duplicate")
     */
    public function duplicateNewsAction(Request $request, $id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em       = $this->getDoctrine()->getManager();
        $newsPost = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($newsPost)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $formGenerator           = $this->get('AdminBundle\Service\FormGenerator\NewsPostFormGenerator');
        $newsPostDuplicationForm = $formGenerator->generateForDuplication($newsPost,
            'duplicate_news_post_form');
        $newsPostDuplicationForm->handleRequest($request);
        if ($newsPostDuplicationForm->isSubmitted() && $newsPostDuplicationForm->isValid()) {
            if ($newsPost->getId() == $newsPostDuplicationForm->getData()->getDuplicationSourceId()) {
                $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
                if ($newsPostManager->duplicate($newsPost,
                        $newsPostDuplicationForm->getData()->getName())) {

                    $data = $jsonResponseDataProvider->success();
                    return new JsonResponse($data, 200);
                } else {

                    return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                        404);
                }
            } else {

                return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                    404);
            }
        }

        $content = $this->renderView(
            'AdminBundle:Communication/News:duplicate_news.html.twig',
            array(
            'duplicate_news_post_form' => $newsPostDuplicationForm->createView()
            )
        );

        $data = $jsonResponseDataProvider->success();
        if ($newsPostDuplicationForm->isSubmitted() && !$newsPostDuplicationForm->isValid()) {

            $data = $jsonResponseDataProvider->formError();
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

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em       = $this->getDoctrine()->getManager();
        $newsPost = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($newsPost)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
        $newsPostManager->definePublishedState($newsPost, $state);


        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/actualites/depublier/{id}", requirements={"id": "\d+"}, name="admin_communication_news_unpublish")
     */
    public function unpublishNewsAction(Request $request, $id)
    {
        return $this->forward(
                'AdminBundle:Communication:publishNews',
                array(
                'id' => $id,
                'state' => false,
                )
        );
    }

    /**
     * @Route("/actualites/archiver/{id}/{archived_state}", defaults={"archived_state"=true}, name="admin_communication_news_archive")
     */
    public function archiveNewsAction(Request $request, $id, $archived_state)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em       = $this->getDoctrine()->getManager();
        $newsPost = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($newsPost)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
        $newsPostManager->defineArchivedState($newsPost, $archived_state);


        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/actualites-archivees/restaurer/{id}", name="admin_communication_news_restore")
     */
    public function restoreNewsAction(Request $request, $id)
    {
        return $this->forward(
                'AdminBundle:Communication:archiveNews',
                array(
                'id' => $id,
                'archived_state' => false,
                )
        );
    }

    /**
     * @Route("/actualites/supprimer/{id}", name="admin_communication_news_delete")
     */
    public function deleteNewsAction(Request $request, $id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em       = $this->getDoctrine()->getManager();
        $newsPost = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($newsPost)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
        $newsPostManager->delete($newsPost);


        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/actualites/action-de-groupe", name="admin_communication_news_group_action")
     */
    public function processGroupActionNewsAction(Request $request)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $strNewsPostIdList = $request->get('news_post_id_list');
        $groupedActionType = $request->get('grouped_action_type');

        if (is_null($strNewsPostIdList) || is_null($groupedActionType) || !in_array($groupedActionType,
                GroupActionType::NEWS_POST_VALID_GROUP_ACTION)
        ) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $newsPostManager = $this->get('AdminBundle\Manager\NewsPostManager');
        $newsPostManager->processGroupAction(
            explode(',', $strNewsPostIdList), $groupedActionType, $program
        );


        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/actualites/previsualisation/{id}", requirements={"id": "\d+"}, name="admin_communication_news_preview")
     */
    public function previewNewsAction(Request $request, $id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em       = $this->getDoctrine()->getManager();
        $newsPost = $em->getRepository('AdminBundle\Entity\NewsPost')
            ->findOneByIdAndProgram($id, $program);
        if (is_null($newsPost)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }


        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $this->renderView(
            'AdminBundle:Communication/News:preview_news.html.twig',
            array(
            'news_post' => $newsPost
            )
        );

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
        $typeId    = $request->request->get("id")["campaign_id"];
        $typeTitle = $request->request->get("id")["title"];
        $id        = !empty($typeId) ? $typeId : $request->query->get("id");
        $title     = !empty($typeTitle) ? $typeTitle : $request->query->get("title");

        $mailjet         = $this->get('mailjet.client');
        $filter          = ["campaignid" => $id];
        $campaigns       = $mailjet->get(Resources::$Campaign,
                ['filters' => $filter])->getData()[0];
        $results         = $this->get('adminBundle.statistique')->getOneCampagne($id);
        $paginator       = $this->get('knp_paginator');
        $pagination      = $paginator->paginate(
            $results["email"], $request->query->getInt('page', 1), 5,
            [
            "id" => $id,
            "title" => $title,
            "paramId" => "id",
            "paramTitle" => "title"
            ]
        );
        $view            = $this->renderView(
            'AdminBundle:Communication/EmailingTemplates:statistique_campaign.html.twig',
            [
            "date" => $campaigns["CreatedAt"],
            "email" => $campaigns["FromEmail"],
            "fromName" => $campaigns["FromName"],
            "sujet" => $campaigns["Subject"],
            "listContact" => $results["listContact"],
            "status" => $results["status"],
            "emails" => $pagination,
            "name" => $results["template"],
            "data" => $results["data"],
            "title" => $title,
            "id" => $id
            ]
        );
        $data['content'] = $view;
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/emailing/campagne/statistique/filter", name="admin_communication_emailing_campaign_filter")
     * @Method("POST")
     */
    public function emailingCampaignStatistiqueFilterAction(Request $request)
    {
        $id       = $request->request->get("id");
        $results  = $this->get('adminBundle.statistique')->getOneCampagne($id);
        $data     = $results["data"];
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
        $id                = $request->query->get("id");
        $status            = $request->query->get("status");
        $objPHPExcel       = $this->get("adminBundle.excel")->generateExcel($id,
            $status);
        $writer            = $this->get('phpexcel')->createWriter($objPHPExcel,
            'Excel5');
        // create the response
        $response          = $this->get('phpexcel')->createStreamedResponse($writer);
        // adding headers
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'CloudRewards.xls'
        );
        $response->headers->set('Content-Type',
            'text/vnd.ms-excel; charset=utf-8');
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
        $id      = $request->query->get("id");

        $filter    = ["campaignid" => $id];
        $title     = $request->query->get("title");
        $campaigns = $mailjet->get(Resources::$Campaign, ['filters' => $filter])->getData()[0];
        $results   = $this->get('adminBundle.statistique')->getOneCampagne($id);
        $html      = $this->renderView(
            'pdf/template.html.twig',
            [
            "date" => $campaigns["CreatedAt"],
            "email" => $campaigns["FromEmail"],
            "fromName" => $campaigns["FromName"],
            "sujet" => $campaigns["Subject"],
            "listContact" => $results["listContact"],
            "status" => $results["status"],
            "emails" => $results["email"],
            "name" => $results["template"],
            "data" => $results["data"],
            "title" => $title
            ]
        );
        $a_date    = new \DateTime();
        $filename  = 'export_statistique'.$a_date->format('dmY');
        $html2pdf  = $this->get('html2pdf_factory')->create();
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

        $em            = $this->getDoctrine()->getManager();
        $eLearningList = $em->getRepository('AdminBundle\Entity\ELearning')->findBy(
            array('program' => $program), array('created_at' => 'DESC')
        );

        return $this->render(
                'AdminBundle:Communication:e_learning.html.twig',
                array(
                'e_learning_content_type_class' => new ELearningContentType(),
                'e_learning_list' => $eLearningList,
                'authorization_type_class' => new AuthorizationType(),
                )
        );
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

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $formGenerator = $this->get('AdminBundle\Service\FormGenerator\ELearningFormGenerator');
        $eLearningForm = $formGenerator->generateForCreation($program);
        $eLearningForm->handleRequest($request);
        if ($eLearningForm->isSubmitted() && $eLearningForm->isValid()) {
            $submissionType   = $request->get('submission_type');
            $eLearningManager = $this->get('AdminBundle\Manager\ELearningManager');
            if (in_array($submissionType, SubmissionType::VALID_SUBMISSION_TYPE)
                && $eLearningManager->create($eLearningForm->getData(),
                    $submissionType)
            ) {

                $data = $jsonResponseDataProvider->success();
                return new JsonResponse($data, 200);
            } else {

                return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                    404);
            }
        }

        $content = $this->renderView(
            'AdminBundle:Communication/ELearning:manip_e_learning.html.twig',
            array(
            'e_learning_form' => $eLearningForm->createView(),
            'submission_type_class' => new SubmissionType(),
            'e_learning_content_type_class' => new ELearningContentType(),
            )
        );

        $data = $jsonResponseDataProvider->success();
        if ($eLearningForm->isSubmitted() && !$eLearningForm->isValid()) {

            $data = $jsonResponseDataProvider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * Action to call when previewing e-learning
     * (using AJAX call)
     *
     * @param Request $request
     * @param int     $id
     *
     * @return JsonResponse
     *
     * @Route("/e-learning/previsualisation/{id}", requirements={"id": "\d+"}, name="admin_communication_e_learning_preview")
     */
    public function previewELearningAction(Request $request, $id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em        = $this->getDoctrine()->getManager();
        $eLearning = $em->getRepository('AdminBundle\Entity\ELearning')->findOneBy(
            array(
                'id' => $id,
                'program' => $program,
            )
        );
        if (is_null($eLearning)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $eLearningManager = $this->get('AdminBundle\Manager\ELearningManager');
        $eLearningData    = $eLearningManager->retrieveELearningContentData($eLearning);


        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $this->renderView(
            'AdminBundle:Communication/ELearning:preview_e_learning.html.twig',
            array(
            'e_learning' => $eLearning,
            'e_learning_media_contents' => $eLearningData['media_contents'],
            'e_learning_quiz_contents' => $eLearningData['quiz_contents'],
            'e_learning_button_content' => $eLearningData['button_content'],
            'content_type_class' => new ELearningContentType(),
            )
        );

        return new JsonResponse($data, 200);
    }

    /**
     * Configuring e-learning welcoming banner
     * @param Request $request
     * @return Response
     * @Route("/e-learning/banniere-accueil", name="admin_communication_e_learning_welcoming_banner")
     */
    public function eLearningWelcomingBannerAction(Request $request)
    {
        $em                 = $this->getDoctrine()->getManager();
        $currentHeaderImage = null;
        $program            = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $em              = $this->getDoctrine()->getManager();
        $elearningBanner = $em->getRepository('AdminBundle\Entity\ELearningHomeBanner')->findOneBy(array(
            'program' => $program));
        if (empty($elearningBanner)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $currentHeaderImage  = $elearningBanner->getImageFile();
        $formElearningBanner = $this->createForm(ELearningHomeBannerType::class,
            $elearningBanner);
        $formElearningBanner->handleRequest($request);
        if ($request->isMethod('POST')) {
            if ($formElearningBanner->isSubmitted() && $formElearningBanner->isValid()) {
                $bannerImageFile = $elearningBanner->getImageFile();
                if (!is_null($bannerImageFile)) {
                    $bannerImageFile->move(
                        $this->getParameter("e_learning_media_document_dir"),
                        $bannerImageFile->getClientOriginalName()
                    );
                    $elearningBanner->setImageFile($bannerImageFile->getClientOriginalName());
                } else {
                    $elearningBanner->setImageFile($currentHeaderImage);
                }
                $menuName   = $formElearningBanner->get('menuName')->getData();
                $imageTitle = $formElearningBanner->get('imageTitle')->getData();
                $elearningBanner->setMenuName($menuName);
                $elearningBanner->setImageTitle($imageTitle);
                if (!empty($formElearningBanner->get('menuName')->getData()) && "true"
                    == $formElearningBanner->get('imageTitle')->getData()
                ) {
                    $filesystem = $this->get('filesystem');
                    $imagePath  = $this->getParameter('e_learning_media_document_dir')
                        .'/'
                        .$elearningBanner->getImageFile();
                    if ($filesystem->exists($imagePath)) {
                        $filesystem->remove($imagePath);
                    }
                    $elearningBanner->setImageFile(null);
                }
                $em->flush();
                return $this->redirectToRoute('admin_communication_e_learning_welcoming_banner');
            }
        }

        return $this->render('AdminBundle:Communication:e_learning_welcoming_banner.html.twig',
                array(
                "elearning_data_form" => $formElearningBanner->createView(),
                "current_header_image" => $currentHeaderImage,
                )
        );
    }

    /**
     * @Route("/pre-sondage/liste" ,name="admin_communication_pre_sondage")
     */
    public function preSondageQuizAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $allData = $manager->getAllSondageQuiz();
        $data    = $this->get("AdminBundle\Service\SondageQuiz\Common")->renderToJson($allData);
        return $this->render('AdminBundle:Communication:preSondage.html.twig',
                ["data" => $allData, "obj" => $data]);
    }

    /**
     * @Route("/pre-sondage/liste/{archived}",defaults={"archived"= false}, name="admin_communication_pre_archived_sondage")
     */
    public function preSondageQuizArchivedAction(Request $request, $archived)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $status  = $request->request->get("statut");
        $manager = $this->get("adminBundle.sondagequizManager");
        $allData = $manager->getAllSondageQuizArchived($status);
        return $this->render('AdminBundle:Communication:preSondage_archived.html.twig',
                ["data" => $allData]);
    }

    /**
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/pre-sondage/create",name="admin_communication_pre_sondage_create")
     */
    public function createPreSondageAction(Request $request, $id = null)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $em                = $this->getDoctrine()->getManager();
        $isSondagesQuiz    = false;
        $sondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        $roleDefault       = $em->getRepository('AdminBundle:Role')->findByProgram($program);
        if (!isset($sondagesQuizArray[0])) {

            $sondagesQuiz = new SondagesQuiz();
        } else {
            $sondagesQuiz   = $sondagesQuizArray[0];
            $isSondagesQuiz = true;
        }
        $sondagesQuizQuestionnaireInfos = new SondagesQuizQuestionnaireInfos();
        $formQuestionnaires             = $this->createForm(SondagesQuizQuestionnaireInfosType::class,
            $sondagesQuizQuestionnaireInfos);
        $sondagesQuizArray              = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);

        $formQuestionnaires->handleRequest($request);
        if ($formQuestionnaires->isSubmitted() && $formQuestionnaires->isValid()) {
            $sondagesQuizQuestionnaireInfosData = $formQuestionnaires->getData();
            if (empty($sondagesQuizQuestionnaireInfosData->getAuthorizedRole())) {
                $sondagesQuizQuestionnaireInfosData->setAuthorizedRole($roleDefault[0]);
            }
            $sondagesQuizQuestionnaireInfosData->setSondagesQuiz($sondagesQuiz);
            if ($request->get("data") == "btn-publier-sondages-quiz") {
                $sondagesQuizQuestionnaireInfosData->setEstPublier(true);
            } else {
                $sondagesQuizQuestionnaireInfosData->setEstPublier(false);
            }

            $em->persist($sondagesQuizQuestionnaireInfosData);
            foreach ($sondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $questions) {
                $questions->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfosData);
                $em->persist($questions);
                foreach ($questions->getSondagesQuizReponses() as $Reponses) {
                    $Reponses->setSondagesQuizQuestions($questions);
                }
            }
            $em->flush();

            $data = $jsonResponseDataProvider->success();
            return new JsonResponse($data, 200);
        }

        $content = $this->renderView(
            'AdminBundle:Communication:pre_create_sondage.html.twig',
            array(
            'formQuestionnaires' => $formQuestionnaires->createView(),
            'IsSondagesQuiz' => $isSondagesQuiz,
            'program' => $program,
        ));

        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/pre-sondage/editer/{id}", requirements={"id": "\d+"}, name="admin_communication_pre_sondage_edit")
     */
    public function editPreSondageAction(Request $request, $id)
    {

        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {

            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $em          = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->find($id);
        if (empty($editSondage)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        //$IsSondagesQuiz = false;
        $sondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        $roleDefault       = $em->getRepository('AdminBundle:Role')->findByProgram($program);
        if (!isset($sondagesQuizArray[0])) {
            $sondagesQuiz = new SondagesQuiz();
        } else {
            $sondagesQuiz = $sondagesQuizArray[0];
            //$IsSondagesQuiz = true;
        }
        $formQuestionnaires = $this->createForm(SondagesQuizQuestionnaireInfosType::class,
            $editSondage);
        $sondagesQuizArray  = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);

        $formQuestionnaires->handleRequest($request);
        if ($formQuestionnaires->isSubmitted() && $formQuestionnaires->isValid()) {
            $sondagesQuizQuestionnaireInfosData = $formQuestionnaires->getData();
            if (empty($sondagesQuizQuestionnaireInfosData->getAuthorizedRole())) {
                $sondagesQuizQuestionnaireInfosData->setAuthorizedRole($roleDefault[0]);
            }
            $sondagesQuizQuestionnaireInfosData->setSondagesQuiz($sondagesQuiz);
            if ($request->get("data") == "btn-publier-sondages-quiz") {
                $sondagesQuizQuestionnaireInfosData->setEstPublier(true);
            } else {
                $sondagesQuizQuestionnaireInfosData->setEstPublier(false);
            }
            $em->persist($sondagesQuizQuestionnaireInfosData);
            foreach ($sondagesQuizQuestionnaireInfosData->getSondagesQuizQuestions() as $questions) {
                $questions->setSondagesQuizQuestionnaireInfos($sondagesQuizQuestionnaireInfosData);
                $em->persist($questions);
                foreach ($questions->getSondagesQuizReponses() as $reponses) {
                    $reponses->setSondagesQuizQuestions($questions);
                }
            }
            $em->flush();

            $data = $jsonResponseDataProvider->success();

            return new JsonResponse($data, 200);
        }

        /* $content = $this->renderView('AdminBundle:Communication:pre_create_sondage.html.twig', array(
          'formQuestionnaires' => $formQuestionnaires->createView(),
          'program' => $program,
          'edit'=> true
          )); */
        $questionsInfosArray = array();
        if (isset($sondagesQuizArray[0])) {
            $questionsInfosArray = $em->getRepository('AdminBundle:SondagesQuizQuestionnaireInfos')->findBySondagesQuiz($sondagesQuizArray[0]);
        }
        $content = $this->renderView(
            'AdminBundle:Communication:pre_create_sondage.html.twig',
            array(
            'formQuestionnaires' => $formQuestionnaires->createView(),
            'program' => $program,
            'QuestionsInfosArray' => $questionsInfosArray
            )
        );

        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/pre-sondage/dupliquer/{id}",requirements={"id": "\d+"},name="admin_communication_pre_sondage_duplicate")
     */
    public function duplicatePreSondageAction(Request $request, $id)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $em              = $this->getDoctrine()->getManager();
        $dupliqueSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($dupliqueSondage)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $form         = $this->get('AdminBundle\Service\FormGenerator\SondageQuizForm');
        $formDuplique = $form->generateFormDuplicate($dupliqueSondage);
        $formDuplique->handleRequest($request);
        if ($formDuplique->isSubmitted() && $formDuplique->isValid()) {
            if ($dupliqueSondage->getId() == $formDuplique->getData()->getDuplicationSourceId()) {
                $manager = $this->get("adminBundle.sondagequizManager");
                if ($manager->duplicate($dupliqueSondage,
                        $formDuplique->getData()->getName())) {
                    $data = $jsonResponseDataProvider->success();
                    return new JsonResponse($data, 200);
                } else {
                    return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                        404);
                }
            } else {
                return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                    404);
            }
        }

        $content = $this->renderView(
            'AdminBundle:Communication:duplicate.html.twig',
            array(
            'duplicateForm' => $formDuplique->createView()
            )
        );
        $data    = $jsonResponseDataProvider->success();
        if ($formDuplique->isSubmitted() && !$formDuplique->isValid()) {
            $data = $jsonResponseDataProvider->formError();
        }
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/pre-sondage/publier/{id}/{state}",
     * defaults={"state"=true},
     * requirements={"id": "\d+"},
     * name="admin_communication_pre_sondage_publier")
     */
    public function publishedPreSondageAction(Request $request, $id, $state)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $em          = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($editSondage)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $manager->renderToPublished($editSondage, $state);

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/archiver/{id}/{archived}", defaults={"archived"=true}, name="admin_communication_pre_sondage_archive")
     */
    public function archivePreSondageAction(Request $request, $id, $archived)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em          = $this->getDoctrine()->getManager();
        $editSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($editSondage)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $manager->renderToArchived($editSondage, $archived);

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/groupe", name="admin_communication_pre_sondage_group_action")
     */
    public function groupPreSondageAction(Request $request)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $idList     = $request->get('id_list');
        $actionType = $request->get('grouped_action_type');
        if (is_null($idList) || is_null($actionType) || !in_array($actionType,
                GroupActionType::NEWS_POST_VALID_GROUP_ACTION)
        ) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $manager->groupAction(
            explode(',', $idList), $actionType, $program
        );

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route(
     *     "/pre-sondage/archivees/liste",name="admin_communication_pre_sondage_news_archived")
     */
    public function archivedListePreSondageAction(Request $request)
    {
        return $this->forward(
                'AdminBundle:Communication:preSondageQuizArchived',
                array(
                'archived' => true,
                )
        );
    }

    /**
     * @Route("/pre-sondage/archivees/restaurer/{id}", name="admin_communication_presondage_restore")
     */
    public function restorePreSondageAction(Request $request, $id)
    {
        return $this->forward(
                'AdminBundle:Communication:archivePreSondage',
                array(
                'id' => $id,
                'archived' => false,
                )
        );
    }

    /**
     * @Route("/pre-sondage/supprimer/{id}", name="admin_communication_pre_sondage_delete")
     */
    public function deletePreSondageAction(Request $request, $id)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em          = $this->getDoctrine()->getManager();
        $sondageQuiz = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->find($id);
        if (empty($sondageQuiz)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $manager = $this->get("adminBundle.sondagequizManager");
        $manager->delete($sondageQuiz);

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/cloture/{id}", name="admin_communication_pre_sondage_cloture")
     */
    public function cloturedPreSondageAction(Request $request, $id)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em             = $this->getDoctrine()->getManager();
        $clotureSondage = $em->getRepository("AdminBundle:SondagesQuizQuestionnaireInfos")
            ->findOneById($id);
        if (empty($clotureSondage)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $manager = $this->get("adminBundle.sondagequizManager");
        $manager->renderToCloture($clotureSondage);

        return new JsonResponse($jsonResponseDataProvider->success(), 200);
    }

    /**
     * @Route("/pre-sondage/banniere/acceuil", name="admin_communication_pre_sondage_bannier")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function bannierePreSondageAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em                = $this->getDoctrine()->getManager();
        $isSondagesQuiz    = false;
        $sondagesQuizArray = $em->getRepository('AdminBundle:SondagesQuiz')->findByProgram($program);
        if (!isset($sondagesQuizArray[0])) {
            $sondagesQuiz = new SondagesQuiz();
        } else {
            $sondagesQuiz   = $sondagesQuizArray[0];
            $isSondagesQuiz = true;
        }

        //Formulaire d'ajout/edition sondages/quiz
        $formSondagesQuiz = $this->createForm(
            SondagesQuizType::class, $sondagesQuiz,
            array(
            'action' => $this->generateUrl('admin_communication_pre_sondage_bannier'),
            'method' => 'POST',
            )
        );

        $formSondagesQuiz->handleRequest($request);
        if ($formSondagesQuiz->isSubmitted() && $formSondagesQuiz->isValid()) {
            $sondagesQuizData = $formSondagesQuiz->getData();
            $sondagesQuizData->setProgram($program);
            $sondagesQuizData->upload($program);
            if (!isset($sondagesQuizArray[0])) {
                $sondagesQuizData->setDateCreation(new \DateTime());
                $em->persist($sondagesQuizData);
            }

            $em->flush();
            return $this->redirectToRoute('admin_communication_pre_sondage_bannier');
        }

        $isBanniere   = false;
        $bannierePath = "";
        if (!empty($sondagesQuiz->getPath())) {
            $isBanniere   = true;
            $bannierePath = $sondagesQuiz->getPath();
        }

        return $this->render('AdminBundle:Communication:banniere.html.twig',
                [
                'formSondagesQuiz' => $formSondagesQuiz->createView(),
                'IsBanniere' => $isBanniere,
                'BannierePath' => $bannierePath,
                'program' => $program,
                'IsSondagesQuiz' => $isSondagesQuiz,]);
    }

    /**
     * @Route("/pre-sondage/statistiques/{id}", name="admin_communication_pre_sondage_stat")
     * @param Request $request
     * @param Id sondage $id
     * @return JsonResponse
     */
    public function statistiquesPreSondageAction(Request $request, $id)
    {
        $jsonResponseDataProvider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
        $program                  = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }

        $em          = $this->getDoctrine()->getManager();
        $manager     = $this->get("adminBundle.sondagequizManager");
        $statSondage = $manager->getElementStatistique($id);
        if (empty($statSondage)) {
            return new JsonResponse($jsonResponseDataProvider->pageNotFound(),
                404);
        }
        $res             = [
            'data' => $statSondage["sondageInfos"],
            'nbreQuestion' => $statSondage['nbreQuest'],
            'nbreReponse' => $statSondage['nbreReponse'],
        ];
        $content         = $this->renderView('AdminBundle:Communication:statistique_sondage.html.twig',
            $res);
        $data            = $jsonResponseDataProvider->success();
        $data['content'] = $content;

        return new JsonResponse($data, 200);
    }
}