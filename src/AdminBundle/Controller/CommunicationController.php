<?php
namespace AdminBundle\Controller;

use AdminBundle\Component\CommunicationEmail\TemplateLogoAlignment;
use AdminBundle\Component\Post\PostType;
use AdminBundle\Component\Slide\SlideType;
use AdminBundle\Controller\AdminController;
use AdminBundle\Entity\ComEmailTemplate;
use AdminBundle\Entity\HomePagePost;
use AdminBundle\Form\HomePagePostType;
use AdminBundle\Form\HomePageSlideDataType;
use Doctrine\Common\Collections\ArrayCollection;
use DrewM\MailChimp\MailChimp;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Component\CommunicationEmail\TemplateModel;
use AdminBundle\Form\ComEmailTemplateType;

/**
 * @Route("/admin/communication")
 */
class CommunicationController extends AdminController
{
    const SIDEBAR_VIEW = 'AdminBundle:Communication:menu_sidebar_communication.html.twig';

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
        $campaign = $this->container->get('AdminBundle\Service\MailChimp\MailChimpCampaign');
        $campaign_folders = $campaign->getFolders();

        return $this->render('AdminBundle:Communication:emailing_compaign.html.twig', array(
            "folders" => $campaign_folders["folders"],
        ));
    }

    /**
     * @Route("/emailing/campagne/new/folder", name="admin_communication_emailing_compaign_new_folder")
     * @Method("POST")
     */
    public function emailingCampaignNewFolderAction(Request $request)
    {
        $campaign = $this->container->get('AdminBundle\Service\MailChimp\MailChimpCampaign');
        $response = $campaign->createFolder($request->get('name'));

        return new JsonResponse($response);
    }

    /**
     * @Route("/emailing/templates", name="admin_communication_emailing_templates")
     */
    public function emailingTemplatesAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        return $this->render('AdminBundle:Communication:emailing_templates.html.twig', array(
            'template_model_class' => new TemplateModel(),
        ));
    }

    /**
     * @Route(
     *     "/emailling/templates/ajout-template/{model}",
     *     name="admin_communication_emailing_templates_add_template",
     *     defaults={"model"=null}
     * )
     */
    public function emailingTemplatesAddTemplateAction(Request $request, $model)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        $json_response_data_provider = $this->get('AdminBundle\Service\JsonResponseData\StandardDataProvider');
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

        if ($request->isMethod('GET')) {
            if (!is_null($model) && in_array($model, $valid_models)) {
                $form_view =  $this->renderView(
                    'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                    array(
                        'manip_template_form' => $add_template_form->createView(),
                        'current_template_model' => $model,
                        'template_model_class' => new TemplateModel(),
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
                    $manager = $this->get('AdminBundle\Manager\ComEmailTemplateManager');
                    $manager->createTemplate($program, $com_email_template);
                    $data = $json_response_data_provider->success();
                    return new JsonResponse($data, 200);
                } else {
                    $data = $json_response_data_provider->formError();
                    $form_view =  $this->renderView(
                        'AdminBundle:Communication/EmailingTemplates:manip_template.html.twig',
                        array(
                            'manip_template_form' => $add_template_form->createView(),
                        )
                    );
                    $data['content'] = $form_view;
                    return new JsonResponse($data, 200);
                }
            }
        }

        return new JsonResponse($json_response_data_provider->pageNotFound(), 404);
    }
}
