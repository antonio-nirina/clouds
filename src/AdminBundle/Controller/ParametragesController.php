<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use AdminBundle\Entity\RegistrationFormData;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Entity\Program;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SiteFormType;
use AdminBundle\Form\FormStructureDeclarationType;
use AdminBundle\Form\FormStructureType;
use AdminBundle\Component\SiteForm\FieldTypeName;
use AdminBundle\Form\RegistrationImportType;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use AdminBundle\Form\RegistrationFormHeaderDataType;
use AdminBundle\Form\RegistrationFormIntroDataType;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Route("/admin/parametrages")
 */
class ParametragesController extends Controller
{

    public function sidebarAction()
    {
        $em = $this->getDoctrine()->getManager();

        $url = 'cloud-rewards.peoplestay.com';

        $program = $em->getRepository('AdminBundle:Program')->findByUrl($url);
        $level = $program[0]->getParamLevel();

        return $this->render('AdminBundle:Parametrages:menu-sidebar-parametrages.html.twig', array(
                                    'level' => $level
                                ));
    }

    /**
     * @Route("/", name="admin_parametrages_programme")
     * @Method({"GET","POST"})
     */
    public function programmeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $url = 'cloud-rewards.peoplestay.com';
        $program = $em->getRepository('AdminBundle:Program')->findByUrl($url);

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $program[0];
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

    public function inscriptionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $program = $programs[0];
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
                    $fields_manager->adjustFieldOrder($field_order, $current_field_list);

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
     * @Route("/inscriptions/imports", name="admin_parametrages_inscriptions_imports")
     */
    public function importsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $programs = $em->getRepository(Program::class)->findAll();
        if (empty($programs) || is_null($programs[0])) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $program = $programs[0];
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
    public function importDownloadModel()
    {
        $response = $this->get('AdminBundle\Service\ImportExport\RegistrationModel')->createResponse();

        return $response;
    }
    /**
     * @Route("/resultats/declaration", name="admin_resultats_declaration")
     */
    public function formulaireDeclarationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

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

        $current_level = 1;
        if ($request->get('product')) {
            $current_level = (int) $request->get('product');
        }

        $form_structure_form = $this->createForm(FormStructureDeclarationType::class);
        $form_structure_form->handleRequest($request);

        $fields_manager = $this->container->get('admin.form_field_manager');
        $site_form_setting = $fields_manager->rechargeDefaultFieldFor($program, $site_form_type, $current_level);
        $site_form_field_settings = $site_form_setting->getSiteFormFieldSettings();


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
            $fields_manager->adjustFieldOrder($field_order, $current_field_list);

            //add new field
            $new_field_list = $form_structure_form->getData()['new-field-list'];
            $fields_manager->addNewFields($new_field_list, $site_form_setting, $current_level);

            //delete field
            $delete_field_list = $form_structure_form->getData()['delete-field-action-list'];
            $fields_manager->deleteField($delete_field_list, $site_form_setting);
            //save modification
            $fields_manager->save();

            $next_form = $form_structure_form->getData()['next'];//next product
            // dump($next_form); die;
            if (!empty($next_form)) {
                return $this->redirect($this->generateUrl('admin_resultats_declaration', array(
                    'product' => $current_level+1
                )));
            }

            $program->setParamLevel(3);
            $em->flush();
            
            return $this->redirectToRoute('admin_resultats_declaration');
        }

        return $this->render('AdminBundle:Parametrages:Declarations.html.twig', array(
            'site_form_setting' => $site_form_setting,
            'form_structure_form' => $form_structure_form->createView(),
            'site_form_field_settings' => $site_form_field_settings,
            'field_type_list' => FieldTypeName::FIELD_NAME,
            'custom_field_allowed' => $site_form_setting->getCustomFieldAllowed(),
            'next_level' => $current_level+1,
            'current_level' => $current_level
        ));
    }
}
