<?php
namespace BeneficiaryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class PartialPageController extends Controller
{
    public function afficheLecteurVideoAction($videos, $programm)
    {
        $urlVideo = "";

        //Youtube
        $isSiteYoutubeShort = strpos($videos->getVideoUrl(), 'youtu.be');
        if ($isSiteYoutubeShort === false) {
            $isSiteYoutubeLong = strpos($videos->getVideoUrl(), 'youtube');
            $isSiteYoutube = $isSiteYoutubeLong;
        } else {
            $isSiteYoutube = $isSiteYoutubeShort;
        }

        if ($isSiteYoutube) {
            $explodeUrlVideo = explode('/', $videos->getVideoUrl());
            $idVideo = $explodeUrlVideo[count($explodeUrlVideo)-1];
            $pos = strpos($idVideo, 'watch');

            if ($pos === false) {
                $urlVideo = $idVideo;
            } else {
                $explodeIdVideo = explode('=', $idVideo);
                $urlVideo = $explodeIdVideo[count($explodeIdVideo)-1];
            }
        }

        //Dailymotion
        $isSiteDailyShort = strpos($videos->getVideoUrl(), 'dai.ly');
        if ($isSiteDailyShort === false) {
            $isSiteDailyLong = strpos($videos->getVideoUrl(), 'dailymotion');
            $isSiteDaily = $isSiteDailyLong;
        } else {
            $isSiteDaily = $isSiteDailyShort;
        }

        if ($isSiteDaily) {
            $explodeUrlVideo = explode('/', $videos->getVideoUrl());
            $idVideo = $explodeUrlVideo[count($explodeUrlVideo)-1];
            $urlVideo = $idVideo;
        }

        return $this->render(
            'BeneficiaryBundle:PartialPage/Ajax:afficheLecteurVideo.html.twig',
            array(
            'videos' => $urlVideo,
            'programm' => $programm,
            'IsSiteYoutube' => $isSiteYoutube,
            'IsSiteDaily' => $isSiteDaily,
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
