<?php

namespace UserBundle\Controller;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use AdminBundle\Component\SiteForm\FieldType;
use FOS\UserBundle\Controller\RegistrationController as BaseController;

class RegistrationController extends BaseController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function registerAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }
        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);
        $parameter = $this->get("user.parameter")->getParam();
        if (!empty($parameter)) {
            $nom = $this->getNameForm($parameter)["all"];
            $nomRadio = $this->getNameForm($parameter, $form)["radio"];
            $resp = new JsonResponse($nomRadio);
            $radio = $resp->getContent();
        } else {
            $nom = "";
            $radio = "";
        }
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
                $var = $this->getValForm($parameter, $form);
                $em = $this->getDoctrine()->getManager()->getRepository("UserBundle:User");
                $al = $em->findAll();
                $code = $this->generateCodeId($al);
                $val = $em->findOneByCode($code);
                while (!empty($val)) {
                    $code = $this->generateCodeId($al);
                    break;
                }
                if (!empty($var)) {
                    $user->setCustomization($var);
                }
                $user->setCode($code);
                $userManager->updateUser($user);
                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }

        return $this->render(
            'UserBundle:Registration:register.html.twig',
            array(
            'form' => $form->createView(),"name" => $nom,"radio" => $radio
            )
        );
    }

    /**
     * Tell the user to check their email provider.
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->get('router')->generate('fos_user_registration_register'));
        }

        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render(
            'UserBundle:Registration:check_email.html.twig',
            array(
            'user' => $user,
            )
        );
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {

        /**
        * @var $userManager \FOS\UserBundle\Model\UserManagerInterface
        */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }


        /**
        * @var $dispatcher EventDispatcherInterface
        */

        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render(
            'UserBundle:Registration:confirmed.html.twig',
            array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(),
            )
        );
    }

    /**
     * @return mixed
     */
    private function getTargetUrlFromSession()
    {
        $key = sprintf('_security.%s.target_path', $this->get('security.token_storage')->getToken()->getProviderKey());

        if ($this->get('session')->has($key)) {
            return $this->get('session')->get($key);
        }
    }

    protected function generateCodeId($total)
    {
        $nombre = "1234567890";
        $code = [];
        $max = count($total);
        $i = 6;
        $ecart = $max - pow(10, $i);
        if ($ecart < 0) {
            $length = 6;
        } elseif ($ecart > 0) {
            $length = 7;
        }
        $alphaLength = strlen($nombre) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $code[] = $nombre[$n];
        }

        return implode($code);
    }


    /**
     * @param $parameter
     * @param $form
     * @return array|string
     */
    protected function getValForm($parameter, $form)
    {
        if (!empty($parameter)) {
            foreach ($parameter as $value) {
                $val = $this->get("user.parameter")->traitement($value->getLabel());
                if ($value->getFieldType() == FieldType::TEXT || $value->getFieldType() == FieldType::CHOICE_RADIO) {
                    $var[] = [$value->getLabel() => $form->get($val)->getData()];
                }
            }
            return $var;
        } else {
            return "";
        }
    }

    /**
     * @param $parameter
     * @param $form
     * @return array
     */
    protected function getNameForm($parameter)
    {
        foreach ($parameter as $value) {
            $val = $this->get("user.parameter")->traitement($value->getLabel());
            if ($value->getFieldType() == FieldType::TEXT || $value->getFieldType() == FieldType::CHOICE_RADIO) {
                $nom[] = $val;
            }
            if ($value->getFieldType() == FieldType::CHOICE_RADIO) {
                $nomR[] = $val;
            }
        }
        $nameRadio = !empty($nomR) ? $nomR : "";
        $name = !empty($nom) ? $nom : "";
        return ["all" => $name,"radio" => $nameRadio];
    }
}
