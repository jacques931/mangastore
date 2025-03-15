<?php

namespace App\Service;

use App\Entity\Media;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaService
{
    private $mediaDirectory;
    private $slugger;

    public function __construct(string $mediaDirectory, SluggerInterface $slugger)
    {
        $this->mediaDirectory = $mediaDirectory;
        $this->slugger = $slugger;
    }

    public function uploadMedia(UploadedFile $file): ?Media
    {
        $mimeType = $file->getMimeType();
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->mediaDirectory, $newFilename);

            $media = new Media();
            $media->setType($mimeType);
            $media->setPath($newFilename);
            $media->setAlt($originalFilename);

            return $media;
        } catch (FileException $e) {
            return null;
        }
    }

    public function deleteMedia(Media $media): void
    {
        $filePath = $this->mediaDirectory . '/' . $media->getPath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
