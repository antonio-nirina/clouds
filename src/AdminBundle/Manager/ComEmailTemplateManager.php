<?php
namespace AdminBundle\Manager;

use AdminBundle\Entity\Program;
use AdminBundle\Entity\ComEmailTemplate;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AdminBundle\Component\CommunicationEmail\TemplateModel;
use AdminBundle\Component\CommunicationEmail\TemplateContentType;
use UserBundle\Entity\User as AppUser;

class ComEmailTemplateManager
{
    private $em;
    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function createTemplate(Program $program, ComEmailTemplate $template, AppUser $app_user, $flush = true)
    {
        $logo_image = $template->getLogo();
        if (!is_null($logo_image)) {
            $logo_image->move(
                $this->container->getParameter('emailing_template_logo_upload_dir'),
                $logo_image->getClientOriginalName()
            );
            $template->setLogo($logo_image->getClientOriginalName());
        }
        $template->setProgram($program);
        $template->setLastEditUser($app_user);

        foreach ($template->getContents() as $content) {
            if (TemplateModel::TEXT_ONLY == $template->getTemplateModel()) {
                if (TemplateContentType::TEXT == $content->getContentType()) {
                    $content->setTemplate($template);
                    $this->em->persist($content);
                } else {
                    $template->removeContent($content);
                    $content->setTemplate(null);
                }
            } elseif (TemplateModel::TEXT_AND_IMAGE == $template->getTemplateModel()) {
                if (TemplateContentType::IMAGE == $content->getContentType()) {
                    $image = $content->getImage();
                    if (!is_null($image)) {
                        $image->move(
                            $this->container->getParameter('emailing_template_image_content_upload_dir'),
                            $image->getClientOriginalName()
                        );
                        $content->setImage($image->getClientOriginalName());
                    }
                }
                $content->setTemplate($template);
                $this->em->persist($content);
            }
        }
        $this->em->persist($template);

        if (true == $flush) {
            $this->flush();
            return $template->getId();
        }
    }

    public function editTemplate(
        ComEmailTemplate $template,
        AppUser $app_user,
        $original_logo_image,
        $original_contents_image,
        $delete_logo_image_command,
        $delete_contents_image_command,
        $flush = true
    ) {
        $logo_image = $template->getLogo();
        if (!is_null($logo_image)) {
            $logo_image->move(
                $this->container->getParameter('emailing_template_logo_upload_dir'),
                $logo_image->getClientOriginalName()
            );
            $template->setLogo($logo_image->getClientOriginalName());
        } else {
            if ("true" != $delete_logo_image_command) {
                $template->setLogo($original_logo_image);
            }
        }

        $template->setLastEditUser($app_user);
        $template->setLastEdit(new \DateTime('now'));
        $arr_delete_contents_image_command = explode(',', $delete_contents_image_command);

        foreach ($template->getContents() as $content) {
            if (TemplateContentType::IMAGE == $content->getContentType()) {
                $image = $content->getImage();
                if (!is_null($image)) {
                    $image->move(
                        $this->container->getParameter('emailing_template_image_content_upload_dir'),
                        $image->getClientOriginalName()
                    );
                    $content->setImage($image->getClientOriginalName());
                } else {
                    if (!is_null($content->getId())
                        && array_key_exists($content->getId(), $original_contents_image)
                        && !in_array($content->getId(), $arr_delete_contents_image_command)
                    ) {
                        $content->setImage($original_contents_image[$content->getId()]);
                    }
                }
            }

            if (is_null($content->getId())) {
                $content->setTemplate($template);
                $this->em->persist($content);
            }
        }

        if (true == $flush) {
            $this->flush();
        }
    }

    public function flush()
    {
        $this->em->flush();
    }
}