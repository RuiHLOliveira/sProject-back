<?php

namespace App\Entity;

use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use App\Repository\RecompensaRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=RecompensaRepository::class)
 */
class Recompensa implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $array = [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'createdat' => $this->getCreatedat(),
            'updatedat' => $this->getUpdatedat(),
            'deletedat' => $this->getDeletedat(),
        ];
        if($this->serializarRecompensasacoes){
            $array['recompensaacoes'] = $this->getRecompensaacoes();
        }
        return $array;
    }

    private $serializarRecompensasacoes;

    public function __construct()
    {
        $this->recompensaacoes = new ArrayCollection();
        $this->serializarRecompensasacoes = false;
    }

    public function serializarRecompensasAcoes() {
        $this->serializarRecompensasacoes = true;
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdat;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedat;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\OneToMany(targetEntity=Recompensaacao::class, mappedBy="recompensa")
     */
    private $recompensaacoes;

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    public function setRecompensaacoes($recompensaacoes): Recompensa
    {
        $this->recompensaacoes = $recompensaacoes;
        return $this;
    }

    public function getRecompensaacoes() {
        return $this->recompensaacoes;
    }

    public function addRecompensaaco(Recompensaacao $recompensaaco): self
    {
        if (!$this->recompensaacoes->contains($recompensaaco)) {
            $this->recompensaacoes[] = $recompensaaco;
            $recompensaaco->setRecompensa($this);
        }

        return $this;
    }

    public function removeRecompensaaco(Recompensaacao $recompensaaco): self
    {
        if ($this->recompensaacoes->removeElement($recompensaaco)) {
            // set the owning side to null (unless already changed)
            if ($recompensaaco->getRecompensa() === $this) {
                $recompensaaco->setRecompensa(null);
            }
        }

        return $this;
    }
    /**
     * Get the value of createdat
     */ 
    public function getCreatedat()
    {
        return $this->createdat;
    }

    /**
     * Set the value of createdat
     *
     * @return  self
     */ 
    public function setCreatedat($createdat)
    {
        $this->createdat = $createdat;

        return $this;
    }

    /**
     * Get the value of updatedat
     */ 
    public function getUpdatedat()
    {
        return $this->updatedat;
    }

    /**
     * Set the value of updatedat
     *
     * @return  self
     */ 
    public function setUpdatedat($updatedat)
    {
        $this->updatedat = $updatedat;

        return $this;
    }

    /**
     * Get the value of deleted_at
     */
    public function getDeletedat()
    {
        return $this->deletedat;
    }

    /**
     * Set the value of deletedat
     */
    public function setDeletedat($deletedat): self
    {
        $this->deletedat = $deletedat;

        return $this;
    }
}
