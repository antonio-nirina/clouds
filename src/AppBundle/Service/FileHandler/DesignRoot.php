<?php

namespace AppBundle\Service\FileHandler;

use AdminBundle\Entity\SiteDesignSetting;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class DesignRoot
{
    private $fs;
    private $rootPath;
    private $default;

    public function __construct($rootPath)
    {
        $this->fs = new Filesystem();
        $this->rootPath = $rootPath;
        $this->default = 'css/root.css';
    }

    public function exists($id)
    {
        $exist = $this->fs->exists($this->rootPath . '/' . $id . '/root.css');
        if ($exist) {
            return $this->rootPath . '/' . $id . '/root.css';
        } else {
            return $this->default;
        }
    }

    public function resetRoot($id, SiteDesignSetting $siteDesign)
    {
        $colors = $siteDesign->getColors();
        $police = $siteDesign->getPolice();
        $path =   $siteDesign->getLogoPath();
        $nom = $siteDesign->getLogoName();

        $default = file_get_contents($this->default);
        $colors['couleur_th'] = $this->hex2rgba($colors["couleur_2"], true);
        $newRootCss = str_replace(
            array(
                "#1d61d4","#598fea",
                "#7682da",
                "--couleur_3: #505050",
                "--couleur_4: #505050",
                "#807f81",
                "#ebeeef",
                "rgba(118, 130, 218, 0.2)"),
            array(
                $colors["couleur_1"],
                $colors["couleur_1_bis"],
                $colors["couleur_2"],
                "--couleur_3: " . $colors["couleur_3"],
                "--couleur_4: " . $colors["couleur_4"],
                $colors["couleur_5"],
                $colors["couleur_6"],
                $colors["couleur_th"],
            ),
            $default
        );

        if ($nom) {
            $newRootCss = str_replace(
                array("cloudRewards", '--nom_logo_display: none'),
                array($nom, '--nom_logo_display: inherit'),
                $newRootCss
            );
        }

        if ($path) {
            $newRootCss = str_replace(
                '--nom_logo_display: inherit',
                '--nom_logo_display: none',
                $newRootCss
            );
        }

        $newRootCss = str_replace(
            array("Lato-Light","Lato-Regular","Lato-Bold"),
            array(
                $police . "-Light",
                $police . "-Regular",
                $police . "-Bold"
            ),
            $newRootCss
        );
        $this->fs->dumpFile($this->rootPath . '/' . $id . '/root.css', $newRootCss);
    }

    public function hex2rgba($hex, $blur = false)
    {
        $hex = str_replace("#", "", $hex);

        switch (strlen($hex)) {
            case 3:
                $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
                $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
                $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
                $a = 1;
                break;
            case 6:
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
                $a = 1;
                break;
            case 8:
                $a = hexdec(substr($hex, 0, 2)) / 255;
                $r = hexdec(substr($hex, 2, 2));
                $g = hexdec(substr($hex, 4, 2));
                $b = hexdec(substr($hex, 6, 2));
                break;
        }
        if ($blur) {
            $a = 0.2;
        }
        $rgba = array($r, $g, $b, $a);
        return 'rgba(' . implode(', ', $rgba) . ')';
    }
}
