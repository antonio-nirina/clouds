<?php
// AdminBundle/Controller/ParametragesController.php

namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Entity\Program;
use AdminBundle\Entity\SiteFormSetting;
use AdminBundle\Entity\SiteFormFieldSetting;
use AdminBundle\Component\SiteForm\SiteFormType;
use AdminBundle\Form\FormStructureType;

/**
 * @Route("/admin/parametrages")
 */
class ParametragesController extends Controller
{
    /**
     * @Route("/", name="admin_parametrages_programme")
     */
    public function ProgrammeAction()
    {
        return $this->render('AdminBundle:Parametrages:Programme.html.twig', array());
    }

    /**
     * @Route("/inscriptions", name="admin_parametrages_inscriptions")
     */
    public function inscriptionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $programs = $em->getRepository(Program::class)->findAll();
        $site_form_field_settings = array();
        if (!empty($programs)) {
            $program = $programs[0];
            $site_form_setting = $em->getRepository(SiteFormSetting::class)->findByProgramAndType(
                $program,
                SiteFormType::REGISTRATION_TYPE
            );
            $site_form_field_settings = $site_form_setting->getSiteFormFieldSettings();
        }

        $form_structure_form = $this->createForm(FormStructureType::class);
        $form_structure_form->handleRequest($request);
        if ($form_structure_form->isSubmitted() && $form_structure_form->isValid()) {
            $current_field_list = json_decode($form_structure_form->getData()['current-field-list']);
            if (!is_null($current_field_list)) {
                foreach ($current_field_list as $field_data) {
                    $field = $em->getRepository(SiteFormFieldSetting::class)->findOneById(intval($field_data->id));
                    if (!is_null($field)) {
                        $field->setPublished(boolval($field_data->published));
                        $field->setMandatory(boolval($field_data->mandatory));
                        $em->flush();
                    }
                }
            }

            return $this->redirectToRoute('admin_parametrages_inscriptions');
        }

        return $this->render('AdminBundle:Parametrages:Inscriptions.html.twig', array(
            'site_form_field_settings' => $site_form_field_settings,
            'form_structure_form' => $form_structure_form->createView(),
        ));
    }
	
	/**
     * @Route("/imports", name="admin_parametrages_imports")
     */
    public function ImportsAction()
    {
        return $this->render('AdminBundle:Parametrages:Imports.html.twig', array());
    }
}