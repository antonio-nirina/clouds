<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Entity\Program;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SiteFormType;
use AdminBundle\Form\FormStructureType;
use AdminBundle\Component\SiteForm\FieldTypeName;
use AdminBundle\Form\RegistrationImportType;
use AdminBundle\Component\SiteForm\SpecialFieldIndex;

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
        $site_form_field_settings = array();
        $site_form_setting = null;
        if (!empty($programs)) {
            $program = $programs[0];
            $site_form_setting = $em->getRepository(SiteFormSetting::class)->findByProgramAndTypeWithField(
                $program,
                SiteFormType::REGISTRATION_TYPE
            );
            $site_form_field_settings = $site_form_setting->getSiteFormFieldSettings();
        }

        $form_structure_form = $this->createForm(FormStructureType::class);
        $form_structure_form->handleRequest($request);
        if ($form_structure_form->isSubmitted() && $form_structure_form->isValid()) {
            $field_order = json_decode($form_structure_form->getData()['field-order']);
            $field_order = array_flip($field_order);

            $current_field_list = json_decode($form_structure_form->getData()['current-field-list']);
            if (!is_null($current_field_list)) {
                foreach ($current_field_list as $field_data) {
                    $field = $em->getRepository(SiteFormFieldSetting::class)->findOneById(intval($field_data->id));
                    if (!is_null($field)) {
                        $field->setPublished(boolval($field_data->published));
                        $field->setMandatory(boolval($field_data->mandatory));
                        if (array_key_exists($field->getId(), $field_order)) {
                            $field->setFieldOrder($field_order[$field->getId()]);
                        }
                    }
                }
            }

            $new_field_list = json_decode($form_structure_form->getData()['new-field-list']);
            if (!is_null($new_field_list)) {
                foreach ($new_field_list as $new_field) {
                    if (!is_null($site_form_setting)
                        &&
                        (
                            is_int($site_form_setting->getCustomFieldAllowed())
                            && $site_form_setting->getCustomFieldAllowed() > 0
                        )
                    ) {
                        $field = new SiteFormFieldSetting();
                        $field->setSiteFormSetting($site_form_setting)
                                ->setFieldType($new_field->field_type)
                                ->setMandatory(boolval($new_field->mandatory))
                                ->setLabel($new_field->label)
                                ->setFieldOrder(30); // big value, to put new field at the bottom
                        if (array_key_exists('choices', $new_field)) {
                            $choices = array_map('strval', (array)$new_field->choices);
                            $choices = array_map('strval', array_flip($choices)); // VALUE is the same as KEY
                            $add_data["choices"] = $choices;
                            $field->setAdditionalData($add_data);
                        }
                        $site_form_setting->addSiteFormFieldSetting($field);
                        $em->persist($field);

                        $site_form_setting->setCustomFieldAllowed(($site_form_setting->getCustomFieldAllowed()) - 1);
                    }
                }
            }

            $field_to_delete_list = ('' != $form_structure_form->getData()['delete-field-action-list'])
                                        ? explode(',', $form_structure_form->getData()['delete-field-action-list'])
                                        : array();
            if (!empty($field_to_delete_list)) {
                foreach ($field_to_delete_list as $field_to_delete_id) {
                    $field = $em->getRepository(SiteFormFieldSetting::class)->findOneById($field_to_delete_id);
                    if (!is_null($field)) {
                        $em->remove($field);
                        $site_form_setting->setCustomFieldAllowed($site_form_setting->getCustomFieldAllowed() + 1);
                    }
                }
            }
            $em->flush();

            return $this->redirectToRoute('admin_parametrages_inscriptions');
        }

        return $this->render('AdminBundle:Parametrages:Inscriptions.html.twig', array(
            'site_form_field_settings' => $site_form_field_settings,
            'form_structure_form' => $form_structure_form->createView(),
            'field_type_list' => FieldTypeName::FIELD_NAME,
            'custom_field_allowed' => $site_form_setting->getCustomFieldAllowed(),
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
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $program = $programs[0];
        $registration_site_form_setting = $em->getRepository('AdminBundle\Entity\SiteFormSetting')
            ->findByProgramAndType($program, SiteFormType::REGISTRATION_TYPE);
        if (is_null($registration_site_form_setting)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $registration_import_form = $this->createForm(RegistrationImportType::class);
        $registration_import_form->handleRequest($request);
        if ($registration_import_form->isSubmitted() && $registration_import_form->isValid()) {
            $import_file = $registration_import_form->getData()["registration_data"];
            $registration_handler = $this->get('AdminBundle\Service\ImportExport\RegistrationHandler');
            $registration_handler->setSiteFormSetting($registration_site_form_setting);
            $registration_handler->import($import_file);

            if (!empty($registration_handler->getErrorList())) {
                $error_list = $registration_handler->getErrorList();
                // show errors
            } else {
                echo "OOOOOkkkk!!";
                // redirect to target page
            }
        }

        return $this->render('AdminBundle:Parametrages:Imports.html.twig', array(
            'registration_form' => $registration_import_form->createView(),
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
}
