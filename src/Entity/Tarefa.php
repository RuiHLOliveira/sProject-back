<?php

namespace App\Entity;

use DateTime;
use Exception;
use JsonSerializable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TarefaRepository;

/**
 * @ORM\Entity(repositoryClass=TarefaRepository::class)
 */
class Tarefa extends JsonSerializableEntity
{
    const SITUACAO_PENDENTE = 0;
    const SITUACAO_CONCLUIDO = 1;
    const SITUACAO_FALHA = 2;

    const DESCRITIVOS_SITUACAO = [
        self::SITUACAO_PENDENTE => 'pendente',
        self::SITUACAO_CONCLUIDO => 'concluida',
        self::SITUACAO_FALHA => 'falhou',
    ];

    public function jsonSerialize()
    {
        $this->fillSituacaoDescritivo();
        $array = parent::jsonSerialize();
        $array['descricao'] = $this->getDescricao();
        $array['situacao'] = $this->getSituacao();
        $array['situacaoDescritivo'] = $this->getSituacaoDescritivo();
        $array['hora'] = $this->getHora() != null ? $this->getHora()->format('H:i') : null;
        $array['meuDia'] = $this->getMeuDia() != null ? $this->getMeuDia()->format('Y-m-d H:i:s') : null;
        $array['meuDiaObj'] = $this->getMeuDia();

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

    public function fillSituacaoDescritivo(){
        $this->setSituacaoDescritivo(self::DESCRITIVOS_SITUACAO[$this->getSituacao()]);
    }

    public function adicionarAoMeuDia() {
        // if($this->situacao !== self::SITUACAO_PENDENTE)
        //     throw new Exception("Não é possível concluir uma tarefa que não está pendente.");
        $this->meuDia = new DateTimeImmutable();
        return $this;
    }
    
    public function removerMeuDia() {
        // if($this->situacao !== self::SITUACAO_PENDENTE)
        //     throw new Exception("Não é possível concluir uma tarefa que não está pendente.");
        $this->meuDia = null;
        return $this;
    }

    public function concluir() {
        if($this->situacao !== self::SITUACAO_PENDENTE)
            throw new Exception("Não é possível concluir uma tarefa que não está pendente.");
        $this->situacao = self::SITUACAO_CONCLUIDO;
        return $this;
    }

    public function falhar() {
        if($this->situacao !== self::SITUACAO_PENDENTE)
            throw new Exception("Não é possível concluir uma tarefa que não está pendente.");
        $this->situacao = self::SITUACAO_FALHA;
        return $this;
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
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $hora;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $meuDia;

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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tarefas")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;

    /**
     * @ORM\Column(type="integer")
     */
    protected $situacao;

    protected $situacaoDescritivo;

    /**
     * @ORM\ManyToOne(targetEntity=Projeto::class, inversedBy="tarefas")
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

    public function getHora(): ?DateTimeImmutable
    {
        return $this->hora;
    }

    public function setHora(?DateTimeImmutable $hora): self
    {
        $this->hora = $hora;

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

    public function getProjeto(): ?Projeto
    {
        return $this->projeto;
    }

    public function setProjeto(?Projeto $projeto): self
    {
        $this->projeto = $projeto;

        return $this;
    }

    /**
     * Get the value of meuDia
     */
    public function getMeuDia()
    {
        return $this->meuDia;
    }

    /**
     * Set the value of meuDia
     */
    public function setMeuDia($meuDia): self
    {
        $this->meuDia = $meuDia;

        return $this;
    }
}
