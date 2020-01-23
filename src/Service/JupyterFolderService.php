<?php
/**
 * Created by PhpStorm.
 * User: jacop
 * Date: 22-Jan-20
 * Time: 5:49 PM
 */

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class JupyterFolderService
{
    private $em;
    private $fileSystem;
    private $escapingService;

    const INBOX = "inbox";
    const SHARED = "shared";



    public function __construct(EntityManagerInterface $em, Filesystem $filesystem, EscapingService $escapingService)
    {
        $this->em = $em;
        $this->fileSystem = $filesystem;
        $this->escapingService = $escapingService;
    }



    public function share($coursecode, $jupyterId, $usersList, $sharedFileList, $storagePath, $overWriteOption = false)
    {
        $sharedFiles = [];
        $targetFolder = self::INBOX;

        foreach($usersList as $username){

            $folderPath = $this->getFolderPath($storagePath,$username,$coursecode,$jupyterId,$targetFolder);

            foreach ($sharedFileList as $sharedFile){
                $fileHandler = new File($sharedFile);
                $fileName = $fileHandler->getFilename();
                $targetFile = $folderPath."\\".$fileName;

                $this->fileSystem->copy($sharedFile, $targetFile, $overWriteOption);

                $sharedFiles[] = $targetFile;
            }
        }

        return $sharedFiles;

    }

    public function getShareableFiles($coursecode, $jupyterId, $username, $storagePath, $isMasterUser = false)
    {
        $finder = new Finder();

        $searchFolder = ($isMasterUser) ? self::SHARED : self::INBOX;

        $folderPath = $this->getFolderPath($storagePath,$username, $coursecode, $jupyterId, $searchFolder);


        $finder->files()->in($folderPath);

        $files = [];
        foreach ($finder as $file) {
            $absoluteFilePath = $file->getRealPath();
            $files[] = $absoluteFilePath;

        }
        return $files;
    }


    public function uploadFiles($storagePath, $temporaryStoragePath, $username, $courseCode, $jupyterId, $files){

        $addedFiles = [];
        foreach ($files as $file){
            $fileName = $file["filename"];
            $fileUrl = $file["url"];
            $folderPath = $this->getFolderPath($storagePath,$username, $courseCode, $jupyterId, self::INBOX);

            $temporaryFilePath = $temporaryStoragePath."\\".$fileName;
            $output = file_put_contents($temporaryFilePath, fopen($fileUrl, 'r'));

            if(is_int($output)){
                //File correctly uploaded
                $file = new File($temporaryFilePath,$fileUrl);


                //Landing folder depends on user's name

                if ($file) {
                    $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move($folderPath, $newFilename);
                        $addedFiles[] = $folderPath."\\".$newFilename;

                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    //Let's check file has been moved correctly
                    if($this->fileSystem->exists($folderPath."\\".$newFilename)){
                        $this->fileSystem->remove($temporaryFilePath);
                    }
                };

            }

        }

        return $addedFiles;
    }



    private function getFolderPath($storagePath, $username, $courseCode, $jupyterId, $lastFolder){

        $userFolder = $this->escapingService->escape($username,"-");
        $jupyterFolder = "ES".$jupyterId;

        $basePath = $storagePath."\\".$userFolder."\\".$courseCode."\\".$jupyterFolder;
        $folderPath = $basePath."\\".$lastFolder;

        if(!$this->fileSystem->exists($folderPath)){
            $missingFolder = ($lastFolder === self::SHARED) ? self::INBOX : self::SHARED;
            $missingFolderPath = $basePath."\\".$missingFolder;
            $this->fileSystem->mkdir($folderPath);
            $this->fileSystem->mkdir($missingFolderPath);
        }

        return $folderPath;
    }



}