<?php

namespace App\Controller;

use App\Service\EscapingService;
use App\Service\JupyterFolderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/jupyter", name="jupyter")
 */
class JupyterController extends AbstractController
{

    private $em;
    private $fileSystem;
    private $escapingService;
    private $validator;
    private $jupyterFolderService;


    public function __construct(EntityManagerInterface $em, Filesystem $filesystem, EscapingService $escapingService, ValidatorInterface $validator, JupyterFolderService $jupyterFolderService)
    {
        $this->em = $em;
        $this->fileSystem = $filesystem;
        $this->escapingService = $escapingService;
        $this->validator = $validator;
        $this->jupyterFolderService = $jupyterFolderService;

    }
    /**
     * @Route("/", name="jupyter_index")
     */
    public function index()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/JupyterController.php',
        ]);
    }

    /**
     * @Route("/get", name="jupyter_get_files", methods={"POST"})
     * @param Request $request
     * @return Response
     *
     * This method returns the list of shareable files (the files inside the "shared" folder
     */
    public function getShareableFiles(Request $request){
        $coursecode = $request->get('coursecode');
        $jupyterId = $request->get('jupyterId');
        $username = $request->get('username');
        $isMasterUser = $request->get('isMasterUser');

        $storagePath = $this->getParameter('app.storage');

        $files = $this->jupyterFolderService->getShareableFiles($coursecode,$jupyterId,$username,$storagePath, $isMasterUser);
        $content = json_encode($files);

        $response = new Response();
        $response->setContent($content);

        return $response;

    }


    /**
     * @Route("/share", name="jupyter_share", methods={"POST"})
     * @param Request $request
     * @return Response
     *
     * This method a user to share a file to a userlist
     */
    public function share(Request $request){

        $storagePath = $this->getParameter('app.storage');

        $coursecode = $request->get('coursecode');
        $jupyterId = $request->get('jupyterId');
        $usersList = $request->get('usersList');
        $sharedFileList = $request->get('sharedFileList');
        $isMasterUser = $request->get('isMasterUser');

        $overwriteOption = $isMasterUser;

        $sharedFiles = $this->jupyterFolderService->share($coursecode, $jupyterId, $usersList, $sharedFileList, $storagePath, $overwriteOption);
        $content = json_encode($sharedFiles);

        $response = new Response();
        $response->setContent($content);

        return $response;

    }

    /**
     * @Route("/upload", name="jupyter_upload", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function upload(Request $request){

        $storagePath = $this->getParameter('app.storage');
        $temporaryStoragePath = $this->getParameter('app.temporarystorage');

        $username = $request->get('username');
        $jupyterId = $request->get('jupyterId');
        $files = $request->get('sharedFiles');
        $coursecode = $request->get('coursecode');

        $addedFiles = $this->jupyterFolderService->uploadFiles($storagePath,$temporaryStoragePath, $username, $coursecode, $jupyterId,$files);
        $content = json_encode($addedFiles);

        $response = new Response();
        $response->setContent($content);

        return $response;

    }

}
