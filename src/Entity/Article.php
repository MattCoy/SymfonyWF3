<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 */
class Article
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     max = 50,
     *     maxMessage = "Le titre ne doit pas faire plus de 50 caractères"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min = 10,
     *     minMessage = "Le contenu doit faire au moins 10 caractères"
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_publi;

    /**
     * on indique à doctrine que cette propriété fait référence à
     * l'entité User et qu'il s'agit d'une relation ManyToOne
     *
     * on rajoute inversedBy
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDatePubli(): ?\DateTimeInterface
    {
        return $this->date_publi;
    }

    public function setDatePubli(?\DateTimeInterface $date_publi): self
    {
        $this->date_publi = $date_publi;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
