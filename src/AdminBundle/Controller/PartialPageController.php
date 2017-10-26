<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AdminBundle\Component\SiteForm\FieldType;

class PartialPageController extends Controller
{
    private $text_type;
    private $choice_type;

    public function __construct()
    {
        $this->text_type = array(
            FieldType::TEXT,
            FieldType::NUM_TEXT,
            FieldType::ALPHANUM_TEXT,
            FieldType::ALPHA_TEXT,
            FieldType::EMAIL,
        );

        $this->choice_type = array(
            FieldType::CHOICE_RADIO,
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
        }

        return new Response('');
    }
}