<?php
namespace AdminBundle\Service\ComEmailingTemplate;

use AdminBundle\Entity\ComEmailTemplate;
use Twig\Environment as Twig;
use Knp\Snappy\Image as SnappyImage;
use AdminBundle\Service\ComEmailingTemplate\TemplateDataGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class TemplateThumbnailGenerator
{
    const THUMBNAIL_PREFIX = 'template-';
    const THUMBNAIL_SUFFIX = '-thumbnail';
    const THUMBNAIL_EXTENSION = '.jpg';
    const THUMBNAIL_WIDTH = 500;
    const THUMBNAIL_TEMP_FILE_NAME = 'temp.jpg';

    private $com_email_template;
    private $snappy_image;
    private $twig;
    private $template_data_generator;
    private $container;
    private $filesystem;

    public function __construct(
        Twig $twig,
        SnappyImage $snappy_image,
        TemplateDataGenerator $template_data_generator,
        ContainerInterface $container,
        Filesystem $filesystem
    ) {
        $this->twig = $twig;
        $this->snappy_image = $snappy_image;
        $this->template_data_generator = $template_data_generator;
        $this->container = $container;
        $this->filesystem = $filesystem;
    }

    public function setComEmailTemplate(ComEmailTemplate $com_email_template)
    {
        $this->com_email_template = $com_email_template;

        return $this;
    }

    public function generate()
    {
        if (is_null($this->com_email_template)) {
            throw new NoComEmailTemplateSetException();
        }
        $this->template_data_generator->setComEmailTemplate($this->com_email_template);
        $html_content = $this->template_data_generator->retrieveHtml();
        if ($this->filesystem->exists($this->getThumbnailPath())) {
            $this->filesystem->remove($this->getThumbnailPath());
        }
        $this->snappy_image->generateFromHtml($html_content, $this->getThumbnailPath());
        $this->resizeImage();

        return;
    }

    private function getThumbnailPath()
    {
        return $this->container->getParameter('emailing_template_thumbnail_dir') . '/'
            . $this->getThumbnailName();
    }

    private function getThumbnailName()
    {
        return self::THUMBNAIL_PREFIX
           . $this->com_email_template->getId()
           . self::THUMBNAIL_SUFFIX
           . self::THUMBNAIL_EXTENSION;
    }

    private function getTempFilePath()
    {
        return $this->container->getParameter('emailing_template_thumbnail_dir') . '/'
            . self::THUMBNAIL_TEMP_FILE_NAME;
    }

    private function resizeImage()
    {
        $img = imagecreatefromjpeg($this->getThumbnailPath());
        $width = imagesx($img);
        $height = imagesy($img);
        $new_width = self::THUMBNAIL_WIDTH;
        $new_height = floor($height * ($new_width / $width));
        $tmp_img = imagecreatetruecolor($new_width, $new_height);
        imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagejpeg($tmp_img, $this->getTempFilePath());

        $this->filesystem->remove($this->getThumbnailPath());
        $this->filesystem->rename($this->getTempFilePath(), $this->getThumbnailPath());

        return;
    }

    public function retrieveThumbnailFullName()
    {
        if (is_null($this->com_email_template)) {
            throw new NoComEmailTemplateSetException();
        }

        return $this->container->getParameter('emailing_template_thumbnail_location')
            . $this->getThumbnailName();
    }

    public function deleteThumbnailFile()
    {
        if (is_null($this->com_email_template)) {
            throw new NoComEmailTemplateSetException();
        }
        if ($this->filesystem->exists($this->getThumbnailPath())) {
            $this->filesystem->remove($this->getThumbnailPath());
        }

        return;
    }
}
