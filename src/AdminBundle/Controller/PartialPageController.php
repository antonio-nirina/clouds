<?php
namespace AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Component\SiteForm\FieldType;

class PartialPageController extends Controller
{
    private $text_type;
    private $choice_type;
    private $single_checkbox_type;

    public function __construct()
    {
        $this->text_type = array(
            FieldType::TEXT,
            FieldType::NUM_TEXT,
            FieldType::ALPHANUM_TEXT,
            FieldType::ALPHA_TEXT,
            FieldType::EMAIL,
            FieldType::PASSWORD,
        );
        $this->choice_type = array(
            FieldType::CHOICE_RADIO,
        );
        $this->date_type = array(
            FieldType::DATE,
        );
        $this->single_checkbox_type = array(
            FieldType::CHECKBOX,
        );
    }

    public function siteFormFieldRowAction($field)
    {
        if (in_array($field->getFieldType(), $this->text_type)) {
            return $this->render('AdminBundle:PartialPage/SiteFormField:text.html.twig', array(
                'field' => $field,
            ));
        } elseif (in_array($field->getFieldType(), $this->choice_type)) {
            $choices = (!empty($field->getAdditionalData()) && array_key_exists('choices', $field->getAdditionalData()))
                ? $field->getAdditionalData()["choices"]
                : array();
            return $this->render('AdminBundle:PartialPage/SiteFormField:radio.html.twig', array(
                'field' => $field,
                'choices' => $choices,
            ));
        } elseif (in_array($field->getFieldType(), $this->date_type)) {
            $template = 'AdminBundle:PartialPage/SiteFormField:date.html.twig';
            return $this->render($template, array(
                'field' => $field,
            ));
        } elseif (in_array($field->getFieldType(), $this->single_checkbox_type)) {
            return $this->render('AdminBundle:PartialPage/SiteFormField:checkbox.html.twig', array(
                'field' => $field,
            ));
        }

        return new Response('');
    }

    
    public function siteFormFieldRow2Action($field, $personalize = false)
    {
        if (in_array($field->getFieldType(), $this->text_type)) {
            return $this->render('AdminBundle:PartialPage/SiteFormField:text2.html.twig', array(
                'field' => $field,
                'personalize' => $personalize
            ));
        } elseif (in_array($field->getFieldType(), $this->choice_type)) {
            $choices = (!empty($field->getAdditionalData()) && array_key_exists('choices', $field->getAdditionalData()))
                ? $field->getAdditionalData()["choices"]
                : array();
            return $this->render('AdminBundle:PartialPage/SiteFormField:radio2.html.twig', array(
                'field' => $field,
                'choices' => $choices,
                'personalize' => $personalize
            ));
        } elseif (in_array($field->getFieldType(), $this->date_type)) {
            $template = 'AdminBundle:PartialPage/SiteFormField:date2.html.twig';
            return $this->render($template, array(
                'field' => $field,
                'personalize' => $personalize
            ));
        }

        return new Response('');
    }

    public function siteFormManyFieldsRowAction($field, $personalize = false)
    {
        // dump($level);die;
        $em = $this->getDoctrine()->getManager();
        $row = $field->getInRow();
        $form_setting = $field->getSiteFormSetting();
        $all_fields_row = $em->getRepository("AdminBundle:SiteFormFieldSetting")->findAllInRow(
            $row,
            $form_setting->getId(),
            $field->getLevel()
        );

        return $this->render('AdminBundle:PartialPage/SiteFormField:row.html.twig', array(
            'field' => $field,
            'all_fields' => $all_fields_row,
            'personalize' => $personalize
        ));
    }
	
	public function afficheContenuPagesStandardAction(array $datas){
		if(isset($datas['page']) && !empty($datas['page'])){
			
			//On recupere les infos de la page parametrés
			$em = $this->getDoctrine()->getManager();
			
			$PagesSettings = array();
			$PagesSettings = $em->getRepository("AdminBundle:SitePagesStandardSetting")->find($datas['page']);
			
			//On recupere les pages par defaut
			$PagesDefault = array();
			$PagesDefault = $em->getRepository('AdminBundle:SitePagesStandardDefault')->find($datas['page']);
			
			$Page = array();
			if(count($PagesSettings) > 0){
				$Page = $PagesSettings;
			}else{
				$Page = $PagesDefault;
			}
		}
		
		$NewPage = "";
		if(isset($datas['new_page']) && !empty($datas['new_page'])){
			$NewPage = $datas['new_page'];
		}
		
		
		return $this->render('AdminBundle:PartialPage/Ajax:afficheContenuPagesStandard.html.twig', array(
			'Page' => $Page,
			'NewPage' => $NewPage,
			'idpagehtml' => $datas['page']
		));
	}
	
	public function affichePopUpImgEditorAction(array $datas){
		//On recupere les infos de la page parametrés
		$em = $this->getDoctrine()->getManager();
		
		return $this->render('AdminBundle:PartialPage/Ajax:affichePopUpImgEditor.html.twig');
	}
}
