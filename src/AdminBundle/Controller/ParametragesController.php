<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use AdminBundle\Component\SiteForm\FieldType;
use AdminBundle\Component\SiteForm\FieldTypeName;
use AdminBundle\Component\SiteForm\SiteFormType;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use AdminBundle\Entity\LoginPortalSlide;
use AdminBundle\Entity\Program;
use AdminBundle\Entity\RegistrationFormData;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Form\FormStructureDeclarationType;
use AdminBundle\Form\FormStructureType;
use AdminBundle\Form\RegistrationFormHeaderDataType;
use AdminBundle\Form\RegistrationFormIntroDataType;
use AdminBundle\Form\RegistrationImportType;
use AdminBundle\Form\ResultSettingType;
use AdminBundle\Form\ResultSettingUploadType;
use AdminBundle\Form\SiteDesignSettingType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use AdminBundle\Form\LoginPortalDataType;
use AdminBundle\Form\HomePageSlideDataType;
use AdminBundle\Entity\HomePageSlide;
use AdminBundle\Form\HomePageEditorialType;

/**
 * @Route("/admin/parametrages")
 */
class ParametragesController extends Controller
{

    public function sidebarAction($active)
    {
        $em = $this->getDoctrine()->getManager();

        $program = $this->container->get('admin.program')->getCurrent();
        $level = $program->getParamLevel();

        return $this->render('AdminBundle:Parametrages:menu-sidebar-parametrages.html.twig', array(
                                    'level' => $level,
                                    'active' => $active
                                ));
    }

    public function rootAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        $site_design = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $site_design = $site_design[0];

        $has_root = $this->container->get('app.design_root')->exists($program->getId());
        return $this->render('root.html.twig', array(
                                    'link' => $has_root
                                ));
    }

    public function logoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        $site_design = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $site_design = $site_design[0];
        $logo_path = false;
        $name = false;

        if ($file = $site_design->getLogoPath()) {
            $logo_path = $this->container->getParameter('logo_path').'/'.$program->getId().'/'.$site_design->getLogoPath();
        } else if ($site_design->getLogoName()) {
            $name = true;
        }
        return $this->render('logo.html.twig', array(
                                    'link' => $logo_path,
                                    'name' => $name
                                ));
    }

    public function logoLoginAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        $site_design = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $site_design = $site_design[0];
        $logo_path = false;
        $name = false;

        if ($file = $site_design->getLogoPath()) {
            $logo_path = $this->container->getParameter('logo_path').'/'.$program->getId().'/'.$site_design->getLogoPath();
        } else if ($site_design->getLogoName()) {
            $name = true;
        }
        return $this->render('logo_login.html.twig', array(
                                    'link' => $logo_path,
                                    'name' => $name
                                ));
    }

    /**
     * @Route("/", name="admin_parametrages_programme")
     * @Method({"GET","POST"})
     */
    public function programmeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $current_program = $program->getType();
        $program_type_repo = $em->getRepository('AdminBundle:ProgramType');
        $all_program_type = $program_type_repo->findAll();

        if ("POST" === $request->getMethod()) {
            $type = (int) $request->get('program_type');
            $is_multi_operation = ((int) $request->get('challenge_mode'))?true:false;

            $changed_type = true;
            if ($type === $program->getType()->getId()) {
                $changed_type = false;
            } else {
                $new_type = $program_type_repo->find($type);
                $program->setType($new_type);
            }

            if ($is_multi_operation != $program->getIsMultiOperation()) {
                $program->setIsMultiOperation($is_multi_operation);
            }

            if (($program->getParamLevel() < 1) || $changed_type) { //remettre au level 1
                $program->setParamLevel(1);
            }

            $em->flush();

            return $this->redirectToRoute('admin_parametrages_inscriptions');
        }

        return $this->render('AdminBundle:Parametrages:Programme.html.twig', array(
                                    'all_program_type' => $all_program_type,
                                    'program' => $program
                                ));
    }

    /**
     * @Route("/inscriptions", name="admin_parametrages_inscriptions")
     */

    public function createRegistrationFormAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        /*$programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $program = $programs[0];*/

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registration_site_form_setting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndTypeWithField($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $registration_site_form_field_settings = $registration_site_form_setting->getSiteFormFieldSettings();

        $registration_form_data = $program->getRegistrationFormData();
        if (is_null($registration_form_data)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $current_header_image = $registration_form_data->getHeaderImage();
        $registration_form_data->setHeaderImage("");

        $form_factory = $this->get("form.factory");
        $form_structure_form = $form_factory->createNamed("form_structure", FormStructureType::class);
        $header_data_form = $form_factory->createNamed(
            "header_data",
            RegistrationFormHeaderDataType::class,
            $registration_form_data
        );
        $intro_data_form = $form_factory->createNamed(
            "introduction_data",
            RegistrationFormIntroDataType::class,
            $registration_form_data
        );

        if ("POST" === $request->getMethod()) {
            if ($request->request->has("form_structure")) {
                $form_structure_form->handleRequest($request);
                if ($form_structure_form->isSubmitted() && $form_structure_form->isValid()) {
                    $fields_manager = $this->container->get("admin.form_field_manager");

                    $field_order = $form_structure_form->getData()["field-order"];

                    $current_field_list = $form_structure_form->getData()["current-field-list"];
                    $fields_manager->adjustFieldAndOrder($field_order, $current_field_list);

                    $new_field_list = $form_structure_form->getData()["new-field-list"];
                    $fields_manager->addNewFields($new_field_list, $registration_site_form_setting);

                    $delete_field_list = $form_structure_form->getData()["delete-field-action-list"];
                    $fields_manager->deleteField($delete_field_list, $registration_site_form_setting);

                    $fields_manager->save();

                    return $this->redirectToRoute("admin_parametrages_inscriptions");
                }
            }

            if ($request->request->has("header_data")) {
                $header_data_form->handleRequest($request);
                if ($header_data_form->isSubmitted() && $header_data_form->isValid()) {
                    $header_image_file = $registration_form_data->getHeaderImage();
                    if (!is_null($header_image_file)) {
                        $header_image_file->move(
                            $this->getParameter("registration_header_image_upload_dir"),
                            $header_image_file->getClientOriginalName()
                        );
                        $registration_form_data->setHeaderImage($header_image_file->getClientOriginalName());
                    } else {
                        $registration_form_data->setHeaderImage($current_header_image);
                    }

                    $em->flush();

                    return $this->redirectToRoute("admin_parametrages_inscriptions");
                }
            }

            if ($request->request->has("introduction_data")) {
                $intro_data_form->handleRequest($request);
                if ($intro_data_form->isSubmitted() && $intro_data_form->isValid()) {
                    $registration_form_data->setHeaderImage($current_header_image);
                    $em->flush();

                    return $this->redirectToRoute("admin_parametrages_inscriptions");
                }
            }
        }

        return $this->render("AdminBundle:Parametrages:Inscriptions.html.twig", array(
            "site_form_field_settings" => $registration_site_form_field_settings,
            "form_structure_form" => $form_structure_form->createView(),
            "field_type_list" => FieldTypeName::FIELD_NAME,
            "custom_field_allowed" => $registration_site_form_setting->getCustomFieldAllowed(),
            "header_data_form" =>  $header_data_form->createView(),
            "current_header_image" => $current_header_image,
            "intro_data_form" => $intro_data_form->createView(),
        ));
    }

    /**
     * @Route("/inscriptions/creation-formulaire/nouveau-champ", name="admin_new_registration_form_field")
     */
    public function addRegistrationFormFieldAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $site_form_field_setting_manager = $this->container->get('admin.form_field_manager');

        /*$programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return new Response('');
        }
        $program = $programs[0];*/

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
//            return $this->redirectToRoute('fos_user_security_logout');
            return new Response('');
        }

        $registration_site_form_setting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndTypeWithField($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return new Response('');
        }

        if ($request->isMethod('GET')) {
            return $this->render("AdminBundle:Parametrages:manip_registration_form_field.html.twig", array(
                "type" => FieldType::TEXT,
                "field_type" => new FieldType(),
            ));
        }

        if ($request->isMethod('POST')) {
            if ($request->get('validate')
                && $request->get('label')
                && $request->get('field_type')
                && !is_null($request->get('field_type'))
            ) {
                $new_field = array(
                    "mandatory" => false,
                    "label" => $request->get('label'),
                    "field_type" => $request->get('field_type'),
                    "special_field_index" => array(SpecialFieldIndex::USER_FIELD),
                );
                if (FieldType::CHOICE_RADIO == $request->get('field_type')) {
                    $yes_no_choices_array = array(
                        "oui" => "oui",
                        "non" => "non,"
                    );
                    $new_field["choices"] = $yes_no_choices_array;
                }
                $field = $site_form_field_setting_manager->addNewField(
                    $new_field,
                    $registration_site_form_setting,
                    true
                );
                if (!is_null($field)) {
                    $response = $this->forward(
                        'AdminBundle:PartialPage:siteFormFieldRow',
                        array('field' => $field)
                    );

                    return $response;
                }
            }
        }

        return new Response('');
    }

    /**
     * @Route("/inscriptions/creation-formulaire/editer-champ", name="admin_edit_registration_form_field")
     */
    public function editRegistrationFormFieldAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $site_form_field_setting_manager = $this->container->get('admin.form_field_manager');

        /*$programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return new Response('');
        }
        $program = $programs[0];*/

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
//            return $this->redirectToRoute('fos_user_security_logout');
            return new Response('');
        }

        $registration_site_form_setting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndTypeWithField($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return new Response('');
        }

        if ($request->isMethod('GET')) {
            if ($request->get('field_id')) {
                $field = $em->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                    ->findBySiteFormSettingAndId($registration_site_form_setting, $request->get('field_id'));
                if (!is_null($field)) {
                    $custom_choice_radio_choices = array();
                    if (FieldType::CHOICE_RADIO == $field->getFieldType()) {
                        $custom_choice_radio_choices["choices"] = array();
                        if (array_key_exists("choices", $field->getAdditionalData())) {
                            $custom_choice_radio_choices = $field->getAdditionalData()["choices"];
                        }
                    }
                    return $this->render(
                        "AdminBundle:Parametrages:manip_registration_form_field.html.twig",
                        array(
                            "type" => $field->getFieldType(),
                            "custom_choice_radio_choices" => $custom_choice_radio_choices,
                            "field_id" => $field->getId(),
                            "field_type" => new FieldType(),
                            "label" => $field->getLabel(),
                        )
                    );
                }
            }
        }

        if ($request->isMethod('POST')) {
            if ($request->get('validate')
                && $request->get('label')
                && $request->get('field_type')
                && !is_null($request->get('field_type'))
                && $request->get('field_id')
            ) {
                $field = $em->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                    ->findBySiteFormSettingAndId($registration_site_form_setting, $request->get('field_id'));
                if (!is_null($field)) {
                    $custom_choices = null;
                    if ($request->get('options')) {
                        $custom_choices = $request->get('options');
                    }
                    $site_form_field_setting_manager->updateFieldWithCustomChoices(
                        $field,
                        $request->get('field_type'),
                        $request->get('label'),
                        $custom_choices
                    );
                    $response = $this->forward(
                        'AdminBundle:PartialPage:siteFormFieldRow',
                        array('field' => $field)
                    );

                    return $response;
                }
            }
        }

        return new Response('');
    }

    /**
     * @Route("/inscriptions/imports", name="admin_parametrages_inscriptions_imports")
     */
    public function importRegistrationDataAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /*$programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $program = $programs[0];*/

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registration_site_form_setting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $registration_import_form = $this->createForm(RegistrationImportType::class);
        $registration_import_form->handleRequest($request);
        $error_list = array();
        if ($registration_import_form->isSubmitted() && $registration_import_form->isValid()) {
            $import_file = $registration_import_form->getData()["registration_data"];
            $registration_handler = $this->get("AdminBundle\Service\ImportExport\RegistrationHandler");
            $registration_handler->setSiteFormSetting($registration_site_form_setting);
            $registration_handler->import($import_file);

            if (!empty($registration_handler->getErrorList())) {
                $error_list = $registration_handler->getErrorList();
            } else {
                $this->addFlash('success_message', 'Import de données effectué avec succès');
                return $this->redirectToRoute("admin_parametrages_inscriptions_imports");
            }
        }

        return $this->render("AdminBundle:Parametrages:Imports.html.twig", array(
            "registration_form" => $registration_import_form->createView(),
            "error_list" => $error_list,
        ));
    }

    /**
     * @Route("/inscriptions/imports/telecharger-modele", name="admin_parametrages_inscriptiions_imports_telecharger_modele")
     */
    public function downloadRegistrationModelAction()
    {
        $em = $this->getDoctrine()->getManager();
        /*$programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $program = $programs[0];*/

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registration_site_form_setting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $model = $this->get('AdminBundle\Service\ImportExport\RegistrationModel');
        $model->setSiteFormSetting($registration_site_form_setting);
        $response = $model->createResponse();

        return $response;
    }


    /**
     * @Route("/inscriptions/imports/etre-contacte",  name="admin_parameters_registration_import_be_contacted")
     */
    public function beContactedAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            || !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $em = $this->getDoctrine()->getManager();

        /*$programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $program = $programs[0];*/

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registration_site_form_setting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $mailer = $this->get('AdminBundle\Service\Mailer\SuperAdminNotificationMailer');
        $mailer->sendBeContactedNotification($this->getUser());
        if (!empty($mailer->getErrorList())) {
            $this->addFlash('failure_email_sent_message', 'Erreur. Demande de contact non envoyé.');
        } else {
            $this->addFlash('success_email_sent_message', 'Demande de contact envoyé.');
        }

        return $this->redirectToRoute("admin_parametrages_inscriptions_imports");
    }


    /**
     * @Route("/resultats/declaration/new", name="admin_new_resultat_declaration")
     * @Method("POST")
     */
    public function newFormulaireDeclaration()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        if ("Challenge" === $program->getType()->getType()) {
            $site_form_type = SiteFormType::PRODUCT_DECLARATION_TYPE;
            $default_lines = 5;
        } else {
            $site_form_type = SiteFormType::LEAD_DECLARATION_TYPE;
        }

        $fields_manager = $this->container->get('admin.form_field_manager');
        $all_level = $fields_manager->getMaxLevel($program, $site_form_type);
        $max_level = (!empty($all_level))?(int) $all_level[0]['level']:0;
        $new_level = $max_level+1;

        $fields_manager->rechargeDefaultFieldFor($program, $site_form_type, $new_level);

        $site_form_setting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeAndLevelWithField($program, $site_form_type, $new_level);

        // dump($site_form_setting); die;

        return $this->render('AdminBundle:Parametrages:New_declaration.html.twig', array(
            'site_form_setting' => $site_form_setting,
            'site_form_field_settings' => $site_form_setting->getSiteFormFieldSettings(),
            'max_line' => $site_form_setting->getCustomFieldAllowed() + $default_lines
        ));
    }

    /**
     * @Route("/resultats/declaration/delete/{level}", name="admin_delete_resultat_declaration")
     * @Method("POST")
     */
    public function deleteFormulaireDeclaration($level)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        if ("Challenge" === $program->getType()->getType()) {
            $site_form_type = SiteFormType::PRODUCT_DECLARATION_TYPE;
        } else {
            $site_form_type = SiteFormType::LEAD_DECLARATION_TYPE;
        }

        $fields_manager = $this->container->get('admin.form_field_manager');
        $fields_manager->removeFieldsForLevel($program, $site_form_type, $level);

        return new Response('done');
    }

    /**
     * @Route("/resultats/declaration/new_field", name="admin_new_field_declaration")
     * @Method("POST")
     */
    public function newFieldDeclaration(Request $request)
    {
        $level = ($request->get('level'))?$request->get('level'):'';
        $type = ($request->get('type_field'))?$request->get('type_field'):"alphanum";
        $label = ($request->get('label'))?$request->get('label'):"";
        $field_id = ($request->get('field_id'))?$request->get('field_id'):"";

        $em = $this->getDoctrine()->getManager();
        $fields_manager = $this->container->get('admin.form_field_manager');
        
        if (!empty($field_id)) {//update
            $field = $em->getRepository('AdminBundle:SiteFormFieldSetting')->find($field_id);

            if ($request->get('update')) {
                $field = $fields_manager->updateField($field, $type, $label);
                return $this->render('AdminBundle:Parametrages:Partial_new.html.twig', array(
                    'field' => $field,
                    'label' => $label,
                    'personalize' => true
                ));
            } else {
                $row = $field->getInRow();
                $type = ($row)?"period":$field->getFieldType();
                $type = ($type == 'text')?"alphanum":$type;
                $label = $field->getLabel();
            }
        }

        if ($request->get('validate')) {//validate
            $new_field = [
                            'level' => $level,
                            'label' => $label,
                            "mandatory" => false,
                            "field_type" => $type
                            ];
            if ($type == "choice-radio") {
                $new_field["choices"] = ["oui"=>"oui","non"=>"non"];
            }

            $url = 'cloud-rewards.peoplestay.com';
            $program = $em->getRepository('AdminBundle:Program')->findByUrl($url);

            if (empty($program)) {//redirection si program n'existe pas
                return $this->redirectToRoute('fos_user_security_logout');
            }

            $program = $program[0];
            if ("Challenge" === $program->getType()->getType()) {
                $site_form_type = SiteFormType::PRODUCT_DECLARATION_TYPE;
            } else {
                $site_form_type = SiteFormType::LEAD_DECLARATION_TYPE;
            }

            $site_form_setting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeWithFieldWithLevel($program, $site_form_type);
            $field = $fields_manager->addNewField($new_field, $site_form_setting);

            // dump($field); die;
            return $this->render('AdminBundle:Parametrages:Partial_new.html.twig', array(
                'field' => $field,
                'label' => $label,
                'personalize' => true
            ));
        }
        
        return $this->render('AdminBundle:Parametrages:New_field_declaration.html.twig', array(
            'level' => $level,
            'type' => $type,
            'label' => $label,
            'field_id' => $field_id
        ));
    }

    /**
     * @Route("/resultats/declaration", name="admin_resultats_declaration")
     */
    public function formulaireDeclarationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        if ("Challenge" === $program->getType()->getType()) {
            $site_form_type = SiteFormType::PRODUCT_DECLARATION_TYPE;
            $default_lines = 5;
        } else {
            $site_form_type = SiteFormType::LEAD_DECLARATION_TYPE;
        }

        $fields_manager = $this->container->get('admin.form_field_manager');
        $all_level = $fields_manager->getMaxLevel($program, $site_form_type);
        
        $max_level = (!empty($all_level))?(int) $all_level[0]['level']:1;
        if (empty($all_level)) {
            $fields_manager->rechargeDefaultFieldFor($program, $site_form_type, $max_level);
        }

        $site_form_setting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeWithFieldWithLevel($program, $site_form_type);
        $arranged_fields = $fields_manager->getArrangedFields($site_form_setting);

        $form_structure_form = $this->createForm(FormStructureDeclarationType::class);
        $form_structure_form->handleRequest($request);

        if ($form_structure_form->isSubmitted() && $form_structure_form->isValid()) {
            //dump($request); die;
            //validation
            $validation_required =  ((int) $form_structure_form->getData()['validation-required'])?true:false;
            $site_form_setting->setValidation($validation_required);

            //pieces justificatif
            $pieces_required = ((int) $form_structure_form->getData()['pieces-required'])?true:false;
            $site_form_setting->setHasPieces($pieces_required);

            //texte head
            $text_head_required =  ((int) $form_structure_form->getData()['text-head-required'])?true:false;
            $site_form_setting->setHasHeadText($text_head_required);

            if ($text_head_required) {
                $text_head =  $form_structure_form->getData()['text-head'];
                $site_form_setting->setHeadText($text_head);
            }
            
            //adjust current field
            $field_order = $form_structure_form->getData()['field-order'];
            $current_field_list = $form_structure_form->getData()['current-field-list'];
            $fields_manager->adjustFieldAndOrder($field_order, $current_field_list);

            //add new field
            // $new_field_list = $form_structure_form->getData()['new-field-list'];
            // if (!empty($new_field_list)) {
            //     $fields_manager->addNewFields($new_field_list, $site_form_setting, true);
            // }

            //delete field
            $delete_field_list = $form_structure_form->getData()['delete-field-action-list'];
            $fields_manager->deleteField($delete_field_list, $site_form_setting, true);
            //save modification
            $fields_manager->save();

            $next_form = $form_structure_form->getData()['next'];//next product
            // // dump($next_form); die;
            // if (!empty($next_form)) {
            //     return $this->redirect($this->generateUrl('admin_resultats_declaration', array(
            //         'product' => $current_level+1
            //     )));
            // }

            $program->setParamLevel(3);
            $em->flush();
            
            return $this->redirectToRoute('admin_resultats_declaration');
        }

        return $this->render('AdminBundle:Parametrages:Declarations.html.twig', array(
            'site_form_setting' => $site_form_setting,
            'form_structure_form' => $form_structure_form->createView(),
            'fields' => $arranged_fields,
            'field_type_list' => FieldTypeName::FIELD_NAME,
            'max_line' => $site_form_setting->getCustomFieldAllowed()+ $default_lines
        ));
    }

    /**
     * @Route("/resultats/declaration/import", name="admin_resultats_declaration_import")
     */
    public function importDeclarationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $result_setting = $em->getRepository('AdminBundle:ResultSetting')->findByProgram($program);
        $result_setting = $result_setting[0];
        $setting_form = $this->createForm(ResultSettingType::class, $result_setting);
        $upload_form = $this->createForm(ResultSettingUploadType::class);
        
        if ($request->get('result_setting')) {//download model
            $setting_form->handleRequest($request);
            if ($setting_form->isSubmitted() && $setting_form->isValid()) {
                $em->flush();
                $monthly = $result_setting->getMonthly();
                $by_product = $result_setting->getByProduct();
                $by_rank = $result_setting->getByRank();
                $response = $this->get('AdminBundle\Service\ImportExport\ResultSettingModel')
                ->createResponse($monthly, $by_product, $by_rank);
                return $response;
            }
        }
        
        $error_list = array();
        $fresh_upload_form = $upload_form;
        if ($request->get('result_setting_upload')) {//upload fichier
            $upload_form->handleRequest($request);
            if ($upload_form->isSubmitted() && $upload_form->isValid()) {
                $imported_file = $upload_form->getData()["uploaded_file"];
                $result_setting_handler = $this->get("AdminBundle\Service\ImportExport\ResultSettingHandler");
                $result_setting_handler->setResultSetting($result_setting);
                $result_setting_handler->import($imported_file);

                if (!empty($result_setting_handler->getErrorList())) {
                    $error_list = $result_setting_handler->getErrorList();
                } else {
                    $this->addFlash('success_message', 'Import de données effectué avec succès');
                    return $this->redirectToRoute("admin_resultats_declaration_import");
                }
            }
        }
        // $upload_form->refresh();

        return $this->render('AdminBundle:Parametrages:Import_declaration.html.twig', array(
            'form_upload' => $upload_form->createView(),
            'setting_form' => $setting_form->createView(),
            'error_list' => $error_list
        ));
    }

    /**
     * @Route("/design", name="admin_param_design")
     */
    public function designAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $site_design = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $site_design = $site_design[0];
        if ($file = $site_design->getLogoPath()) {
            $logo_path = $this->container->get('admin.logo')->getTargetDir().'/'.$program->getId();
            $site_design->setLogoPath(new File($logo_path.'/'.$file));
        }

        $site_design_form_logo = $this->createForm(SiteDesignSettingType::class, $site_design)
                                      ->remove('police')
                                      ->remove('colors');//form logo

        $site_design_form_colors = $this->createForm(SiteDesignSettingType::class, $site_design)
                                        ->remove('police')
                                        ->remove('logo_name')
                                        ->remove('logo_path');//form couleurs

        $site_design_form_police = $this->createForm(SiteDesignSettingType::class, $site_design)
                                        ->remove('colors')
                                        ->remove('logo_name')
                                        ->remove('logo_path');//form police

        if ($request->get('site_design_setting')) {
            if (array_key_exists('logo_name', $request->get('site_design_setting'))) {//logo
                $site_design_form_logo->handleRequest($request);
                // dump($site_design_form_logo->isValid()); die;
                if ($site_design_form_logo->isSubmitted() && $site_design_form_logo->isValid()) {
                    if ($request->files->get('site_design_setting')) {
                        $file = $site_design->getLogoPath();
                        $file_name = $this->container->get('admin.logo')->upload($file, $program->getId());
                        $site_design->setLogoPath($file_name);
                    } else {
                        $site_design->setLogoPath($file);
                    }

                    $this->container->get('app.design_root')->resetRoot(
                        $program->getId(),
                        $site_design
                    );
                    $em->flush();
                    $this->redirectToRoute('admin_param_design');
                }
            }

            if (array_key_exists('colors', $request->get('site_design_setting'))) {//couleur
                $site_design_form_colors->handleRequest($request);
                if ($site_design_form_colors->isSubmitted() && $site_design_form_colors->isValid()) {
                    $this->container->get('app.design_root')->resetRoot(
                        $program->getId(),
                        $site_design
                    );
                    if ($file) {
                        $site_design->setLogoPath($file);
                    }
                    $em->flush();
                    $this->redirectToRoute('admin_param_design');
                }
            }

            if (array_key_exists('police', $request->get('site_design_setting'))) {//police
                $site_design_form_police->handleRequest($request);
                if ($site_design_form_police->isSubmitted() && $site_design_form_police->isValid()) {
                    $this->container->get('app.design_root')->resetRoot(
                        $program->getId(),
                        $site_design
                    );
                    if ($file) {
                        $site_design->setLogoPath($file);
                    }
                    $em->flush();
                    $this->redirectToRoute('admin_param_design');
                }
            }
        }

        return $this->render('AdminBundle:Parametrages:Design.html.twig', array(
            'site_design_form_logo' => $site_design_form_logo->createView(),
            'site_design_form_colors' => $site_design_form_colors->createView(),
            'site_design_form_police' => $site_design_form_police->createView(),
        ));
    }

    /**
     * @Route("/contenus/portail-identification", name="admin_content_configure_login_portal")
     */
    public function configureLoginPortalAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $login_portal_data = $program->getLoginPortalData();
        if (is_null($login_portal_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $original_slides = new ArrayCollection();
        foreach ($login_portal_data->getLoginPortalSlides() as $slide) {
            $original_slides->add($slide);
        }

        $original_slides_image = array();
        foreach ($original_slides as $slide) {
            $original_slides_image[$slide->getId()] = $slide->getImage();
        }

        $form_factory = $this->get('form.factory');
        $login_portal_data_form = $form_factory->createNamed(
            "login_portal_data_form",
            LoginPortalDataType::class,
            $login_portal_data
        );
        $login_portal_data_form->handleRequest($request);

        if ($login_portal_data_form->isSubmitted() && $login_portal_data_form->isValid()) {
            foreach ($login_portal_data->getLoginPortalSlides() as $slide) {
                if (is_null($slide->getId())) {
                    $slide->setLoginPortalData($login_portal_data);
                    $em->persist($slide);
                }

                if (is_null($slide->getImage())) {
                    if (array_key_exists($slide->getId(), $original_slides_image)) {
                        $slide->setImage($original_slides_image[$slide->getId()]);
                    }
                } else {
                    $image = $slide->getImage();
                    $image->move(
                        $this->getParameter('content_login_portal_slide_image_upload_dir'),
                        $image->getClientOriginalName()
                    );
                    $slide->setImage($image->getClientOriginalName());
                }
            }

            foreach ($original_slides as $original_slide) {
                if (false === $login_portal_data->getLoginPortalSlides()->contains($original_slide)) {
                    $original_slide->setLoginPortalData(null);
                    $login_portal_data->removeLoginPortalSlide($original_slide);
                    $em->remove($original_slide);
                }
            }
            $em->flush();

            return $this->redirectToRoute("admin_content_configure_login_portal");
        }

        return $this->render('AdminBundle:Parametrages:content_configure_login_portal.html.twig', array(
            'login_portal_data_form' => $login_portal_data_form->createView(),
            'original_slides_image' => $original_slides_image,
        ));
    }

    /**
     * @Route("/contenus/portail-identification/ajout-slide", name="admin_content_configure_login_portal_add_slide")
     */
    public function addLoginPortalSlideAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $login_portal_data = $program->getLoginPortalData();
        if (is_null($login_portal_data)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $max_slide_order = 0;
        if (!$login_portal_data->getLoginPortalSlides()->isEmpty()) {
            $max_slide_order = $em->getRepository('AdminBundle\Entity\LoginPortalData')
                ->retrieveMaxSlideOrderByLoginPortalData($login_portal_data);
        }

        $new_slide = new LoginPortalSlide();
        $new_slide->setSlideOrder($max_slide_order + 1);
        $new_slide->setLoginPortalData($login_portal_data);
        $login_portal_data->addLoginPortalSlide($new_slide);
        $em->persist($new_slide);
        $em->flush();

        return new Response($new_slide->getId());
    }

    /**
     * @Route(
     *     "/contenus/portail-identification/suppression-slide/{id}",
     *     name="admin_content_configure_login_portal_delete_slide"),
     *     requirements={"id": "\d+"}
     */
    public function deleteLoginPortalSlideAction($id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $login_portal_data = $program->getLoginPortalData();
        if (is_null($login_portal_data)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $to_del_slide = $em->getRepository('AdminBundle\Entity\LoginPortalSlide')
            ->findOneBy(array(
                'login_portal_data' => $login_portal_data,
                'id' => $id,
            ));
        if (is_null($to_del_slide)) {
            return new Response('');
        }

        $login_portal_data->removeLoginPortalSlide($to_del_slide);
        $to_del_slide->setLoginPortalData(null);
        $em->remove($to_del_slide);
        $em->flush();

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route("/contenus/page-accueil", name="admin_content_configure_home_page")
     */
    public function configureHomePageAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $editorial = $home_page_data->getEditorial();
        if (is_null($editorial)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $original_slides = new ArrayCollection();
        foreach ($home_page_data->getHomePageSlides() as $slide) {
            $original_slides->add($slide);
        }

        $original_slides_image = array();
        foreach ($original_slides as $slide) {
            $original_slides_image[$slide->getId()] = $slide->getImage();
        }

        $form_factory = $this->get('form.factory');
        $home_page_slide_data_form = $form_factory->createNamed(
            'home_page_slide_data_form',
            HomePageSlideDataType::class,
            $home_page_data
        );
        $home_page_editorial_data_form = $form_factory->createNamed(
            'home_page_editorial_data_form',
            HomePageEditorialType::class,
            $editorial
        );

        $em = $this->getDoctrine()->getManager();
        if ("POST" === $request->getMethod()) {
            if ($request->request->has('home_page_slide_data_form')) {
                $home_page_slide_data_form->handleRequest($request);
                if ($home_page_slide_data_form->isSubmitted() && $home_page_slide_data_form->isValid()) {
                    foreach ($home_page_data->getHomePageSlides() as $slide) {
                        if (is_null($slide->getId())) {
                            $slide->setHomePageData($home_page_data);
                            $em->persist($slide);
                        }

                        if (is_null($slide->getImage())) {
                            if (array_key_exists($slide->getId(), $original_slides_image)) {
                                $slide->setImage($original_slides_image[$slide->getId()]);
                            }
                        } else {
                            $image = $slide->getImage();
                            $image->move(
                                $this->getParameter('content_home_page_slide_image_upload_dir'),
                                $image->getClientOriginalName()
                            );
                            $slide->setImage($image->getClientOriginalName());
                        }
                    }

                    foreach ($original_slides as $original_slide) {
                        if (false === $home_page_data->getHomePageSlides()->contains($original_slide)) {
                            $original_slide->setHomePageData(null);
                            $home_page_data->removeHomePageSlide($original_slide);
                            $em->remove($original_slide);
                        }
                    }
                    $em->flush();
                    return $this->redirectToRoute('admin_content_configure_home_page');
                }
            }

            if ($request->request->has('home_page_editorial_data_form')) {
                $home_page_editorial_data_form->handleRequest($request);
                if ($home_page_editorial_data_form->isSubmitted() && $home_page_editorial_data_form->isValid()) {
                    $editorial->setLastEdit(new \DateTime(
                        'now',
                        new \DateTimeZone($this->getParameter('app_time_zone'))
                    ));
                    $em->flush();
                    return $this->redirectToRoute('admin_content_configure_home_page');
                }
            }
        }


        return $this->render('AdminBundle:Parametrages:content_configure_home_page.html.twig', array(
            'home_page_slide_data_form' => $home_page_slide_data_form->createView(),
            'home_page_editorial_data_form' => $home_page_editorial_data_form->createView(),
            'original_slides_image' => $original_slides_image,
        ));
    }

    /**
     * @Route("/contenus/page-accueil/ajout-slide", name="admin_content_configure_home_page_add_slide")
     */
    public function addHomePageSlideAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $max_slide_order = 0;
        if (!$home_page_data->getHomePageSlides()->isEmpty()) {
            $max_slide_order = $em->getRepository('AdminBundle\Entity\HomePageData')
                ->retrieveMaxSlideOrderByHomePageData($home_page_data);
        }
        $new_slide = new HomePageSlide();
        $new_slide->setSlideOrder($max_slide_order + 1)
            ->setHomePageData($home_page_data);
        $home_page_data->addHomePageSlide($new_slide);

        $em->persist($new_slide);
        $em->flush();

        return new Response($new_slide->getId());
    }

    /**
     * @Route(
     *     "/contenus/page-accueil/suppression-slide/{id}",
     *     name="admin_content_configure_home_page_delete_slide"),
     *     requirements={"id": "\d+"}
     */
    public function deleteHomePageSlideAction($id)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $to_del_slide = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findOneBy(array(
                'home_page_data' => $home_page_data,
                'id' => $id
            ));
        if (is_null($to_del_slide)) {
            return new Response('');
        }

        $home_page_data->removeHomePageSlide($to_del_slide);
        $to_del_slide->setHomePageData(null);
        $em->remove($to_del_slide);
        $em->flush();

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route("/tableaux-reseaux",name="admin_table_network")
     */
    public function tableauReseauAction()
    {
        return $this->render('AdminBundle:Parametrages:table_reseau.html.twig');
    }
	
	/**
     * @Route("/pages-standard",name="admin_pages_standard")
     */
    public function pagesStandardAction()
    {
        return $this->render('AdminBundle:Parametrages:pages_standard.html.twig');
    }
}
