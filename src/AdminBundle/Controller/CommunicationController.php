<?php
namespace AdminBundle\Controller;

use AdminBundle\Component\Slide\SlideType;
use AdminBundle\Controller\AdminController;
use AdminBundle\Form\HomePageSlideDataType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/communication")
 */
class CommunicationController extends AdminController
{
    const SIDEBAR_VIEW = 'AdminBundle:Communication:menu_sidebar_communication.html.twig';

    public function __construct()
    {
        $this->active_menu_index = 3;
        $this->sidebar_view = self::SIDEBAR_VIEW;
    }

    /**
     * @Route("/edito", name="admin_communication_editorial")
     */
    public function editorialAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        return $this->render('AdminBundle:Communication:edito.html.twig');
    }

    /**
     * @Route("/slideshow", name="admin_communication_slideshow")
     */
    public function slideshowAction(Request $request)
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $home_page_data = $program->getHomePageData();
        if (is_null($home_page_data)) {
            return $this->redirectToRoute('fos_user_security_logout');
        }

        $original_slides = new ArrayCollection();
        foreach ($home_page_data->getHomePageSlides() as $slide) {
            $original_slides->add($slide);
        }

        $original_slides_image = array();
        foreach ($original_slides as $slide) {
            $original_slides_image[$slide->getId()] = $slide->getImage();
        }

        $form_factory = $this->get('form.factory');
        $home_page_slide_data_form = $form_factory->createNamed(
            'home_page_slide_data_form',
            HomePageSlideDataType::class,
            $home_page_data
        );

        $em = $this->getDoctrine()->getManager();
        if ("POST" === $request->getMethod()) {
            if ($request->request->has('home_page_slide_data_form')) {
                $home_page_slide_data_form->handleRequest($request);
                if ($home_page_slide_data_form->isSubmitted() && $home_page_slide_data_form->isValid()) {
                    // checking for "delete image" commands
                    $deleted_image_slide_id_list = array();
                    foreach ($home_page_slide_data_form->get('home_page_slides') as $home_page_slide) {
                        $delete_image_command = $home_page_slide->get('delete_image_command')->getData();
                        if (!empty($delete_image_command) && 'true' == $delete_image_command) {
                            $slide = $home_page_slide->getNormData();
                            $slide->setImage($original_slides_image[$slide->getId()]);
                            $number_other_slide_using_image = $em->getRepository('AdminBundle\Entity\HomePageSlide')
                                ->retrieveNumberOfOtherSlideUsingImage($home_page_data, $slide);
                            if (0 == $number_other_slide_using_image) {
                                $filesystem = $this->get('filesystem');
                                $image_path = $this->getParameter('content_home_page_slide_image_upload_dir')
                                    .'/'
                                    .$slide->getImage();
                                if ($filesystem->exists($image_path)) {
                                    $filesystem->remove($image_path);
                                }
                            }
                            $slide->setImage(null);
                            array_push($deleted_image_slide_id_list, $slide->getId());
                        }
                    }


                    // editing existant slide
                    foreach ($home_page_data->getHomePageSlides() as $slide) {
                        if (!is_null($slide->getId())) {
                            // setting image for existent slide
                            if (is_null($slide->getImage())) {
                                if (!in_array($slide->getId(), $deleted_image_slide_id_list)) {
                                    // set previous image
                                    if (array_key_exists($slide->getId(), $original_slides_image)) {
                                        $slide->setImage($original_slides_image[$slide->getId()]);
                                    }
                                }

                            } else {
                                // upload new image
                                $image = $slide->getImage();
                                $image->move(
                                    $this->getParameter('content_home_page_slide_image_upload_dir'),
                                    $image->getClientOriginalName()
                                );
                                $slide->setImage($image->getClientOriginalName());
                            }
                        }
                    }

                    // deleting slides
                    foreach ($original_slides as $original_slide) {
                        if (false === $home_page_data->getHomePageSlides()->contains($original_slide)) {
                            $original_slide->setHomePageData(null);
                            $home_page_data->removeHomePageSlide($original_slide);
                            $em->remove($original_slide);
                        }
                    }

                    // adding new slide
                    foreach ($home_page_data->getHomePageSlides() as $slide) {
                        if (is_null($slide->getId())) {
                            $slide->setHomePageData($home_page_data);
                            if (!is_null($slide->getImage())) {
                                $image = $slide->getImage();
                                $image->move(
                                    $this->getParameter('content_home_page_slide_image_upload_dir'),
                                    $image->getClientOriginalName()
                                );
                                $slide->setImage($image->getClientOriginalName());
                            }
                            $em->persist($slide);
                        }
                    }

                    $em->flush();

                    return $this->redirectToRoute('admin_communication_slideshow');
                }
            }
        }

        return $this->render('AdminBundle:Communication:slideshow.html.twig', array(
            'home_page_slide_data_form' => $home_page_slide_data_form->createView(),
            'original_slides_image' => $original_slides_image,
            'slide_type' => new SlideType(),
        ));
    }
}

