<?php

namespace App\Controller;

use App\Entity\NotebookFiles;
use App\Service\EscapingService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MainController extends AbstractController
{
    private $em;
    private $fileSystem;
    private $escapingService;
    private $validator;


    public function __construct(EntityManagerInterface $em, Filesystem $filesystem, EscapingService $escapingService, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->fileSystem = $filesystem;
        $this->escapingService = $escapingService;
        $this->validator = $validator;
    }

    /**
     * @Route("/main", name="main")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MainController.php',
        ]);
    }

    /**
     * @Route("/upload", name="upload", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request){
        $url = $request->get('url');
        $username = $request->get('username');
        $fileId = $request->get('file_id');
        $fileName = $request->get('filename');
        $coursecode = $request->get('coursecode');


        $noteBook = NotebookFiles::mainConstructor($fileId,$url, $username, $coursecode);
        $errors = $this->validator->validate($noteBook);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        $storagePath = $this->getParameter('app.storage');
        $temporaryStoragePath = $this->getParameter('app.temporarystorage');

        $temporaryFilePath = $temporaryStoragePath."\\".$fileName;
        $output = file_put_contents($temporaryFilePath, fopen($url, 'r'));

        if(is_int($output)){
            //File correctly uploaded
            $file = new File($temporaryFilePath,$url);
            //Landing folder depends on user's name
            $userSpecificFolder = $this->escapingService->escape($username, "-")."\\".$coursecode;
            $folderName = $storagePath."\\".$userSpecificFolder;

            //check if folder exists
            if(!$this->fileSystem->exists($folderName)) {
                $this->fileSystem->mkdir($folderName);
            }

            if ($file) {
                $originalFilename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $file->move($folderName, $newFilename);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                //Let's check file has been moved correctly
                if($this->fileSystem->exists($folderName."\\".$newFilename)){
                    //DB insert
                    try {
                        $noteBook->setPath($userSpecificFolder);
                        $this->em->persist($noteBook);
                        $this->em->flush();
                    }catch (\Exception $exception){
                        $this->fileSystem->remove($folderName."\\".$newFilename);
                    }

                }else{
                    $this->fileSystem->remove($temporaryFilePath);
                    //We should delete the temporary file
                }
            };

        }




        return new Response("File caricato");




    }

    public function fileExists(){}


}
