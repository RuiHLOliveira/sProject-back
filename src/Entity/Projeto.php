<?php

namespace App\Entity;

use App\Repository\ProjetoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass=ProjetoRepository::class)
 */
class Projeto implements JsonSerializable
{
    public function jsonSerialize()
    {
        // $array = parent::jsonSerialize();
        $this->fillSituacaoDescritivo();
        $this->fillPrioridadeDescritivo();
        $array = [
            'id' => $this->getId(),
            'nome' => $this->getNome(),
            'anotacoes' => $this->getAnotacoes(),
            'fixado' => $this->getFixado(),
            'situacao' => $this->getSituacao(),
            'situacaoDescritivo' => $this->getSituacaoDescritivo(),
            'prioridade' => $this->getPrioridade(),
            'prioridadeDescritivo' => $this->getPrioridadeDescritivo(),
            'dataPrazo' => $this->getDataPrazo() != null ? $this->getDataPrazo()->format('Y-m-d H:i:s') : '',
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'deletedAt' => $this->getDeletedAt(),
        ];
        if($this->serializarTarefas) {
            $array['tarefas'] = $this->tarefasArray;
        }
        return $array;
    }
    
    const SITUACAO_PENDENTE = 1;
    const SITUACAO_AGUARDANDO_RESPOSTA = 2;
    const SITUACAO_PAUSADO_INDEFINIDAMENTE = 3;
    const SITUACAO_CONCLUIDO = 4;

    const DESCRITIVOS_SITUACAO = [
        self::SITUACAO_PENDENTE => 'pendente',
        self::SITUACAO_AGUARDANDO_RESPOSTA => 'espera',
        self::SITUACAO_PAUSADO_INDEFINIDAMENTE => 'suspenso',
        self::SITUACAO_CONCLUIDO => 'concluida',
    ];

    const PRIORIDADE_URGENTE = 1;
    const PRIORIDADE_ALTA = 2;
    const PRIORIDADE_MEDIA = 3;
    const PRIORIDADE_BAIXA = 4;
    const PRIORIDADE_BAIXISSIMA = 5;

    const DESCRITIVOS_PRIORIDADE = [
        self::PRIORIDADE_URGENTE => 'Urgente',
        self::PRIORIDADE_ALTA => 'Alta',
        self::PRIORIDADE_MEDIA => 'Média',
        self::PRIORIDADE_BAIXA => 'Baixa',
        self::PRIORIDADE_BAIXISSIMA => 'Baixíssima',
    ];

    const LISTA_PRIORIDADES = [
        self::PRIORIDADE_URGENTE,
        self::PRIORIDADE_ALTA,
        self::PRIORIDADE_MEDIA,
        self::PRIORIDADE_BAIXA,
        self::PRIORIDADE_BAIXISSIMA,
    ];

    private $serializarTarefas;
    private $tarefasArray;

    /**
     * Marca as tarefas deste Projeto para serem serializadas
     */
    public function serializarTarefas(){
        $this->serializarTarefas = true;
        $this->tarefasToArray();
    }
    
    public function tarefasToArray() {
        $collection = $this->getTarefas();
        $collection = $collection->toArray();
        $this->tarefasArray = $collection;
    }
    
    public function fillSituacaoDescritivo(){
        if($this->getSituacao() != null && $this->getSituacao() > 0)
            $this->setSituacaoDescritivo(self::DESCRITIVOS_SITUACAO[$this->getSituacao()]);
    }
    public function fillPrioridadeDescritivo(){
        if($this->getPrioridade() != null && $this->getPrioridade() > 0)
            $this->setPrioridadeDescritivo(self::DESCRITIVOS_PRIORIDADE[$this->getPrioridade()]);
    }


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dataPrazo;

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
     * @ORM\OneToMany(targetEntity=Tarefa::class, mappedBy="projeto")
     */
    private $tarefas;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @ORM\Column(type="text")
     */
    private $anotacoes;

    /**
     * @ORM\Column(type="smallint", options={"default":0})
     */
    protected $situacao;

    protected $situacaoDescritivo;

    /**
     * @ORM\Column(type="smallint", options={"default":5})
     */
    private $prioridade;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $fixado;

    protected $prioridadeDescritivo;
    
    public function __construct()
    {
        // $this->horas = new ArrayCollection();
        $this->tarefas = new ArrayCollection();
        $this->fillSituacaoDescritivo();
        $this->fillPrioridadeDescritivo();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataPrazo(): ?\DateTimeInterface
    {
        return $this->dataPrazo;
    }

    public function setDataPrazo(\DateTimeInterface $dataPrazo): self
    {
        $this->dataPrazo = $dataPrazo;

        return $this;
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
     * @return Collection<int, Tarefa>
     */
    public function getTarefas(): Collection
    {
        return $this->tarefas;
    }

    public function addTarefa(Tarefa $tarefa): self
    {
        if (!$this->tarefas->contains($tarefa)) {
            $this->tarefas[] = $tarefa;
            $tarefa->setProjeto($this);
        }

        return $this;
    }

    public function removeTarefa(Tarefa $tarefa): self
    {
        if ($this->tarefas->removeElement($tarefa)) {
            // set the owning side to null (unless already changed)
            if ($tarefa->getProjeto() === $this) {
                $tarefa->setProjeto(null);
            }
        }

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

    public function getPrioridade(): ?int
    {
        return $this->prioridade;
    }

    public function setPrioridade(int $prioridade): self
    {
        $this->prioridade = $prioridade;
        $this->fillPrioridadeDescritivo();

        return $this;
    }
    
    public function getPrioridadeDescritivo(): ?string
    {
        return $this->prioridadeDescritivo;
    }

    public function setPrioridadeDescritivo(string $prioridadeDescritivo): self
    {
        $this->prioridadeDescritivo = $prioridadeDescritivo;
        return $this;
    }
    
    public function getFixado(): ?bool
    {
        return $this->fixado;
    }

    public function setFixado(bool $fixado): self
    {
        $this->fixado = $fixado;
        return $this;
    }
}
