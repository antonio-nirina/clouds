<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    protected $active_menu_index;
    protected $sidebar_view;

    protected function render($view, array $parameters = array(), Response $response = null)
    {
        if (!is_null($this->active_menu_index)) {
            $active_menu_index_parameter = array('active_menu_index' => $this->active_menu_index);
            $parameters = array_merge($parameters, $active_menu_index_parameter);
        }

        return parent::render($view, $parameters, $response);
    }

    public function sidebarAction($active)
    {
        return $this->render($this->sidebar_view, array(
            'active' => $active
        ));
    }
}