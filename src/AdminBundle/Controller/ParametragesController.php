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
use AdminBundle\Form\FormStructureDeclarationType;
use AdminBundle\Form\FormStructureType;
use AdminBundle\Component\SiteForm\FieldTypeName;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

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
    public function importsAction()
    {
        return $this->render('AdminBundle:Parametrages:Imports.html.twig', array());
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
     * @Route("/resultats/declaration/new", name="admin_new_resultat_declaration")
     * @Method("POST")
     */
    public function newFormulaireDeclaration()
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

        $url = 'cloud-rewards.peoplestay.com';
        $program = $em->getRepository('AdminBundle:Program')->findByUrl($url);

        if (empty($program)) {//redirection si program n'existe pas
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $program = $program[0];
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
}
