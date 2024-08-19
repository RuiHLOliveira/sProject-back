<?php

namespace App\Entity;

use App\Entity\CategoriaItem;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InboxItemRepository;

/**
 * @ORM\Entity(repositoryClass=InboxItemRepository::class)
 */
class InboxItem extends JsonSerializableEntity
{
    const ORIGEM_WEB = 1;
    const ORIGEM_YOUTUBE = 2;
    const ORIGEM_INSTAGRAM = 3;

    const ORIGENS = [
        '0' => '-',
        self::ORIGEM_WEB => 'Web',
        self::ORIGEM_YOUTUBE => 'Youtube',
        self::ORIGEM_INSTAGRAM => 'Instagram',
    ];

    public function jsonSerialize()
    {
        // $this->fillSituacaoDescritivo();
        $array = parent::jsonSerialize();
        $array['nome'] = $this->getNome();
        $array['link'] = $this->getLink();
        $array['origem'] = $this->getOrigem();
        $array['categoriaItem'] = $this->getCategoriaItem();
        $array['origemDescritivo'] = self::ORIGENS[$this->getOrigem()];
        $array['acao'] = $this->getAcao();
        $array['dict_origens'] = self::ORIGENS;
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
    protected $nome;

    /**
     * @ORM\Column(type="text")
     */
    protected $link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $origem;

    /**
     * @ORM\Column(type="text")
     */
    protected $acao;
    
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
     * @ORM\ManyToOne(targetEntity=CategoriaItem::class, inversedBy="inboxItems")
     * @ORM\JoinColumn(nullable=true)
     */
    protected $categoriaItem;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="inboxItems")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

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

    public function getOrigem(): ?string
    {
        return $this->origem;
    }

    public function setOrigem(string $origem): self
    {
        $this->origem = $origem;

        return $this;
    }

    public function getAcao(): ?string
    {
        return $this->acao;
    }

    public function setAcao(string $acao): self
    {
        $this->acao = $acao;

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

    public function getCategoriaItem(): ?CategoriaItem
    {
        return $this->categoriaItem;
    }

    public function setCategoriaItem(?CategoriaItem $categoriaItem): self
    {
        $this->categoriaItem = $categoriaItem;

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
}
