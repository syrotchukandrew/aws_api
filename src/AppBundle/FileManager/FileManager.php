<?php

namespace AppBundle\FileManager;

use AppBundle\AwsManager\AwsS3Manager;
use AppBundle\Entity\Picture;

class FileManager
{
    private $rootDir;

    /**
     * @var AwsS3Manager
     */
    private $aws;

    public function __construct($rootDir, AwsS3Manager $aws)
    {
        $this->rootDir = $rootDir;
        $this->aws = $aws;
    }

    public function fileManager(Picture $picture)
    {
        if ($picture->getPicture() !== null) {
            $file = $picture->getPicture();
            $fileName = 'images/' . md5(uniqid()) . '.' . $file->guessExtension();
            $imagesDir = $this->rootDir . '/../web/images';
            $file->move($imagesDir, $fileName);
            $picture->setPictureName($fileName);
            try {
                $this->aws->putObject($fileName, $fileName);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $picture;
    }
}