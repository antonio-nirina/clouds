<?php
namespace AdminBundle\Form\Handler;

/**
 * Class ELearningBannerHandler
 * @package AppBundle\Form\Handler
 */
class ELearningBannerHandler
{
    protected $elearningBanner;
    protected $currentHeaderImage;
    protected $formElearningBanner;
    protected $em;
    protected $path;

    /**
     * ELearningBannerHandler constructor.
     * @param $elearningBanner
     * @param $currentHeaderImage
     * @param $formElearningBanner
     * @param $path
     * @param $em
     */
    public function __construct($elearningBanner, $currentHeaderImage, $formElearningBanner, $path, $em)
    {
        $this->elearningBanner = $elearningBanner;
        $this->currentHeaderImage = $currentHeaderImage;
        $this->formElearningBanner = $formElearningBanner;
        $this->em = $em;
        $this->path = $path;
    }

    /**
     * FormBanner Submit process
     * @return bool
     */
    public function process()
    {
        $bannerImageFile = $this->elearningBanner->getImageFile();
        if (!is_null($bannerImageFile)) {
            $bannerImageFile->move(
                $this->path,
                $bannerImageFile->getClientOriginalName()
            );
            $this->elearningBanner->setImageFile($bannerImageFile->getClientOriginalName());
        } else {
            $this->elearningBanner->setImageFile($this->currentHeaderImage);
        }
        $menuName = $this->formElearningBanner->get('menuName')->getData();
        $imageTitle = $this->formElearningBanner->get('imageTitle')->getData();
        $this->elearningBanner->setMenuName($menuName);
        $this->elearningBanner->setImageTitle($imageTitle);
        if (!empty($this->formElearningBanner->get('menuName')->getData())
            && "true" == $this->formElearningBanner->get('imageTitle')->getData()
        ) {
            $filesystem = $this->get('filesystem');
            $imagePath = $this->path
                . '/'
                . $this->elearningBanner->getImageFile();
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath);
            }
            $this->elearningBanner->setImageFile(null);
        }
        $this->em->flush();
        return true;
    }
}

