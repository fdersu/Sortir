<?php


namespace App\Upload;


class UserFile
{

    public function save($file, $dataDirectory){

        $newFileName = 'addUser' . $file->guessExtension();
        $file->move($dataDirectory, $newFileName);
    }
}