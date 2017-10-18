<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Controller\ChangePasswordController as BaseController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Util\LegacyFormHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use UserBundle\Form\FirstChangePasswordType;

/**
 * @Route("/admin/log/first")
 */
class FirstLoginController extends BaseController
{
    /**
     * @Route("/", name="admin_first_log")
     */
    public function indexAction(Request $request) 
    {
    	$user = $this->getUser();

    	if($user->getTemporaryPwd())
    	{
    		$this->redirectToRoute('admin_dashboard_show');
    	}
    	// dump($user); dump($user instanceof User); die;

        if (!is_object($user) /*|| !$user instanceof User*/) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        /** @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        /** @var $formFactory FactoryInterface */
        // $formFactory = $this->get('fos_user.change_password.form.factory');

        $form = $this->createForm(FirstChangePasswordType::class,$user);        
        // $form->setData($user);
        // dump($form); die;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $userManager UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_SUCCESS, $event);


            $user->setTemporaryPwd(true);//mis à mdp temporaire
            $userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::CHANGE_PASSWORD_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('@FOSUser/ChangePassword/first_change_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
}