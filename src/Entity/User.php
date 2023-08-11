<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Article::class)]
    private Collection $articles;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?bool $gpdr = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->created_at =  new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER && ROLE_AUTHOR (Author to be able to add a post)
        $roles[] = 'ROLE_USER';
        $roles[] = 'ROLE_AUTHOR';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setUser($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getUser() === $this) {
                $article->setUser(null);
            }
        }

        return $this;
    }

        // Convertir l'objet en châine de caractères 
        public function __toString() {
            return $this->email;
        }

        public function getLastname(): ?string
        {
            return $this->lastname;
        }

        public function setLastname(string $lastname): static
        {
            $this->lastname = $lastname;

            return $this;
        }

        public function getFirstname(): ?string
        {
            return $this->firstname;
        }

        public function setFirstname(string $firstname): static
        {
            $this->firstname = $firstname;

            return $this;
        }

        public function getAvatar(): ?string
        {
            return $this->avatar;
        }

        public function setAvatar(string $avatar): static
        {
            $this->avatar = $avatar;

            return $this;
        }

        public function isGpdr(): ?bool
        {
            return $this->gpdr;
        }

        public function setGpdr(bool $gpdr): static
        {
            $this->gpdr = $gpdr;

            return $this;
        }

        public function getCreatedAt(): ?\DateTimeImmutable
        {
            return $this->created_at;
        }

        public function setCreatedAt(\DateTimeImmutable $created_at): static
        {
            $this->created_at = $created_at;

            return $this;
        }
        

}
