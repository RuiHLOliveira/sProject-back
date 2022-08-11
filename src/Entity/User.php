<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Hora::class, mappedBy="usuario")
     */
    private $horas;

    /**
     * @ORM\OneToMany(targetEntity=Dia::class, mappedBy="usuario")
     */
    private $dias;

    /**
     * @ORM\OneToMany(targetEntity=Atividade::class, mappedBy="usuario")
     */
    private $atividades;

    public function __construct()
    {
        $this->horas = new ArrayCollection();
        $this->dias = new ArrayCollection();
        $this->atividades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Hora>
     */
    public function getHoras(): Collection
    {
        return $this->horas;
    }

    public function addHora(Hora $hora): self
    {
        if (!$this->horas->contains($hora)) {
            $this->horas[] = $hora;
            $hora->setUsuario($this);
        }

        return $this;
    }

    public function removeHora(Hora $hora): self
    {
        if ($this->horas->removeElement($hora)) {
            // set the owning side to null (unless already changed)
            if ($hora->getUsuario() === $this) {
                $hora->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dia>
     */
    public function getDias(): Collection
    {
        return $this->dias;
    }

    public function addDia(Dia $dia): self
    {
        if (!$this->dias->contains($dia)) {
            $this->dias[] = $dia;
            $dia->setUsuario($this);
        }

        return $this;
    }

    public function removeDia(Dia $dia): self
    {
        if ($this->dias->removeElement($dia)) {
            // set the owning side to null (unless already changed)
            if ($dia->getUsuario() === $this) {
                $dia->setUsuario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Atividade>
     */
    public function getAtividades(): Collection
    {
        return $this->atividades;
    }

    public function addAtividade(Atividade $atividade): self
    {
        if (!$this->atividades->contains($atividade)) {
            $this->atividades[] = $atividade;
            $atividade->setUsuario($this);
        }

        return $this;
    }

    public function removeAtividade(Atividade $atividade): self
    {
        if ($this->atividades->removeElement($atividade)) {
            // set the owning side to null (unless already changed)
            if ($atividade->getUsuario() === $this) {
                $atividade->setUsuario(null);
            }
        }

        return $this;
    }
}
