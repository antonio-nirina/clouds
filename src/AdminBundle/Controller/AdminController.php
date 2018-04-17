<?php
namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    protected $activeMenuIndex;
    protected $sidebarView;

    /**
     * @param type $active
     * @return Response
     */
    public function sidebarAction($active)
    {
        return $this->render(
            $this->sidebarView,
            array(
            'active' => $active,
            )
        );
    }

    /**
     * @param string        $view
     * @param array         $parameters
     * @param Response|null $response
     * @return Response
     */
    protected function render($view, array $parameters = array(), Response $response = null)
    {
        if (!is_null($this->activeMenuIndex)) {
            $activeMenuIndexParameter = array('active_menu_index' => $this->activeMenuIndex);
            $parameters = array_merge($parameters, $activeMenuIndexParameter);
        }

        return parent::render($view, $parameters, $response);
    }
}
