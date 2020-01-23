<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotebookFilesRepository")
 */
class NotebookFiles
{
    public static function mainConstructor($fileId, $url, $username, $coursecode ){

        $noteBookFile = new NotebookFiles();
        $noteBookFile->setFileId($fileId);
        $noteBookFile->setUrl($url);
        $noteBookFile->setUsername($username);
        $noteBookFile->setCoursecode($coursecode);
        $noteBookFile->setPath("path");

        return $noteBookFile;
    }

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Positive()
     * @ORM\Column(type="integer")*
     */
    private $fileId;

    /**
     * @Assert\Url()
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @Assert\NotNull()
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @Assert\NotNull()
     * @ORM\Column(type="string", length=255)
     */
    private $coursecode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileId(): ?int
    {
        return $this->fileId;
    }

    public function setFileId(int $fileId): self
    {
        $this->fileId = $fileId;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getCoursecode(): ?string
    {
        return $this->coursecode;
    }

    public function setCoursecode(string $coursecode): self
    {
        $this->coursecode = $coursecode;

        return $this;
    }
}
