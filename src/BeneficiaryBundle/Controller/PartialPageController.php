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
}
