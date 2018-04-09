<?php

namespace AdminBundle\Manager;

use AdminBundle\Entity\HomePagePost;
use AdminBundle\Entity\HomePagePostSlide;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\Filesystem\Filesystem;

class SlideshowManager
{
    private $em;
    private $container;
    private $filesystem;

    public function __construct(EntityManager $em, Container $container, Filesystem $filesystem)
    {
        $this->em = $em;
        $this->container = $container;
        $this->filesystem = $filesystem;
    }

    public function getOriginalSlides($home_page_data)
    {
        $original_slides = new ArrayCollection();
        foreach ($home_page_data->getHomePageSlides() as $slide) {
            $original_slides->add($slide);
        }

        return $original_slides;
    }

    public function getOriginalSlidesImage($original_slides)
    {
        $original_slides_image = array();
        foreach ($original_slides as $slide) {
            $original_slides_image[$slide->getId()] = $slide->getImage();
        }

        return $original_slides_image;
    }

    public function checkDeletedImages($home_page_slide_data_form, $home_page_data, $original_slides_image)
    {
        // checking for "delete image" commands
        $deleted_image_slide_id_list = array();
        foreach ($home_page_slide_data_form->get('home_page_slides') as $home_page_slide) {
            $delete_image_command = $home_page_slide->get('delete_image_command')->getData();
            if (!empty($delete_image_command) && 'true' == $delete_image_command) {
                $slide = $home_page_slide->getNormData();
                $slide->setImage($original_slides_image[$slide->getId()]);
                $number_other_slide_using_image = $this->em->getRepository('AdminBundle\Entity\HomePageSlide')
                    ->retrieveNumberOfOtherSlideUsingImage($home_page_data, $slide);
                if (0 == $number_other_slide_using_image) {
                    $filesystem = $this->filesystem;
                    $image_path = $this->container->getParameter('content_home_page_slide_image_upload_dir')
                        . '/'
                        . $slide->getImage();
                    if ($filesystem->exists($image_path)) {
                        $filesystem->remove($image_path);
                    }
                }
                $slide->setImage(null);
                array_push($deleted_image_slide_id_list, $slide->getId());
            }
        }

        return $deleted_image_slide_id_list;
    }

    public function editHomePageSlides($home_page_data, $deleted_image_slide_id_list, $original_slides_image)
    {
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
                        $this->container->getParameter('content_home_page_slide_image_upload_dir'),
                        $image->getClientOriginalName()
                    );
                    $slide->setImage($image->getClientOriginalName());
                }
            }
        }

        return $home_page_data;
    }

    public function deleteHomePageSlides($home_page_data, $original_slides)
    {
        // deleting slides
        foreach ($original_slides as $original_slide) {
            if (false === $home_page_data->getHomePageSlides()->contains($original_slide)) {
                $original_slide->setHomePageData(null);
                $home_page_data->removeHomePageSlide($original_slide);
                $this->em->remove($original_slide);
            }
        }

        return $home_page_data;
    }

    public function addNewHomePageSlides($home_page_data)
    {
        // adding new slide
        foreach ($home_page_data->getHomePageSlides() as $slide) {
            if (is_null($slide->getId())) {
                $slide->setHomePageData($home_page_data);
                if (!is_null($slide->getImage())) {
                    $image = $slide->getImage();
                    $image->move(
                        $this->container->getParameter('content_home_page_slide_image_upload_dir'),
                        $image->getClientOriginalName()
                    );
                    $slide->setImage($image->getClientOriginalName());
                }
                $this->em->persist($slide);
            }
        }

        return $home_page_data;
    }


    public function save()
    {
        return $this->em->flush();
    }
}
