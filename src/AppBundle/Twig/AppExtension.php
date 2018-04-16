<?php

namespace AppBundle\Twig;

use Twig_Extension;

class AppExtension  extends Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('html', [$this, 'html'], ['is_safe' => ['html','css','html_attr','url']]),
        ];
    }

    public function html($html)
    {
        return $html;
    }

    public function getName()
    {
        return 'app_extension';
    }
}