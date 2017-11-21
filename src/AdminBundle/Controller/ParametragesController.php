<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use AdminBundle\Component\SiteForm\FieldTypeName;
use AdminBundle\Component\SiteForm\SiteFormType;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use AdminBundle\Component\SiteForm\FieldType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

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
            return $this->redirectToRoute('fos_user_security_logout');
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
            return $this->redirectToRoute('fos_user_security_logout');
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
}
