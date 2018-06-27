<?php
/**
 * Created by PhpStorm.
 * User: Matthieu
 * Date: 26/06/2018
 * Time: 11:59
 */

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    //on crée une propriété qui va stocker le chemin vers le dossier d'upload
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        //lors de l'instanciation, on remplit la propriété $targetDirectory
        $this->targetDirectory = $targetDirectory;
    }

    //on crée la méthode qui va effectuer le transfert du fichier et qui va générer un nom aléatoire
    public function upload(UploadedFile $file, $oldFileName = null)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move($this->getTargetDirectory(), $fileName);

        //suppression de l'ancienne image s'il y en a une
        if($oldFileName and file_exists($this->targetDirectory . '/' . $oldFileName)){
            unlink($this->targetDirectory . '/' . $oldFileName);
        }
        return $fileName;
    }

    //getter de $targetDirectory
    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}