<?php
namespace BeneficiaryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PartialPageController extends Controller
{
    public function afficheLecteurVideoAction($videos, $programm)
    {
        $UrlVideo = "";

        //Youtube
        $IsSiteYoutubeShort = strpos($videos->getVideoUrl(), 'youtu.be');
        if ($IsSiteYoutubeShort === false) {
            $IsSiteYoutubeLong = strpos($videos->getVideoUrl(), 'youtube');
            $IsSiteYoutube = $IsSiteYoutubeLong;
        } else {
            $IsSiteYoutube = $IsSiteYoutubeShort;
        }

        if ($IsSiteYoutube) {
            $ExplodeUrlVideo = explode('/', $videos->getVideoUrl());
            $IdVideo = $ExplodeUrlVideo[count($ExplodeUrlVideo)-1];
            $pos = strpos($IdVideo, 'watch');

            if ($pos === false) {
                $UrlVideo = $IdVideo;
            } else {
                $ExplodeIdVideo = explode('=', $IdVideo);
                $UrlVideo = $ExplodeIdVideo[count($ExplodeIdVideo)-1];
            }
        }

        //Dailymotion
        $IsSiteDailyShort = strpos($videos->getVideoUrl(), 'dai.ly');
        if ($IsSiteDailyShort === false) {
            $IsSiteDailyLong = strpos($videos->getVideoUrl(), 'dailymotion');
            $IsSiteDaily = $IsSiteDailyLong;
        } else {
            $IsSiteDaily = $IsSiteDailyShort;
        }

        if ($IsSiteDaily) {
            $ExplodeUrlVideo = explode('/', $videos->getVideoUrl());
            $IdVideo = $ExplodeUrlVideo[count($ExplodeUrlVideo)-1];
            $UrlVideo = $IdVideo;
        }

        return $this->render(
            'BeneficiaryBundle:PartialPage/Ajax:afficheLecteurVideo.html.twig',
            array(
            'videos' => $UrlVideo,
            'programm' => $programm,
            'IsSiteYoutube' => $IsSiteYoutube,
            'IsSiteDaily' => $IsSiteDaily,
            )
        );
    }

    /**
     * Partial page showing account block action
     *
     * @return Response
     */
    public function accountBlockAction()
    {
        $program = $this->container->get('admin.program')->getCurrent();
        if (empty($program)) {
            return new Response();
        }
        $em = $this->getDoctrine()->getManager();
        $programUser = $em->getRepository('AdminBundle\Entity\ProgramUser')->findOneBy(array(
            'program' => $program,
            'specialUseCaseState' => true,
        ));
        if (is_null($programUser)) {
            return new Response();
        }
        $appUser = $programUser->getAppUser();
        if (is_null($appUser)) {
            return new Response();
        }
        $userPoints = $em->getRepository('AdminBundle\Entity\UserPoint')->findBy(
            array('program_user' => $programUser),
            array('date' => 'DESC')
        );

        return $this->render('BeneficiaryBundle:PartialPage:account_block.html.twig', array(
            'app_user' => $appUser,
            'user_point' => !empty($userPoints) ? $userPoints[0] : null,
        ));
    }
}
