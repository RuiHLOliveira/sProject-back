<?php

namespace App\Entity;

use DateTime;
use Exception;
use JsonSerializable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProjetofotoRepository;

/**
 * @ORM\Entity(repositoryClass=ProjetofotoRepository::class)
 */
class Projetofoto extends JsonSerializableEntity
{
    public function jsonSerialize()
    {
        $array = parent::jsonSerialize();
        $array['descricao'] = $this->getDescricao();
        $array['link'] = $this->getLink();

        if(!$this->serializarProjeto) {
            $array['projeto'] = $this->getProjeto()->getId();
        } else if($this->serializarProjeto) {
            $array['projeto'] = $this->getProjeto();
        }

        return $array;
    }

    protected $serializarProjeto;

    public function serializarProjeto() {
        // $this->getProjeto();
        $this->serializarProjeto = true;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $descricao;

    /**
     * @ORM\Column(type="text")
     */
    protected $link;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    protected $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $updated_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $deleted_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projetosfotos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Projeto::class, inversedBy="projetosfotos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $projeto;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getProjeto(): ?Projeto
    {
        return $this->projeto;
    }

    public function setProjeto(?Projeto $projeto): self
    {
        $this->projeto = $projeto;

        return $this;
    }
}
