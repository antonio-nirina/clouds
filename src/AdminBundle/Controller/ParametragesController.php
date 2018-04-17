<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use AdminBundle\Component\Post\PostType;
use AdminBundle\Component\SiteForm\FieldType;
use AdminBundle\Component\SiteForm\FieldTypeName;
use AdminBundle\Component\SiteForm\SiteFormType;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;
use AdminBundle\Entity\HomePageSlide;
use AdminBundle\Entity\LoginPortalSlide;
use AdminBundle\Entity\Program;
use AdminBundle\Entity\RegistrationFormData;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Entity\SitePagesStandardDefault;
use AdminBundle\Entity\SitePagesStandardSetting;
use AdminBundle\Form\FormStructureDeclarationType;
use AdminBundle\Form\FormStructureType;
use AdminBundle\Form\HomePageEditorialType;
use AdminBundle\Form\HomePageSlideDataType;
use AdminBundle\Form\LoginPortalDataType;
use AdminBundle\Form\PointAttributionSettingPerformance1Type;
use AdminBundle\Form\PointAttributionSettingPerformance2Type;
use AdminBundle\Form\ProgramPeriodPointType;
use AdminBundle\Form\ProgramPointAttributionType;
use AdminBundle\Form\ProgramRankType;
use AdminBundle\Form\RegistrationFormHeaderDataType;
use AdminBundle\Form\RegistrationFormIntroDataType;
use AdminBundle\Form\RegistrationImportType;
use AdminBundle\Form\ResultSettingType;
use AdminBundle\Form\ResultSettingUploadType;
use AdminBundle\Form\SiteDesignSettingType;
use AdminBundle\Form\SiteTableNetworkSettingType;
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
use AdminBundle\Component\Slide\SlideType;
use Symfony\Component\Finder\Finder;
use AdminBundle\Form\ProductPointType;
use AdminBundle\Controller\AdminController;

/**
 * @Route("/admin/parametrages")
 */
class ParametragesController extends AdminController
{
    public function __construct()
    {
        $this->activeMenuIndex = 6;
    }

    public function sidebarAction($active)
    {
        $em = $this->getDoctrine()->getManager();

        $program = $this->container->get('admin.program')->getCurrent();
        $level = $program->getParamLevel();

        return $this->render(
            'AdminBundle:Parametrages:menu-sidebar-parametrages.html.twig',
            array(
                'level' => $level,
                'active' => $active
            )
        );
    }

    /**
     * @return Response
     */
    public function rootAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        $hasRoot = $this->container->get('app.design_root')->exists($program->getId());
        return $this->render(
            'root.html.twig',
            array(
                'link' => $hasRoot
            )
        );
    }

    /**
     * @return Response
     */
    public function logoAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        $siteDesign = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $siteDesign = $siteDesign[0];
        $logoPath = false;
        $name = false;

        if ($file = $siteDesign->getLogoPath()) {
            if (is_file($file)) {
                $logoPath = $file->getPathname();
            } else {
                $logoPath = $this->container->getParameter('logo_path') . '/' . $program->getId() . '/' . $file;
            }
        } elseif ($siteDesign->getLogoName()) {
            $name = true;
        }
        return $this->render(
            'logo.html.twig',
            array(
                    'link' => $logoPath,
                    'name' => $name
            )
        );
    }

    /**
     * @return Response
     */
    public function logoLoginAction()
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();
        $siteDesign = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $siteDesign = $siteDesign[0];
        $logoPath = false;
        $name = false;

        if ($file = $siteDesign->getLogoPath()) {
            $logoPath = $this->container->getParameter('logo_path') . '/' . $program->getId() . '/' . $file;
        } elseif ($siteDesign->getLogoName()) {
            $name = true;
        }
        return $this->render(
            'logo_login.html.twig',
            array(
                                    'link' => $logoPath,
                                    'name' => $name
            )
        );
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

        
        $programTypeRepo = $em->getRepository('AdminBundle:ProgramType');
        $allProgramType = $programTypeRepo->findAll();

        if ("POST" === $request->getMethod()) {
            $type = (int) $request->get('program_type');
            $isMultiOperation = ((int) $request->get('challenge_mode'))?true:false;

            $changedType = true;
            if ($type === $program->getType()->getId()) {
                $changedType = false;
            } else {
                $newType = $programTypeRepo->find($type);
                $program->setType($newType);
            }

            if ($isMultiOperation != $program->getIsMultiOperation()) {
                $program->setIsMultiOperation($isMultiOperation);
            }

            if (($program->getParamLevel() < 1) || $changedType) { //remettre au level 1
                $program->setParamLevel(1);
            }

            $em->flush();

            return $this->redirectToRoute('admin_parametrages_inscriptions');
        }

        return $this->render(
            'AdminBundle:Parametrages:Programme.html.twig',
            array(
                                    'all_program_type' => $allProgramType,
                                    'program' => $program
            )
        );
    }

    /**
     * @Route("/inscriptions", name="admin_parametrages_inscriptions")
     */

    public function createRegistrationFormAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registrationSiteFormSetting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndTypeWithField($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registrationSiteFormSetting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $registrationSiteFormFieldSettings = $registrationSiteFormSetting->getSiteFormFieldSettings();

        $registrationFormData = $program->getRegistrationFormData();
        if (is_null($registrationFormData)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }
        $currentHeaderImage = $registrationFormData->getHeaderImage();
        $registrationFormData->setHeaderImage("");

        $formFactory = $this->get("form.factory");
        $formStructureForm = $formFactory->createNamed("form_structure", FormStructureType::class);
        $headerDataForm = $formFactory->createNamed(
            "header_data",
            RegistrationFormHeaderDataType::class,
            $registrationFormData
        );
        $introDataForm = $formFactory->createNamed(
            "introduction_data",
            RegistrationFormIntroDataType::class,
            $registrationFormData
        );

        if ("POST" === $request->getMethod()) {
            if ($request->request->has("form_structure")) {
                $formStructureForm->handleRequest($request);
                if ($formStructureForm->isSubmitted() && $formStructureForm->isValid()) {
                    $fieldsManager = $this->container->get("admin.form_field_manager");

                    $fieldOrder = $formStructureForm->getData()["field-order"];

                    $currentFieldList = $formStructureForm->getData()["current-field-list"];
                    $fieldsManager->adjustFieldAndOrder($fieldOrder, $currentFieldList);

                    $newFieldList = $formStructureForm->getData()["new-field-list"];
                    $fieldsManager->addNewFields($newFieldList, $registrationSiteFormSetting);

                    $deleteFieldList = $formStructureForm->getData()["delete-field-action-list"];
                    $fieldsManager->deleteField($deleteFieldList, $registrationSiteFormSetting);

                    $fieldsManager->save();

                    return $this->redirectToRoute("admin_parametrages_inscriptions");
                }
            }

            if ($request->request->has("header_data")) {
                $headerDataForm->handleRequest($request);
                if ($headerDataForm->isSubmitted() && $headerDataForm->isValid()) {
                    $headerImageFile = $registrationFormData->getHeaderImage();
                    if (!is_null($headerImageFile)) {
                        $headerImageFile->move(
                            $this->getParameter("registration_header_image_upload_dir"),
                            $headerImageFile->getClientOriginalName()
                        );
                        $registrationFormData->setHeaderImage($headerImageFile->getClientOriginalName());
                    } else {
                        $registrationFormData->setHeaderImage($currentHeaderImage);
                    }

                    if (!empty($headerDataForm->get('delete_image_command')->getData())
                        && "true" == $headerDataForm->get('delete_image_command')->getData()
                    ) {
                        $filesystem = $this->get('filesystem');
                        $imagePath = $this->getParameter('registration_header_image_upload_dir')
                            . '/'
                            . $registrationFormData->getHeaderImage();
                        if ($filesystem->exists($imagePath)) {
                            $filesystem->remove($imagePath);
                        }
                        $registrationFormData->setHeaderImage(null);
                    }
                    $em->flush();

                    return $this->redirectToRoute("admin_parametrages_inscriptions");
                }
            }

            if ($request->request->has("introduction_data")) {
                $introDataForm->handleRequest($request);
                if ($introDataForm->isSubmitted() && $introDataForm->isValid()) {
                    $registrationFormData->setHeaderImage($currentHeaderImage);
                    $em->flush();

                    return $this->redirectToRoute("admin_parametrages_inscriptions");
                }
            }
        }

        return $this->render(
            "AdminBundle:Parametrages:Inscriptions.html.twig",
            array(
            "site_form_field_settings" => $registrationSiteFormFieldSettings,
            "form_structure_form" => $formStructureForm->createView(),
            "field_type_list" => FieldTypeName::FIELD_NAME,
            "custom_field_allowed" => $registrationSiteFormSetting->getCustomFieldAllowed(),
            "header_data_form" =>  $headerDataForm->createView(),
            "current_header_image" => $currentHeaderImage,
            "intro_data_form" => $introDataForm->createView(),
            )
        );
    }

    /**
     * @Route(
     *     "/inscriptions/header-formulaire/suppression-image",
     *     name="admin_parameters_registration_delete_header_image")
     */
    public function deleteRegistrationFormHeaderImage()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $registrationFormData = $program->getRegistrationFormData();
        if (is_null($registrationFormData)) {
            return new Response('');
        }

        if (!is_null($registrationFormData->getHeaderImage())) {
            $filesystem = $this->get('filesystem');
            $imagePath = $this->getParameter('registration_header_image_upload_dir')
                . '/'
                . $registrationFormData->getHeaderImage();
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
            $registrationFormData->setHeaderImage(null);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route("/inscriptions/creation-formulaire/nouveau-champ", name="admin_new_registration_form_field")
     */
    public function addRegistrationFormFieldAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $siteFormFieldSettingManager = $this->container->get('admin.form_field_manager');

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            //            return $this->redirectToRoute('fos_user_security_logout');
            return new Response('');
        }

        $registrationSiteFormSetting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndTypeWithField($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registrationSiteFormSetting)) {
            return new Response('');
        }

        if ($request->isMethod('GET')) {
            return $this->render(
                "AdminBundle:Parametrages:manip_registration_form_field.html.twig",
                array(
                "type" => FieldType::TEXT,
                "field_type" => new FieldType(),
                )
            );
        }

        if ($request->isMethod('POST')) {
            if ($request->get('validate')
                && $request->get('label')
                && $request->get('field_type')
                && !is_null($request->get('field_type'))
            ) {
                $newField = array(
                    "mandatory" => false,
                    "label" => $request->get('label'),
                    "field_type" => $request->get('field_type'),
                    "special_field_index" => array(SpecialFieldIndex::USER_FIELD),
                );
                if (FieldType::CHOICE_RADIO == $request->get('field_type')) {
                    $yesNoChoicesArray = array(
                        "oui" => "oui",
                        "non" => "non,"
                    );
                    $newField["choices"] = $yesNoChoicesArray;
                }
                $field = $siteFormFieldSettingManager->addNewField(
                    $newField,
                    $registrationSiteFormSetting,
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
        $siteFormFieldSettingManager = $this->container->get('admin.form_field_manager');

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            //            return $this->redirectToRoute('fos_user_security_logout');
            return new Response('');
        }

        $registrationSiteFormSetting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndTypeWithField($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registrationSiteFormSetting)) {
            return new Response('');
        }

        if ($request->isMethod('GET')) {
            if ($request->get('field_id')) {
                $field = $em->getRepository('AdminBundle\Entity\SiteFormFieldSetting')
                    ->findBySiteFormSettingAndId($registrationSiteFormSetting, $request->get('field_id'));
                if (!is_null($field)) {
                    $customChoiceRadioChoices = array();
                    if (FieldType::CHOICE_RADIO == $field->getFieldType()) {
                        $customChoiceRadioChoices["choices"] = array();
                        if (array_key_exists("choices", $field->getAdditionalData())) {
                            $customChoiceRadioChoices = $field->getAdditionalData()["choices"];
                        }
                    }
                    return $this->render(
                        "AdminBundle:Parametrages:manip_registration_form_field.html.twig",
                        array(
                            "type" => $field->getFieldType(),
                            "custom_choice_radio_choices" => $customChoiceRadioChoices,
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
                    ->findBySiteFormSettingAndId($registrationSiteFormSetting, $request->get('field_id'));
                if (!is_null($field)) {
                    $customChoices = null;
                    if ($request->get('options')) {
                        $customChoices = $request->get('options');
                    }
                    $siteFormFieldSettingManager->updateFieldWithCustomChoices(
                        $field,
                        $request->get('field_type'),
                        $request->get('label'),
                        $customChoices
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

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registrationSiteFormSetting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registrationSiteFormSetting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $registrationImportForm = $this->createForm(RegistrationImportType::class);
        $registrationImportForm->handleRequest($request);
        $errorList = array();
        if ($registrationImportForm->isSubmitted() && $registrationImportForm->isValid()) {
            $importFile = $registrationImportForm->getData()["registration_data"];
            $registrationHandler = $this->get("AdminBundle\Service\ImportExport\RegistrationHandler");
            $registrationHandler->setSiteFormSetting($registrationSiteFormSetting);
            $registrationHandler->import($importFile);

            if (!empty($registrationHandler->getErrorList())) {
                $errorList = $registrationHandler->getErrorList();
            } else {
                $this->addFlash('success_message', 'Import de données effectué avec succès');
                return $this->redirectToRoute("admin_parametrages_inscriptions_imports");
            }
        }

        return $this->render(
            "AdminBundle:Parametrages:Imports.html.twig",
            array(
            "registration_form" => $registrationImportForm->createView(),
            "error_list" => $errorList,
            )
        );
    }

    /**
     * @Route("/inscriptions/imports/telecharger-modele", name="admin_parametrages_inscriptiions_imports_telecharger_modele")
     */
    public function downloadRegistrationModelAction()
    {
        $em = $this->getDoctrine()->getManager();

        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registrationSiteFormSetting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registrationSiteFormSetting)) {
            return $this->redirectToRoute("fos_user_security_logout");
        }

        $model = $this->get('AdminBundle\Service\ImportExport\RegistrationModel');
        $model->setSiteFormSetting($registrationSiteFormSetting);
        $response = $model->createResponse();

        return $response;
    }

    /**
     * @Route("/inscriptions/imports/etre-contacte",  name="admin_parameters_registration_import_be_contacted")
     */
    public function beContactedAction(Request $request)
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')
            || !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')
        ) {
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

        $registrationSiteFormSetting = $em->getRepository("AdminBundle\Entity\SiteFormSetting")
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registrationSiteFormSetting)) {
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
            $siteFormType = SiteFormType::PRODUCT_DECLARATION_TYPE;
            $defaultLines = 5;
        } else {
            $siteFormType = SiteFormType::LEAD_DECLARATION_TYPE;
        }

        $fieldsManager = $this->container->get('admin.form_field_manager');
        $allLevel = $fieldsManager->getMaxLevel($program, $siteFormType);
        $maxLevel = (!empty($allLevel))?(int) $allLevel[0]['level']:0;
        $newLevel = $maxLevel+1;

        $fieldsManager->rechargeDefaultFieldFor($program, $siteFormType, $newLevel);

        $siteFormSetting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeAndLevelWithField($program, $siteFormType, $newLevel);

        // dump($site_form_setting); die;

        return $this->render(
            'AdminBundle:Parametrages:New_declaration.html.twig',
            array(
            'site_form_setting' => $siteFormSetting,
            'site_form_field_settings' => $siteFormSetting->getSiteFormFieldSettings(),
            'max_line' => $siteFormSetting->getCustomFieldAllowed() + $defaultLines
            )
        );
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
            $siteFormType = SiteFormType::PRODUCT_DECLARATION_TYPE;
        } else {
            $siteFormType = SiteFormType::LEAD_DECLARATION_TYPE;
        }

        $fieldsManager = $this->container->get('admin.form_field_manager');
        $fieldsManager->removeFieldsForLevel($program, $siteFormType, $level);

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
        $fieldId = ($request->get('field_id'))?$request->get('field_id'):"";

        $em = $this->getDoctrine()->getManager();
        $fieldsManager = $this->container->get('admin.form_field_manager');

        if (!empty($fieldId)) {//update
            $field = $em->getRepository('AdminBundle:SiteFormFieldSetting')->find($fieldId);

            if ($request->get('update')) {
                $field = $fieldsManager->updateField($field, $type, $label);
                return $this->render(
                    'AdminBundle:Parametrages:Partial_new.html.twig',
                    array(
                    'field' => $field,
                    'label' => $label,
                    'personalize' => true
                    )
                );
            } else {
                $row = $field->getInRow();
                $type = ($row)?"period":$field->getFieldType();
                $type = ($type == 'text')?"alphanum":$type;
                $label = $field->getLabel();
            }
        }

        if ($request->get('validate')) {//validate
            $newField = [
                            'level' => $level,
                            'label' => $label,
                            "mandatory" => false,
                            "field_type" => $type
                            ];
            if ($type == "choice-radio") {
                $newField["choices"] = ["oui"=>"oui","non"=>"non"];
            }

            $program = $this->container->get('admin.program')->getCurrent();
            if (empty($program)) {//redirection si program n'existe pas
                return new Response('');
            }

            if ("Challenge" === $program->getType()->getType()) {
                $siteFormType = SiteFormType::PRODUCT_DECLARATION_TYPE;
            } else {
                $siteFormType = SiteFormType::LEAD_DECLARATION_TYPE;
            }

            $siteFormSetting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeWithFieldWithLevel($program, $siteFormType);
            $field = $fieldsManager->addNewField($newField, $siteFormSetting);

            // dump($field); die;
            return $this->render(
                'AdminBundle:Parametrages:Partial_new.html.twig',
                array(
                'field' => $field,
                'label' => $label,
                'personalize' => true
                )
            );
        }

        return $this->render(
            'AdminBundle:Parametrages:New_field_declaration.html.twig',
            array(
            'level' => $level,
            'type' => $type,
            'label' => $label,
            'field_id' => $fieldId
            )
        );
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
            $siteFormType = SiteFormType::PRODUCT_DECLARATION_TYPE;
            $defaultLines = 5;
        } else {
            $siteFormType = SiteFormType::LEAD_DECLARATION_TYPE;
        }

        $fieldsManager = $this->container->get('admin.form_field_manager');
        $allLevel = $fieldsManager->getMaxLevel($program, $siteFormType);

        $maxLevel = (!empty($allLevel))?(int) $allLevel[0]['level']:1;
        if (empty($allLevel)) {
            $fieldsManager->rechargeDefaultFieldFor($program, $siteFormType, $maxLevel);
        }

        $siteFormSetting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeWithFieldWithLevel($program, $siteFormType);
        $arrangedFields = $fieldsManager->getArrangedFields($siteFormSetting);

        $formStructureForm = $this->createForm(FormStructureDeclarationType::class);
        $formStructureForm->handleRequest($request);

        if ($formStructureForm->isSubmitted() && $formStructureForm->isValid()) {
            //dump($request); die;
            //validation
            $validationRequired =  ((int) $formStructureForm->getData()['validation-required'])?true:false;
            $siteFormSetting->setValidation($validationRequired);

            //pieces justificatif
            $piecesRequired = ((int) $formStructureForm->getData()['pieces-required'])?true:false;
            $siteFormSetting->setHasPieces($piecesRequired);

            //texte head
            $textHeadRequired =  ((int) $formStructureForm->getData()['text-head-required'])?true:false;
            $siteFormSetting->setHasHeadText($textHeadRequired);

            if ($textHeadRequired) {
                $textHead =  $formStructureForm->getData()['text-head'];
                $siteFormSetting->setHeadText($textHead);
            }

            //adjust current field
            $fieldOrder = $formStructureForm->getData()['field-order'];
            $currentFieldList = $formStructureForm->getData()['current-field-list'];
            $fieldsManager->adjustFieldAndOrder($fieldOrder, $currentFieldList);

            //add new field
            // $newFieldList = $formStructureForm->getData()['new-field-list'];
            // if (!empty($newFieldList)) {
            //     $fieldsManager->addNewFields($newFieldList, $site_form_setting, true);
            // }

            //delete field
            $deleteFieldList = $formStructureForm->getData()['delete-field-action-list'];
            $fieldsManager->deleteField($deleteFieldList, $siteFormSetting, true);
            //save modification
            $fieldsManager->save();

            $nextForm = $formStructureForm->getData()['next'];//next product
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

        return $this->render(
            'AdminBundle:Parametrages:Declarations.html.twig',
            array(
            'site_form_setting' => $siteFormSetting,
            'form_structure_form' => $formStructureForm->createView(),
            'fields' => $arrangedFields,
            'field_type_list' => FieldTypeName::FIELD_NAME,
            'max_line' => $siteFormSetting->getCustomFieldAllowed()+ $defaultLines
            )
        );
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

        $resultSetting = $em->getRepository('AdminBundle:ResultSetting')->findByProgram($program);
        $resultSetting = $resultSetting[0];
        $settingForm = $this->createForm(ResultSettingType::class, $resultSetting);
        $uploadForm = $this->createForm(ResultSettingUploadType::class);

        if ($request->get('result_setting')) {//download model
            $settingForm->handleRequest($request);
            if ($settingForm->isSubmitted() && $settingForm->isValid()) {
                $em->flush();
                $monthly = $resultSetting->getMonthly();
                $byProduct = $resultSetting->getByProduct();
                $byRank = $resultSetting->getByRank();
                $resultSettingModal = $this->get('AdminBundle\Service\ImportExport\ResultSettingModel');
                $resultSettingModal->setProgram($program);
                $response = $resultSettingModal
                    ->createResponse($monthly, $byProduct, $byRank);
                return $response;
            }
        }

        $errorList = array();
        if ($request->get('result_setting_upload')) {//upload fichier
            $uploadForm->handleRequest($request);
            if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
                $importedFile = $uploadForm->getData()["uploaded_file"];
                $resultSettingHandler = $this->get('AdminBundle\Service\ImportExport\ResultSettingHandler');
                $resultSettingHandler->setProgram($program);
                $resultSettingHandler->setResultSetting($resultSetting);
                $resultSettingHandler->import($importedFile);

                if (!empty($resultSettingHandler->getErrorList())) {
                    $errorList = $resultSettingHandler->getErrorList();
                } else {
                    $this->addFlash('success_message', 'Import de données effectué avec succès');
                    return $this->redirectToRoute("admin_resultats_declaration_import");
                }
            }
        }
        // $upload_form->refresh();

        return $this->render(
            'AdminBundle:Parametrages:Import_declaration.html.twig',
            array(
            'form_upload' => $uploadForm->createView(),
            'setting_form' => $settingForm->createView(),
            'error_list' => $errorList
            )
        );
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

        $siteDesign = $em->getRepository('AdminBundle:SiteDesignSetting')->findByProgram($program);
        $siteDesign = $siteDesign[0];

        if ($logo = $siteDesign->getLogoPath()) {
            $siteDesign->setLogoPath(
                $this->container->get('admin.logo')->getFile($logo, $program->getId())
            );
        }

        if ($background = $siteDesign->getBodyBackground()) {
            $siteDesign->setBodyBackground(
                $this->container->get('admin.body_background')->getFile($background, $program->getId())
            );
        }

        $siteDesignFormLogo = $this->createForm(SiteDesignSettingType::class, $siteDesign)
            ->remove('police')
            ->remove('colors')
            ->remove('body_background');//form logo

        $siteDesignFormColors = $this->createForm(SiteDesignSettingType::class, $siteDesign)
            ->remove('police')
            ->remove('logo_name')
            ->remove('logo_path');//form couleurs

        $siteDesignFormPolice = $this->createForm(SiteDesignSettingType::class, $siteDesign)
            ->remove('colors')
            ->remove('logo_name')
            ->remove('logo_path')
            ->remove('body_background');//form police

        if ($request->get('site_design_setting')) {
            if (array_key_exists('logo_name', $request->get('site_design_setting'))) {//logo
                $siteDesignFormLogo->handleRequest($request);
                if ($background) {
                    $siteDesign->setBodyBackground($background);
                }

                if ($siteDesignFormLogo->isSubmitted() && $siteDesignFormLogo->isValid()) {
                    if (array_key_exists('logo_path', $request->files->get('site_design_setting'))
                        && !is_null($siteDesign->getLogoPath())
                    ) {
                        $logo = $this->container->get('admin.logo')->upload(
                            $siteDesign->getLogoPath(),
                            $program->getId()
                        );
                        $siteDesign->setLogoPath($logo);
                    } elseif ($logo = $request->get('logo')) {
                        $siteDesign->setLogoPath($logo);
                    }

                    $this->container->get('app.design_root')->resetRoot(
                        $program->getId(),
                        $siteDesign
                    );
                    $em->flush();
                    $this->redirectToRoute('admin_param_design');
                }
            }

            // die;
            if (array_key_exists('colors', $request->get('site_design_setting'))) {//couleur
                $siteDesignFormColors->handleRequest($request);
                if ($logo) {
                    $siteDesign->setLogoPath($logo);
                }

                if ($siteDesignFormColors->isSubmitted() && $siteDesignFormColors->isValid()) {
                    if (array_key_exists('body_background', $request->files->get('site_design_setting'))
                        && !is_null($siteDesign->getBodyBackground())
                    ) {
                        $background = $this->container->get('admin.body_background')->upload(
                            $siteDesign->getBodyBackground(),
                            $program->getId()
                        );
                        $siteDesign->setBodyBackground($background);
                    } elseif ($background = $request->get('background')) {
                        $siteDesign->setBodyBackground($background);
                    }

                    $this->container->get('app.design_root')->resetRoot(
                        $program->getId(),
                        $siteDesign
                    );
                    $em->flush();
                    $this->redirectToRoute('admin_param_design');
                }
            }

            if (array_key_exists('police', $request->get('site_design_setting'))) {//police
                $siteDesignFormPolice->handleRequest($request);
                if ($logo) {
                    $siteDesign->setLogoPath($logo);
                }
                if ($background) {
                    $siteDesign->setBodyBackground($background);
                }

                if ($siteDesignFormPolice->isSubmitted() && $siteDesignFormPolice->isValid()) {
                    $this->container->get('app.design_root')->resetRoot(
                        $program->getId(),
                        $siteDesign
                    );
                    $em->flush();
                    $this->redirectToRoute('admin_param_design');
                }
            }
        }

        return $this->render(
            'AdminBundle:Parametrages:Design.html.twig',
            array(
            'site_design_form_logo' => $siteDesignFormLogo->createView(),
            'site_design_form_colors' => $siteDesignFormColors->createView(),
            'site_design_form_police' => $siteDesignFormPolice->createView(),
            'logo' => $logo,
            'background' => $background
            )
        );
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

        $loginPortalData = $program->getLoginPortalData();
        if (is_null($loginPortalData)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        $originalSlides = new ArrayCollection();
        foreach ($loginPortalData->getLoginPortalSlides() as $slide) {
            $originalSlides->add($slide);
        }

        $originalSlidesImage = array();
        foreach ($originalSlides as $slide) {
            $originalSlidesImage[$slide->getId()] = $slide->getImage();
        }

        $formFactory = $this->get('form.factory');
        $loginPortalDataForm = $formFactory->createNamed(
            "login_portal_data_form",
            LoginPortalDataType::class,
            $loginPortalData
        );
        $loginPortalDataForm->handleRequest($request);

        if ($loginPortalDataForm->isSubmitted() && $loginPortalDataForm->isValid()) {
            // checking for "delete image" commands
            $deletedImageSlideIdList = array();
            foreach ($loginPortalDataForm->get('login_portal_slides') as $loginPortalSlide) {
                $deleteImageCommand = $loginPortalSlide->get('delete_image_command')->getData();
                if (!empty($deleteImageCommand) && 'true' == $deleteImageCommand) {
                    $slide = $loginPortalSlide->getNormData();
                    $slide->setImage($originalSlidesImage[$slide->getId()]);
                    $numberOtherSlideUsingImage = $em->getRepository('AdminBundle\Entity\LoginPortalSlide')
                        ->retrieveNumberOfOtherSlideUsingImage($loginPortalData, $slide);
                    if (0 == $numberOtherSlideUsingImage) {
                        $filesystem = $this->get('filesystem');
                        $imagePath = $this->getParameter('content_login_portal_slide_image_upload_dir')
                            . '/'
                            . $slide->getImage();
                        if ($filesystem->exists($imagePath)) {
                            $filesystem->remove($imagePath);
                        }
                    }
                    $slide->setImage(null);
                    array_push($deletedImageSlideIdList, $slide->getId());
                }
            }

            // editing existant slide
            foreach ($loginPortalData->getLoginPortalSlides() as $slide) {
                if (!is_null($slide->getId())) {
                    // setting image for existent slide
                    if (is_null($slide->getImage())) {
                        if (!in_array($slide->getId(), $deletedImageSlideIdList)) {
                            // set previous image
                            if (array_key_exists($slide->getId(), $originalSlidesImage)) {
                                $slide->setImage($originalSlidesImage[$slide->getId()]);
                            }
                        }
                    } else {
                        // upload new image
                        $image = $slide->getImage();
                        $image->move(
                            $this->getParameter('content_login_portal_slide_image_upload_dir'),
                            $image->getClientOriginalName()
                        );
                        $slide->setImage($image->getClientOriginalName());
                    }
                }
            }

            // deleting slides
            foreach ($originalSlides as $originalSlide) {
                if (false === $loginPortalData->getLoginPortalSlides()->contains($originalSlide)) {
                    $originalSlide->setLoginPortalData(null);
                    $loginPortalData->removeLoginPortalSlide($originalSlide);
                    $em->remove($originalSlide);
                }
            }

            // adding new slide
            foreach ($loginPortalData->getLoginPortalSlides() as $slide) {
                if (is_null($slide->getId())) {
                    $slide->setLoginPortalData($loginPortalData);
                    if (!is_null($slide->getImage())) {
                        $image = $slide->getImage();
                        $image->move(
                            $this->getParameter('content_login_portal_slide_image_upload_dir'),
                            $image->getClientOriginalName()
                        );
                        $slide->setImage($image->getClientOriginalName());
                    }
                    $em->persist($slide);
                }
            }

            $em->flush();

            return $this->redirectToRoute("admin_content_configure_login_portal");
        }

        return $this->render(
            'AdminBundle:Parametrages:content_configure_login_portal.html.twig',
            array(
            'login_portal_data_form' => $loginPortalDataForm->createView(),
            'original_slides_image' => $originalSlidesImage,
            )
        );
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

        $loginPortalData = $program->getLoginPortalData();
        if (is_null($loginPortalData)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $maxSlideOrder = 0;
        if (!$loginPortalData->getLoginPortalSlides()->isEmpty()) {
            $maxSlideOrder = $em->getRepository('AdminBundle\Entity\LoginPortalData')
                ->retrieveMaxSlideOrderByLoginPortalData($loginPortalData);
        }

        $newSlide = new LoginPortalSlide();
        $newSlide->setSlideOrder($maxSlideOrder + 1);
        $newSlide->setLoginPortalData($loginPortalData);
        $loginPortalData->addLoginPortalSlide($newSlide);
        $em->persist($newSlide);
        $em->flush();

        return new Response($newSlide->getId());
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

        $loginPortalData = $program->getLoginPortalData();
        if (is_null($loginPortalData)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $toDelSlide = $em->getRepository('AdminBundle\Entity\LoginPortalSlide')
            ->findOneBy(
                array(
                'login_portal_data' => $loginPortalData,
                'id' => $id,
                )
            );
        if (is_null($toDelSlide)) {
            return new Response('');
        }

        $loginPortalData->removeLoginPortalSlide($toDelSlide);
        $toDelSlide->setLoginPortalData(null);
        $em->remove($toDelSlide);
        $em->flush();

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route(
     *     "/contenus/portail-identification/suppression-slide-image/{slideId}",
     *     name="admin_content_configure_login_portal_delete_slide_image"),
     *     requirements={"slideId": "\d+"}
     */
    public function deleteLoginPortalSlideImageAction($slideId)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $loginPortalData = $program->getLoginPortalData();
        if (is_null($loginPortalData)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $slide = $em->getRepository('AdminBundle\Entity\LoginPortalSlide')
            ->findOneBy(
                array(
                'login_portal_data' => $loginPortalData,
                'id' => $slideId,
                )
            );
        if (is_null($slide)) {
            return new Response('');
        }

        if (!is_null($slide->getImage())) {
            $numberOtherSlideUsingImage = $em->getRepository('AdminBundle\Entity\LoginPortalSlide')
                ->retrieveNumberOfOtherSlideUsingImage($loginPortalData, $slide);
            if (0 == $numberOtherSlideUsingImage) {
                $filesystem = $this->get('filesystem');
                $imagePath = $this->getParameter('content_login_portal_slide_image_upload_dir')
                    . '/'
                    . $slide->getImage();
                if ($filesystem->exists($imagePath)) {
                    $filesystem->remove($imagePath);
                }
            }
            $slide->setImage(null);
            $em->flush();
        }

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

        $homePageData = $program->getHomePageData();
        if (is_null($homePageData)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        /*$editorial = $home_page_data->getEditorial();
        if (is_null($editorial)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }*/
        $em = $this->getDoctrine()->getManager();
        $parameterEdito = $em->getRepository('AdminBundle\Entity\HomePagePost')
            ->findOneBy(
                array(
                'program' => $program,
                'post_type' => PostType::PARAMETER_EDITO,
                )
            );
        if (is_null($parameterEdito)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $slideshowManager = $this->container->get('admin.slideshow');
        $originalSlides = $slideshowManager->getOriginalSlides($homePageData);
        $originalSlidesImage = $slideshowManager->getOriginalSlidesImage($originalSlides);

        $formFactory = $this->get('form.factory');
        $homePageSlideDataForm = $formFactory->createNamed(
            'home_page_slide_data_form',
            HomePageSlideDataType::class,
            $homePageData
        );
        $homePageEditorialDataForm = $formFactory->createNamed(
            'home_page_editorial_data_form',
            HomePageEditorialType::class,
            $parameterEdito
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
                    $homePageData = $slideshowManager->editHomePageSlides(
                        $homePageData,
                        $deletedImageSlideIdList,
                        $originalSlidesImage
                    );
                    // deleting slides
                    $homePageData = $slideshowManager->deleteHomePageSlides($homePageData, $originalSlides);
                    // adding new slide
                    $slideshowManager->addNewHomePageSlides($homePageData);

                    $em->flush();

                    return $this->redirectToRoute('admin_content_configure_home_page');
                }
            }

            if ($request->request->has('home_page_editorial_data_form')) {
                $homePageEditorialDataForm->handleRequest($request);
                if ($homePageEditorialDataForm->isSubmitted() && $homePageEditorialDataForm->isValid()) {
                    /*$editorial->setLastEdit(new \DateTime(
                        'now',
                        new \DateTimeZone($this->getParameter('app_time_zone'))
                    ));*/
                    $em->flush();
                    return $this->redirectToRoute('admin_content_configure_home_page');
                }
            }
        }

        return $this->render(
            'AdminBundle:Parametrages:content_configure_home_page.html.twig',
            array(
            'home_page_slide_data_form' => $homePageSlideDataForm->createView(),
            'home_page_editorial_data_form' => $homePageEditorialDataForm->createView(),
            'original_slides_image' => $originalSlidesImage,
            'slide_type' => new SlideType(),
            )
        );
    }

    /**
     * @Route("/contenus/page-accueil/ajout-slide/{slideType}", name="admin_content_configure_home_page_add_slide")
     */
    public function addHomePageSlideAction($slideType)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $homePageData = $program->getHomePageData();
        if (is_null($homePageData)) {
            return new Response('');
        }

        $validSlideType = array(
            SlideType::IMAGE,
            SlideType::VIDEO,
        );
        if (!in_array($slideType, $validSlideType)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $maxSlideOrder = 0;
        if (!$homePageData->getHomePageSlides()->isEmpty()) {
            $maxSlideOrder = $em->getRepository('AdminBundle\Entity\HomePageData')
                ->retrieveMaxSlideOrderByHomePageData($homePageData);
        }
        $newSlide = new HomePageSlide();
        $newSlide->setSlideOrder($maxSlideOrder + 1)
            ->setHomePageData($homePageData)
            ->setSlideType($slideType);
        $homePageData->addHomePageSlide($newSlide);

        $em->persist($newSlide);
        $em->flush();

        return new Response($newSlide->getId());
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

        $homePageData = $program->getHomePageData();
        if (is_null($homePageData)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $toDelSlide = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findOneBy(
                array(
                'home_page_data' => $homePageData,
                'id' => $id
                )
            );
        if (is_null($toDelSlide)) {
            return new Response('');
        }

        $homePageData->removeHomePageSlide($toDelSlide);
        $toDelSlide->setHomePageData(null);
        $em->remove($toDelSlide);
        $em->flush();

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route(
     *     "/contenus/page-accueil/suppression-slide-image/{slideId}",
     *     name="admin_content_configure_home_page_delete_slide_image"),
     *     requirements={"slideId": "\d+"}
     */
    public function deleteHomePageSlideImageAction($slideId)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $homePageData = $program->getHomePageData();
        if (is_null($homePageData)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();
        $slide = $em->getRepository('AdminBundle\Entity\HomePageSlide')
            ->findOneBy(
                array(
                'home_page_data' => $homePageData,
                'id' => $slideId,
                )
            );
        if (is_null($slide)) {
            return new Response('');
        }

        if (!is_null($slide->getImage())) {
            $numberOtherSlideUsingImage = $em->getRepository('AdminBundle\Entity\HomePageSlide')
                ->retrieveNumberOfOtherSlideUsingImage($homePageData, $slide);
            if (0 == $numberOtherSlideUsingImage) {
                $filesystem = $this->get('filesystem');
                $imagePath = $this->getParameter('content_home_page_slide_image_upload_dir')
                    . '/'
                    . $slide->getImage();
                if ($filesystem->exists($imagePath)) {
                    $filesystem->remove($imagePath);
                }
            }
            $slide->setImage(null);
            $em->flush();
        }

        return new Response('<html><body>OK</body></html>');
    }

    /**
     * @Route("/contenus/tableaux-reseaux",name="admin_table_network")
     */
    public function tableauReseauAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $siteTableNetwork = $em->getRepository("AdminBundle:SiteTableNetworkSetting")->findBy(
            array('program' => $program)
        );

        $siteTableNetworkForm = $this->createForm(SiteTableNetworkSettingType::class, $siteTableNetwork[0]);
        $siteTableNetworkForm->handleRequest($request);
        // dump($site_table_network_form); die;
        if ($siteTableNetworkForm->isSubmitted() && $siteTableNetworkForm->isValid()) {
            // dump($request); die;
            $em->flush();
        }

        return $this->render(
            'AdminBundle:Parametrages:table_reseau.html.twig',
            array(
            "site_table_network" => $siteTableNetworkForm->createView(),
            )
        );
    }

    /**
     * @Route("/points/rang", name="admin_point_rang")
     */
    public function rankPointAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $this->container->get('admin.role_rank')->setAllRoleRank($program);
        $rolesForm = $this->createForm(ProgramRankType::class, $program);
        $rolesForm->handleRequest($request);

        if ($rolesForm->isSubmitted() && $rolesForm->isValid()) {
            $program = $this->container->get('admin.role_rank')->saveAllRoleRank($program);
            $this->redirectToRoute('admin_point_rang');
        }

        return $this->render(
            'AdminBundle:Parametrages:rank_point.html.twig',
            array(
                'roles_form' => $rolesForm->createView()
            )
        );
    }

    /**
     * @Route("/points/periode", name="admin_point_periode")
     */
    public function periodPointAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $this->container->get('admin.period_point')->setAllPeriodPoint($program);
        $periodPointForm = $this->createForm(ProgramPeriodPointType::class, $program);
        $periodPointForm->handleRequest($request);

        if ($periodPointForm->isSubmitted() && $periodPointForm->isValid()) {
            $em->flush();
            $this->redirectToRoute('admin_point_periode');
        }

        return $this->render(
            'AdminBundle:Parametrages:period_point.html.twig',
            array(
            "period_point" => $periodPointForm->createView()
            )
        );
    }

    /**
     * @Route("/points/periode/new", name="admin_new_point_period")
     * @Method("POST")
     */
    public function newPeriodPointAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $this->container->get('admin.period_point')->newPeriodPointProduct($program);
        $periodPointForm = $this->createForm(ProgramPeriodPointType::class, $program);

        return $this->render(
            'AdminBundle:Parametrages:new_period_point.html.twig',
            array(
            "period_point" => $periodPointForm->createView()
            )
        );
    }

    /**
     * @Route("/points/periode/delete/{product_group}", name="admin_delete_point_period")
     * @Method("POST")
     */
    public function deletePeriodPointAction($product_group)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $this->container->get('admin.period_point')->deletePeriodPointProduct($program, $product_group);
        
        return new Response('done');
    }

    /**
     * @Route("/points/performances", name="admin_point_performance")
     */
    public function perfomancePointAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $program = $this->container->get('admin.program')->getCurrent();

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $this->container->get('admin.point_attribution')->setPerformance1($program);
        $performanceForm1 = $this->createForm(
            ProgramPointAttributionType::class,
            $program,
            array(
                'entry_type_class' => PointAttributionSettingPerformance1Type::class
            )
        );
        if ($request->get("ca-points")) {
            $performanceForm1->handleRequest($request);
            if ($performanceForm1->isSubmitted() && $performanceForm1->isValid()) {
                $em->flush();
            }
        }

        $program = $this->container->get('admin.point_attribution')->setPerformance2($program);
        $performanceForm2 = $this->createForm(
            ProgramPointAttributionType::class,
            $program,
            array(
                'entry_type_class' => PointAttributionSettingPerformance2Type::class
            )
        );
        if ($request->get("classment-points")) {
            $performanceForm2->handleRequest($request);
            if ($performanceForm2->isSubmitted() && $performanceForm2->isValid()) {
                $em->flush();
            }
        }

        return $this->render(
            'AdminBundle:Parametrages:performance_point.html.twig',
            array(
            'performance_form1' => $performanceForm1->createView(),
            'performance_form2' => $performanceForm2->createView(),
            )
        );
    }

    /**
     * @Route("/points/produits", name="admin_point_product")
     */
    public function productPointAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $productPointAttribManager = $this->get('AdminBundle\Manager\ProductPointAttributionManager');
        $productPointSettingData = $productPointAttribManager->createProductPointSettingData($program);

        $formFactory = $this->get("form.factory");
        $productPointAttributionForm = $formFactory->createNamed(
            'product_point_attribution_form',
            ProductPointType::class,
            $productPointSettingData,
            array('validation_groups' => array('product_point'))
        );

        $originalProductSettingDatas = new ArrayCollection();
        foreach ($productPointSettingData->getProductPointSettingList() as $settingData) {
            $originalProductSettingDatas->add($settingData);
        }

        $productPointAttributionForm->handleRequest($request);

        $productPointErrors = array();
        if ($productPointAttributionForm->isSubmitted() && $productPointAttributionForm->isValid()) {
            $productPointOptionChecker = $this->get('AdminBundle\Service\ProductPoint\ProductPointOptionChecker');
            $errors = $productPointOptionChecker->check($productPointSettingData);
            if (empty($errors)) {
                $productPointAttribManager->saveProductPointSettingData(
                    $productPointSettingData,
                    $program
                );

                $productPointAttribManager->deleteUselessProductPointSettingData(
                    $productPointSettingData,
                    $originalProductSettingDatas,
                    $program
                );

                $productPointAttribManager->flush();

                return $this->redirectToRoute('admin_point_product');
            } else {
                $productPointErrors = $errors;
            }
        }

        return $this->render(
            'AdminBundle:Parametrages:product_point.html.twig',
            array(
            'product_point_attribution_form' => $productPointAttributionForm->createView(),
            'product_point_errors' => $productPointErrors,
            )
        );
    }

    /**
     * @Route("/points/produits/ajout-produit", name="admin_point_product_add_product")
     */
    public function addProductPointAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }
        $productPointAttribManager = $this->get('AdminBundle\Manager\ProductPointAttributionManager');
        $createdProductGroup = $productPointAttribManager->newProductGroupPointAttribution($program);
        if (-1 == $createdProductGroup) {
            return new Response('');
        }

        return new Response('<html><body>' . $createdProductGroup . '</body></html>');
    }

    /**
     * @Route(
     *      "/points/produits/suppression-produit/{productGroup}",
     *      name="admin_point_product_delete_product"),
     *      requirements={"productGroup": "\d+"}
     */
    public function deleteProductPoinAction($productGroup)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $productPointAttribManager = $this->get('AdminBundle\Manager\ProductPointAttributionManager');
        $productPointAttribManager->deleteProductGroupPointAttribution($productGroup, $program);
        $productPointAttribManager->redefineProductGroup($productGroup, $program);

        return new Response('<html><body>OK</body></html>');
    }

/**
     * @Route(
     *     "/contenus/pages-standard/affiche-contenu-page",
     *     name="admin_pages_standard_affiche")
     */
    public function affichePagesStandardAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $datas = array();
            $datas['page'] = $request->get('id_page');
            $datas['new_page'] = $request->get('new_page');
            $response = $this->forward(
                'AdminBundle:PartialPage:afficheContenuPagesStandard',
                array('datas' => $datas)
            );
            return new Response($response->getContent());
        } else {
            return new Response('');
        }
    }

    /**
     * @Route("/contenus/pages-standard/supprimer-img",name="admin_pages_standard_supprimer_img")
     */
    public function supprimerImgPageStandardAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $datas = array();
            $datas['page'] = $request->get('id_page');
            $sitePagesStandardSetting = $em->getRepository("AdminBundle:SitePagesStandardSetting")->find($datas['page']);
            $sitePagesStandardSetting->setPath(null);
            $em->flush();
            return new Response('ok');
        } else {
            return new Response('');
        }
    }

    /**
     * @Route("/contenus/pages-standard/add-img-editor",name="admin_pages_standard_add_img_editor")
     */
    public function LoadPopUpInsertImageCkeditorAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $response = $this->forward('AdminBundle:PartialPage:affichePopUpImgEditor', array('datas' => array(), 'programm' => $program));
            return new Response($response->getContent());
        } else {
            return new Response('');
        }
    }

    /**
     * @Route("/contenus/pages-standard/delete-page-standard",name="admin_pages_standard_delete_page")
     */
    public function DeletePageStandardAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $idpage = $request->get('idpage');
            $sitePagesStandardSetting = $em->getRepository("AdminBundle:SitePagesStandardSetting")->find($idpage);
            if (!is_null($sitePagesStandardSetting)) {
                $em->remove($sitePagesStandardSetting);
                $em->flush();
            }
            return new Response('ok');
        } else {
            return new Response('');
        }
    }

    /**
     * @Route("/contenus/pages-standard/add-img-editor-upload",name="admin_pages_standard_add_img_editor_upload")
     */
    public function UploadImageCkeditorAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $sitePagesStandardSetting = new SitePagesStandardSetting();
            $Img = $request->files->get('images-ckeditor');
            $sitePagesStandardSetting->setImgPage($Img);
            $sitePagesStandardSetting->upload($program);
            $sitePagesStandardSetting->getPath();
        }

        return new Response($program->getId());
    }

    /**
     * @Route("/contenus/pages-standard/list-img-editor",name="admin_pages_standard_list_img_editor")
     */
    public function ListImageCkeditorAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response('');
        }

        $em = $this->getDoctrine()->getManager();

        if ($request->isMethod('POST')) {
            $sitePagesStandardSetting = new SitePagesStandardSetting();
            $RootDir = $sitePagesStandardSetting->getUploadRootDir();
            $RootProgramm = $RootDir . '/' . $program->getId();
            //On lit tous les fichiers images
            $finder = new Finder();

            $files = $finder->files()->in($RootProgramm)->sortByChangedTime()->getIterator();

            $ListeFile = array();
            foreach ($files as $file) {
                $ListeFile[] = array(
                'url' => '/web/content/pages_standards/' . $program->getId() . '/' . $file->getRelativePathname(),
                'nom' => $file->getRelativePathname()
                );
            }

            $response = $this->forward('AdminBundle:PartialPage:afficheListImgEditor', array('datas' => $ListeFile));
            return new Response($response->getContent());
        }

        return new Response('');
    }

    /**
     * Gestion des pages standards
     *
     * @Route("/contenus/pages-standard",name="admin_pages_standard")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function pagesStandardAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();

        //redirection si program n'existe pas
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();

        //Validation
        $Onglets = "";
        if ($request->isMethod('POST')) {
            $NomPages = $request->get('nom_page');
            $TitrePages = $request->get('titre_page');
            $MenuPages = $request->get('menu_page');
            $ImgPages = $request->files->get('img_page');
            $ContenuPages = $request->get('contenu_page');
            $statusPages = $request->get('status_page');
            $Id = $request->get('id_page');
            $Onglets = $request->get('onglet-selectionner-page');

            $publier = $request->get('publier');
            $ordre = $request->get('ordre');
            $obligatoire = $request->get('obligatoire');
            $label = $request->get('label');
            $typeChamp = $request->get('type_champ');

            for ($i=0; $i < count($NomPages); $i++) {
                $sitePagesStandardSetting = $em->getRepository("AdminBundle:SitePagesStandardSetting")->find($Id[$i]);
                if (is_null($sitePagesStandardSetting)) {
                    $sitePagesStandardSetting = new SitePagesStandardSetting();
                }

                $sitePagesStandardSetting->setNomPage($NomPages[$i]);
                $sitePagesStandardSetting->setTitrePage($TitrePages[$i]);
                $sitePagesStandardSetting->setMenuPage($MenuPages[$i]);
                if (isset($ImgPages[$i])) {
                    $sitePagesStandardSetting->setImgPage($ImgPages[$i]);
                }

                $sitePagesStandardSetting->setContenuPage($ContenuPages[$i]);

                if (false == $statusPages[$i] || "0" == $statusPages[$i]) {
                    $sitePagesStandardSetting->setStatusPage(false);
                } else {
                    $sitePagesStandardSetting->setStatusPage(true);
                }

                $sitePagesStandardSetting->setProgram($program);

                $Options['options'] = array();
                if ($NomPages[$i] == 'contact') {
                    $cpt = 0;
                    foreach ($label as $LibelleChamp) {
                        $Options['options'][] = array(
                        'type' => $typeChamp[$cpt],
                        'publier' => (isset($publier[$LibelleChamp]) && !empty($publier[$LibelleChamp])) ? 1 : 0,
                        'obligatoire' => (isset($obligatoire[$LibelleChamp]) && !empty($obligatoire[$LibelleChamp])) ? 1 : 0,
                        'label' => $LibelleChamp,
                        'ordre' => $ordre[$cpt]
                        );
                        $cpt++;
                    }
                }
                $sitePagesStandardSetting->setOptions($Options['options']);

                $sitePagesStandardSetting->upload($program);
                $em->persist($sitePagesStandardSetting);
            }

            $em->flush();
        }

        //Get all pages with programm
        $AllPagesSetting = $em->getRepository("AdminBundle:SitePagesStandardSetting")->findBy(
            array('program' => $program)
        );

        //Get all pages default
        $AllPagesDefault = $em->getRepository(SitePagesStandardDefault::class)->findAll();

        if (count($AllPagesSetting) > 0) {
            $AllPages = $AllPagesSetting;
        } else {
            $AllPages = $AllPagesDefault;
        }

        return $this->render(
            'AdminBundle:Parametrages:pages_standard.html.twig',
            array(
            'AllPages' => $AllPages,
            'Onglets' => $Onglets
            )
        );
    }

    /**
     * @Route("/temporaire/resultats-points")
     */
    public function pointResultAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $em = $this->getDoctrine()->getManager();
        $userPointList = $em->getRepository('AdminBundle\Entity\UserPoint')
            ->findAllWithUserDataByProgram($program);

        return $this->render(
            'AdminBundle:Parametrages:temp_point_result.html.twig',
            array(
            'user_point_list' => $userPointList,
            )
        );
    }
}
