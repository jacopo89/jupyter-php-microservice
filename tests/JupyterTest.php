<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JupyterTest extends WebTestCase
{
    public function testShareProfToStud()
    {
        $client = static::createClient();
        self::bootKernel();
        $container = self::$container;


        $courseCode = "PYTHON";
        $jupyterId = 1;
        $usersList = ["piero.piero"];
        $sharedFileList = ["C:\Users\jacop\Desktop\projects\php\\test\public\data\jacopo-2etrapani\PYTHON\ES1\shared\python.png"];
        $isMasterUser = true;

        $parameters["courseCode"] = $courseCode; $parameters["jupyterId"] = $jupyterId; $parameters["usersList"] = $usersList;
        $parameters["sharedFileList"] = $sharedFileList;
        $parameters["isMasterUser"] = $isMasterUser;



        $client->request('POST', 'jupyter/share',$parameters);

        $content = $client->getResponse()->getContent();
        $sharedFiles = json_decode($content,true);


        $fileSystem = $container->get('filesystem');

        foreach ($sharedFiles as $sharedFile){

            $this->assertTrue($fileSystem->exists($sharedFile));
        }

    }

    public function testGetSharebleFiles()
    {
        $client = static::createClient();
        self::bootKernel();
        $container = self::$container;


        $courseCode = "PYTHON";
        $jupyterId = 1;
        $username = "jacopo.trapani";
        $isMasterUser = true;
        //$usersList = ["piero.piero"];
        //$sharedFileList = ["C:\Users\jacop\Desktop\projects\php\\test\public\data\jacopo-2etrapani\PYTHON\ES1\shared\python.png"];

        $parameters["coursecode"] = $courseCode; $parameters["jupyterId"] = $jupyterId; $parameters["username"] = $username;
        $parameters["isMasterUser"] = $isMasterUser;
        //$parameters["sharedFileList"] = $sharedFileList;


        $client->request('POST', 'jupyter/get',$parameters);

        $content = $client->getResponse()->getContent();
        $files = json_decode($content,true);


        $this->assertNotEmpty($files);

    }

    public function testGetSharebleFilesUnexistingUser()
    {
        $client = static::createClient();
        self::bootKernel();
        $container = self::$container;


        $courseCode = "PYTHON";
        $jupyterId = 1;
        $username = "gigi.dimaio";

        $parameters["coursecode"] = $courseCode; $parameters["jupyterId"] = $jupyterId; $parameters["username"] = $username;
        //$parameters["sharedFileList"] = $sharedFileList;


        $client->request('POST', 'jupyter/get',$parameters);

        $content = $client->getResponse()->getContent();
        $files = json_decode($content,true);

        $this->assertEmpty($files);

    }


    public function testShareStudToProf()
    {
        $client = static::createClient();
        self::bootKernel();
        $container = self::$container;


        $courseCode = "PYTHON";
        $jupyterId = 1;
        $usersList = ["jacopo.trapani"];
        $sharedFileList = ["C:\Users\jacop\Desktop\projects\php\\test\public\data\piero-2epiero\ES1\inbox\python.png"];
        $isMasterUser = false;

        $parameters["coursecode"] = $courseCode; $parameters["jupyterId"] = $jupyterId; $parameters["usersList"] = $usersList;
        $parameters["sharedFileList"] = $sharedFileList;
        $parameters["isMasterUser"] = $isMasterUser;

        $client->request('POST', 'jupyter/share',$parameters);

        $content = $client->getResponse()->getContent();
        $sharedFiles = json_decode($content,true);


        $fileSystem = $container->get('filesystem');

        foreach ($sharedFiles as $sharedFile){

            $this->assertTrue($fileSystem->exists($sharedFile));
        }

    }


    public function testUploadFile(){
        $client = static::createClient();
        self::bootKernel();
        $container = self::$container;


        $courseCode = "PYTHON";
        $jupyterId = 2;
        $username = "jacopo.trapani";
        $file["filename"] = "gigi.png";
        $file["url"] = "C:\Users\jacop\Desktop\projects\php\\test\public\data\piero-2epiero\ES1\inbox\python.png";
        $files = [];
        $files[] = $file;

        $parameters["coursecode"] = $courseCode; $parameters["jupyterId"] = $jupyterId; $parameters["username"] = $username;
        $parameters["sharedFiles"] = $files;

        $client->request('POST', 'jupyter/upload',$parameters);

        $content = $client->getResponse()->getContent();
        $sharedFiles = json_decode($content,true);


        $fileSystem = $container->get('filesystem');

        foreach ($sharedFiles as $sharedFile){

            $this->assertTrue($fileSystem->exists($sharedFile));
        }

    }
}
