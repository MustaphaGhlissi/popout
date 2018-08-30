<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 07/03/2018
 * Time: 12:04
 */

namespace App\Service;


class FileUploader
{
    private $targetDir;
    public function __construct(string $targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function getTargetDir(): string
    {
        return $this->targetDir;
    }

    public function upload(string $base64, string $imgName): void
    {
        $image = base64_decode($base64);
        $filePath = $this->getTargetDir() .'/'. $imgName;
        file_put_contents($filePath, $image);
    }
}