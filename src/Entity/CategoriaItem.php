<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoriaItemRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=CategoriaItemRepository::class)
 */
class CategoriaItem extends JsonSerializableEntity
{
    public function jsonSerialize()
    {
        // $this->fillSituacaoDescritivo();
        $array = parent::jsonSerialize();
        $array['id'] = $this->getId();
        $array['categoria'] = $this->getCategoria();
        return $array;
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
    protected $categoria;

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
     * @ORM\OneToMany(targetEntity=InboxItem::class, mappedBy="usuario")
     */
    private $inboxItems;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="categoriaItems")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;
    
    public function __construct()
    {
        $this->inboxItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): self
    {
        $this->categoria = $categoria;

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
    
    /**
     * @return Collection<int, InboxItem>
     */
    public function getInboxItems(): Collection
    {
        return $this->inboxItems;
    }

    public function addInboxItem(InboxItem $inboxItem): self
    {
        if (!$this->inboxItems->contains($inboxItem)) {
            $this->inboxItems[] = $inboxItem;
            $inboxItem->setCategoriaItem($this);
        }

        return $this;
    }

    public function removeInboxItem(InboxItem $inboxItem): self
    {
        if ($this->inboxItems->removeElement($inboxItem)) {
            // set the owning side to null (unless already changed)
            if ($inboxItem->getCategoriaItem() === $this) {
                $inboxItem->setCategoriaItem(null);
            }
        }

        return $this;
    }
}
