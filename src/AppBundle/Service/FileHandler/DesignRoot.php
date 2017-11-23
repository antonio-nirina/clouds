<?php

namespace AppBundle\Service\FileHandler;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class DesignRoot
{
    private $fs;
    private $root_path;
    private $default;

    public function __construct($root_path)
    {
        $this->fs = new Filesystem();
        $this->root_path = $root_path;
        $this->default = 'css/root.css';
    }

    public function exists($id)
    {
        $exist = $this->fs->exists($this->root_path.'/'.$id.'/root.css');
        if ($exist) {
            return $this->root_path.'/'.$id.'/root.css';
        } else {
            return $this->default;
        }
    }

    public function resetRoot($id, $colors, $police)
    {
        $default = file_get_contents($this->default);
        $new_root_css = str_replace(
            array("#1d61d4","#598fea","#7682da","#505050","#505050","#807f81","#ebeeef"),
            array(
                $colors["couleur_1"],
                $colors["couleur_1_bis"],
                $colors["couleur_2"],
                $colors["couleur_3"],
                $colors["couleur_4"],
                $colors["couleur_5"],
                $colors["couleur_6"],
            ),
            $default
        );
        $new_root_css = str_replace(
            array("Lato-Light","Lato-Regular","Lato-Bold"),
            array(
                $police."-Light",
                $police."-Regular",
                $police."-Bold"
            ),
            $new_root_css
        );
        $this->fs->dumpFile($this->root_path.'/'.$id.'/root.css', $new_root_css);
    }
}
