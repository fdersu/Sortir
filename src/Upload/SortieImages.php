<?php


namespace App\Upload;


use App\Entity\Sortie;


class SortieImages
{
    public function save($file, Sortie $sortie, $directory){

        $newFileName = $sortie->getUrlPhoto() . '_' . uniqid() . '.' . $file->guessExtension();
        $file->move($directory, $newFileName);
        $sortie->setUrlPhoto($newFileName);

    }
}