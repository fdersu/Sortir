<?php


namespace App\Upload;


use App\Entity\User;

class UserImages
{

    public function save($file, User $user, $directory){

        $newFileName = $user->getPseudo() . '_' . uniqid() . '.' . $file->guessExtension();
        $file->move($directory, $newFileName);
        $user->setPhoto($newFileName);

    }
}