<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use JsonSerializable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\HabitoRepository;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass=HabitoRepository::class)
 */
class Habito extends JsonSerializableEntity
{

    const SITUACAO_PENDENTE = 0;
    const SITUACAO_CONCLUIDO = 1;
    const SITUACAO_FALHA = 2;

    private $serializarHabitoRealizados;
    private $habitoRealizadosArray;

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
        $array['motivo'] = $this->getMotivo();
        $array['situacao'] = $this->getSituacao();
        $array['situacaoDescritivo'] = $this->getSituacaoDescritivo();
        $array['hora'] = $this->getHora() != null ? $this->getHora()->format('H:i') : null;
        $array['habitoRealizados'] = $this->habitoRealizadosArray;
        return $array;
    }

    /**
     * Marca as tarefas deste Projeto para serem serializadas
     */
    public function serializarHabitoRealizados(){
        $this->serializarHabitoRealizados = true;
        $this->habitoRealizadosToArray();
    }

    public function habitoRealizadosToArray() {
        $collection = $this->getHabitoRealizados();
        $collection = $collection->toArray();
        $this->habitoRealizadosArray = $collection;
    }

    public function fillSituacaoDescritivo(){
        $this->setSituacaoDescritivo(self::DESCRITIVOS_SITUACAO[$this->getSituacao()]);
    }

    public function concluir() {
        if($this->situacao !== self::SITUACAO_PENDENTE)
            throw new Exception("Não é possível concluir uma habito que não está pendente.");
        $this->situacao = self::SITUACAO_CONCLUIDO;
        return $this;
    }

    public function falhar() {
        if($this->situacao !== self::SITUACAO_PENDENTE)
            throw new Exception("Não é possível concluir uma habito que não está pendente.");
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $motivo;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    protected $hora;

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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="habitos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $usuario;

    /**
     * @ORM\Column(type="integer")
     */
    protected $situacao;

    protected $situacaoDescritivo;

    /**
     * @ORM\OneToMany(targetEntity=HabitoRealizado::class, mappedBy="habito")
     */
    private $habitoRealizados;

    public function __construct()
    {
        $this->habitoRealizados = new ArrayCollection();
    }

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

    public function getMotivo(): ?string
    {
        return $this->motivo;
    }

    public function setMotivo(string $motivo): self
    {
        $this->motivo = $motivo;

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

    /**
     * @return Collection<int, HabitoRealizado>
     */
    public function getHabitoRealizados(): Collection
    {
        return $this->habitoRealizados;
    }

    public function addHabitoRealizado(HabitoRealizado $habitoRealizado): self
    {
        if (!$this->habitoRealizados->contains($habitoRealizado)) {
            $this->habitoRealizados[] = $habitoRealizado;
            $habitoRealizado->setHabito($this);
        }

        return $this;
    }

    public function removeHabitoRealizado(HabitoRealizado $habitoRealizado): self
    {
        if ($this->habitoRealizados->removeElement($habitoRealizado)) {
            // set the owning side to null (unless already changed)
            if ($habitoRealizado->getHabito() === $this) {
                $habitoRealizado->setHabito(null);
            }
        }

        return $this;
    }

}
