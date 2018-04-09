<?php

namespace UserBundle\Service\Parameter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Repository\SiteFormFieldSettingRepository;

class AddFormType
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getParam()
    {
        $lists = [
            "société",
            "adresse postale",
            "code postal",
            "ville",
            "téléphone",
            "civilité",
            "prénom",
            "nom",
            "confirmation e-mail",
            "e-mail",
            "confirmation mot de passe",
            "mot de passe",
            "acceptation du règlement"
        ];
        $em = $this->container->get("doctrine.orm.entity_manager");
        $formParameter = $em->getRepository("AdminBundle:SiteFormFieldSetting")->getChampParameter($lists);
        return $formParameter;
    }

    public function traitement($field, $charset = 'utf-8')
    {
        $char = ["?","!","/","%",":","&","+","*","^","$","¨","{","}","#","~","@","(",")","[","]","|","°","§","£","µ",";",",","<",">","."];
        $newField = str_replace($char, "", $field);
        $str = str_replace(" ", "_", $newField);
        $str = htmlentities($str, ENT_NOQUOTES, $charset);
        $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        $str = preg_replace('#&[^;]+;#', '', $str);
        return $str;
    }
}
