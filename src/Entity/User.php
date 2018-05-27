<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $password;

    /**
     * @param mixed
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * //on indique a doctrine la relation OneToMany
     * //orphanRemoval=true permet de dire a doctrine de supprimer définitivement l'article s'il n'a plus d'auteur
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="user", orphanRemoval=true)
     */
    private $articles;

    public function __construct()
    {
        $this->isActive = true; //par défault un user est actif

        $this->articles = new ArrayCollection();
    }

    /**
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function getSalt()
    {
        //on va utiliser l'encoder bCrypt qui gère lui même le salt
        //on utilisera donc pas de propriété salt
        //on est quand même obligé d'écrire cette méthode à cause de l'interface UserInterface que l'on implémente
        return null;
    }

    public function getId()
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials()
    {
        //par mesure de sécurité, on effaçe le mot de passe en clair
        $this->plainPassword = null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /*
     * Permet d'ajouter un article en le liant à l'utilisateur
     */
    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {

            //on rajoute l'article s'il n'est pas déjà présent
            $this->articles[] = $article;

            //Important : on met à jour l'objet Article en lui donnant un auteur
            $article->setUser($this);

        }

        return $this;
    }

    /*
     * Permet de supprimer un article depuis l'objet User
     * Comme on ne souhaite pas conserver un article sans auteur, on a spécifié
     * dans les annotations de la propriété article orphanRemoval=true pour que doctrine supprimme l'article
     * Si l'on  souhaite simplement conserver l'article sans auteur, il faut modifier l'entité article (propriété user)
     * ainsi que cette méthode
     */
    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {

            //si l'article est bien lié à cet utilisateur
            //on l'enlève de la liste des articles de cet utilisateur
            $this->articles->removeElement($article);

            //a décommenter si l'on souhaite pouvoir conserver l'article sans auteur
            /*if ($article->getUser() === $this) {
                $article->setUser(null);
            }*/

        }

        return $this;
    }
}
