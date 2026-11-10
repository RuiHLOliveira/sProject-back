<?php

namespace App\Entity;

use App\Entity\Projeto;
use App\Repository\ObjetivoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=ObjetivoRepository::class)
 */
class Objetivo implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $this->fillSituacaoDescritivo();
        $this->fillHorizontesDescritivo();
        $array = [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'anotacoes' => $this->getAnotacoes(),
            'respostas' => $this->getRespostas(),
            'horizonte' => $this->getHorizonte(),
            'horizonteDescritivo' => $this->getHorizonteDescritivo(),
            'fotos' => $this->getFotos() ?? '[]',
            'situacao' => $this->getSituacao(),
            'situacaoDescritivo' => $this->getSituacaoDescritivo(),
            'area' => $this->getArea(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'deletedAt' => $this->getDeletedAt(),
        ];
        return $array;
    }
    
    const SITUACAO_PENDENTE = 1;
    const SITUACAO_CONCLUIDO = 2;

    const DESCRITIVOS_SITUACAO = [
        self::SITUACAO_PENDENTE => 'pendente',
        self::SITUACAO_CONCLUIDO => 'concluida',
    ];

    const HORIZONTE_2 = 2;
    const HORIZONTE_3 = 3;
    const HORIZONTE_4 = 4;
    const HORIZONTE_5 = 5;

    const LISTA_HORIZONTES = [
        self::HORIZONTE_2,
        self::HORIZONTE_3,
        self::HORIZONTE_4,
        self::HORIZONTE_5,
    ];
    
    const DESCRITIVOS_HORIZONTES = [
        self::HORIZONTE_2 => "Horizonte 2: Trimestral",
        self::HORIZONTE_3 => "Horizonte 3: Anual",
        self::HORIZONTE_4 => "Horizonte 4: 5 Anos",
        self::HORIZONTE_5 => "Horizonte 5: Vida",
    ];

    public function fillSituacaoDescritivo(){
        if($this->getSituacao() != null && $this->getSituacao() > 0)
            $this->setSituacaoDescritivo(self::DESCRITIVOS_SITUACAO[$this->getSituacao()]);
    }
    public function fillHorizontesDescritivo(){
        if($this->getHorizonte() != null && $this->getHorizonte() > 1)
            $this->setHorizonteDescritivo(self::DESCRITIVOS_HORIZONTES[$this->getHorizonte()]);
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="projetos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuario;

    /**
     * @ORM\ManyToOne(targetEntity=Area::class, inversedBy="objetivos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $area;

    /**
     * @ORM\OneToMany(targetEntity=Projeto::class, mappedBy="projeto")
     */
    private $projetos;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="text")
     */
    private $anotacoes;

    /**
     * @ORM\Column(type="json",  nullable=true, options={"default":null})
     */
    private $respostas;

    /**
     * @ORM\Column(type="json",  nullable=true, options={"default":null})
     */
    private $fotos;

    /**
     * @ORM\Column(type="smallint", options={"default":0})
     */
    protected $situacao;
    protected $situacaoDescritivo;

    /**
     * @ORM\Column(type="smallint", options={"default":0})
     */
    protected $horizonte;
    protected $horizonteDescritivo;

    
    public function __construct()
    {
        // $this->horas = new ArrayCollection();
        $this->projetos = new ArrayCollection();
        $this->fillSituacaoDescritivo();
        $this->fillHorizontesDescritivo();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
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

    /**
     * @return Collection<int, Projeto>
     */
    public function getProjetos(): Collection
    {
        return $this->projetos;
    }

    public function addProjeto(Projeto $projeto): self
    {
        if (!$this->projetos->contains($projeto)) {
            $this->projetos[] = $projeto;
            $projeto->setObjetivo($this);
        }
        return $this;
    }

    public function removeProjeto(Projeto $projeto): self
    {
        if ($this->projetos->removeElement($projeto)) {
            if ($projeto->getObjetivo() === $this) {
                $projeto->setObjetivo(null);
            }
        }
        return $this;
    }

    public function getArea(): ?Area
    {
        return $this->area;
    }

    public function setArea(?Area $area): self
    {
        $this->area = $area;
        return $this;
    }
    
    public function getFotos(): ?string
    {
        return $this->fotos;
    }

    public function setFotos($fotos): self
    {
        $this->fotos = $fotos;
        return $this;
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

    public function getAnotacoes(): ?string
    {
        return $this->anotacoes;
    }

    public function setAnotacoes(string $anotacoes): self
    {
        $this->anotacoes = $anotacoes;

        return $this;
    }
    
    public function getSituacao(): ?int
    {
        return $this->situacao;
    }

    public function setSituacao(int $situacao): self
    {
        $this->situacao = $situacao;
        $this->fillSituacaoDescritivo();

        return $this;
    }

    public function getSituacaoDescritivo(): ?string
    {
        return $this->situacaoDescritivo;
    }

    public function setSituacaoDescritivo(string $situacaoDescritivo): self
    {
        $this->situacaoDescritivo = $situacaoDescritivo;
        return $this;
    }

    
    public function getHorizonte(): ?int
    {
        return $this->horizonte;
    }

    public function setHorizonte(int $horizonte): self
    {
        $this->horizonte = $horizonte;
        $this->fillHorizontesDescritivo();

        return $this;
    }

    public function getHorizonteDescritivo(): ?string
    {
        return $this->horizonteDescritivo;
    }

    public function setHorizonteDescritivo(string $horizonteDescritivo): self
    {
        $this->horizonteDescritivo = $horizonteDescritivo;
        return $this;
    }

    public function getRespostas(): ?string
    {
        return $this->respostas;
    }

    public function setRespostas(string $respostas): self
    {
        $this->respostas = $respostas;
        return $this;
    }

}
