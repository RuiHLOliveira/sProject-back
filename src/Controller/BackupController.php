<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Dia;
use App\Entity\Note;
use App\Entity\Habito;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Notebook;
use App\Entity\Atividade;
use App\Entity\CategoriaItem;
use App\Entity\Historico;
use App\Entity\InboxItem;
use App\Entity\HabitoRealizado;
use App\Service\BackupService;
use App\Service\CategoriaItemService;
use App\Service\HabitosService;
use App\Service\ProjetosService;
use App\Service\InboxItemService;
use App\Service\HistoricosService;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BackupController extends AbstractController
{

    /**
     * @var ProjetosService $projetosService
     */
    private $projetosService;

    /**
     * @var HabitosService $habitosService
     */
    private $habitosService;

    /**
     * @var InboxItemService $inboxItemService
     */
    private $inboxItemService;

    /**
     * @var HistoricosService $historicosService
     */
    private $historicosService;

    /**
     * @var CategoriaItemService $categoriaItemsService
     */
    private $categoriaItemsService;

    /**
     * @var BackupService $backupService
     */
    private $backupService;

    public function __construct(
        ProjetosService $projetosService,
        HistoricosService $historicosService,
        HabitosService $habitosService,
        InboxItemService $inboxItemService,
        CategoriaItemService $categoriaItemsService,
        BackupService $backupService
    ) {
        $this->projetosService = $projetosService;
        $this->historicosService = $historicosService;
        $this->habitosService = $habitosService;
        $this->inboxItemService = $inboxItemService;
        $this->categoriaItemsService = $categoriaItemsService;
        $this->backupService = $backupService;
    }

    
    /**
     * @Route("/backup/exportSqlBkpInsert", name="exportSqlBkpInsert")
     */
    public function exportSqlBkpInsert(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            if(!strstr($usuario->getEmail(),'ruigx')){
                throw new LogicException("Usuario não suportado!");
            }
            $arquivoSql = $this->backupService->runBackup();
            return new JsonResponse(compact('arquivoSql'), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @Route("/backup/exportProjetosTxt", name="backupExportProjetosTxt")
     */
    public function exportProjetosTxt(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $arquivoTxt = '';
            $projetos = $this->projetosService->findAll($usuario, [], ['situacao'=>'asc','prioridade' => 'asc']);
            $habitos = $this->habitosService->findAll($usuario, [], []);
            $inboxItems = $this->inboxItemService->findAll($usuario, [], []);
            /**
             * @var Projeto $projeto
             */
            foreach($projetos as $key => $projeto) {
                $dataPrazo = !is_null($projeto->getDataPrazo()) ? $projeto->getDataPrazo()->format('d/m/Y H:i:s') : '-';
                $createdAt = !is_null($projeto->getCreatedAt()) ? $projeto->getCreatedAt()->format('d/m/Y H:i:s') : '-';
                $updatedAt = !is_null($projeto->getUpdatedAt()) ? $projeto->getUpdatedAt()->format('d/m/Y H:i:s') : '-';
                $deletedAt = !is_null($projeto->getDeletedAt()) ? $projeto->getDeletedAt()->format('d/m/Y H:i:s') : '-';
                $projeto->fillSituacaoDescritivo();
                $projeto->fillPrioridadeDescritivo();

                $arquivoTxt .= "Projeto: ".$projeto->getNome()."\n";
                $arquivoTxt .= "Anotações: ".$projeto->getAnotacoes()."\n";
                $arquivoTxt .= "Situacao: ".$projeto->getSituacao().'-'.$projeto->getSituacaoDescritivo()."\n";
                $arquivoTxt .= "Prioridade: ".$projeto->getPrioridade().'-'.$projeto->getPrioridadeDescritivo()."\n";
                $arquivoTxt .= "Data Prazo: ".$dataPrazo."\n";
                $arquivoTxt .= "Criado em: ".$createdAt."\n";
                $arquivoTxt .= "Ultima att: ".$updatedAt."\n";
                $arquivoTxt .= "*******************\n";
                $arquivoTxt .= "Tarefas: \n";
                /**
                 * @var Tarefa $tarefa
                 */
                foreach ($projeto->getTarefas() as $key => $tarefa) {
                    if($key > 0 && $key < count($projeto->getTarefas())) $arquivoTxt .= "-------------\n";
                    $hora = !is_null($tarefa->getHora()) ? $tarefa->getHora()->format('d/m/Y H:i:s') : '-';
                    $tarefa->fillSituacaoDescritivo();
                    $arquivoTxt .= "Descricao: ".$tarefa->getDescricao()."\n";
                    $arquivoTxt .= "Motivo: ".$tarefa->getMotivo()."\n";
                    $arquivoTxt .= "Situacao: ".$tarefa->getSituacao().'-'.$tarefa->getSituacaoDescritivo()."\n";
                    $arquivoTxt .= "Hora: ".$hora."\n";
                    // $arquivoTxt .= "CreatedAt : ".$createdAt."\n";
                    // $arquivoTxt .= "UpdatedAt : ".$updatedAt."\n";
                    // $arquivoTxt .= "DeletedAt : ".$deletedAt."\n";
                }

                $historicos = $this->historicosService->findAll($usuario, ['moduloId'=>$projeto->getId()], ['createdAt'=>'desc']);
                $arquivoTxt .= "*******************\n";
                $arquivoTxt .= "Historicos: \n";
                /**
                 * @var Historico $historico
                 */
                foreach ($historicos as $key => $historico) {
                    if($key > 0 && $key < count($historicos)) $arquivoTxt .= "-------------\n";
                    $createdAt = !is_null($historico->getCreatedAt()) ? $historico->getCreatedAt()->format('d/m/Y H:i:s') : '-';
                    $arquivoTxt .= "Descricao: ".$historico->getDescricao()."\n";
                    $arquivoTxt .= "Hora: ".$createdAt."\n";
                    // $arquivoTxt .= "CreatedAt : ".$createdAt."\n";
                    // $arquivoTxt .= "UpdatedAt : ".$updatedAt."\n";
                    // $arquivoTxt .= "DeletedAt : ".$deletedAt."\n";
                }
                $arquivoTxt .= "*******************\n";
                $arquivoTxt .= "\n\n\n";
            }

            /**
             * @var Habito $habito
             */
            foreach($habitos as $key => $habito) {
                $hora = !is_null($habito->getHora()) ? $habito->getHora()->format('H:i') : '-';
                $createdAt = !is_null($habito->getCreatedAt()) ? $habito->getCreatedAt()->format('d/m/Y H:i:s') : '-';
                $updatedAt = !is_null($habito->getUpdatedAt()) ? $habito->getUpdatedAt()->format('d/m/Y H:i:s') : '-';
                $deletedAt = !is_null($habito->getDeletedAt()) ? $habito->getDeletedAt()->format('d/m/Y H:i:s') : '-';
                $arquivoTxt .= "Habito: ".$habito->getDescricao()."\n";
                $arquivoTxt .= "Motivo: ".$habito->getMotivo()."\n";
                $arquivoTxt .= "Hora: ".$hora."\n";
                $arquivoTxt .= "Criado em: ".$createdAt."\n";
                $arquivoTxt .= "Ultima att: ".$updatedAt."\n";
                $arquivoTxt .= "*******************\n";
                $arquivoTxt .= "Realizado Em: \n";
                /**
                 * @var HabitoRealizado $habitoRealizado
                 */
                foreach ($habito->getHabitoRealizados() as $key => $habitoRealizado) {
                    if($key > 0 && $key < count($habito->getHabitoRealizados())) $arquivoTxt .= "-------------\n";
                    $realizadoEm = $habitoRealizado->getRealizadoEm()->format('d/m/Y H:i:s');
                    $arquivoTxt .= "dia: ".$realizadoEm."\n";
                    // $arquivoTxt .= "CreatedAt : ".$createdAt."\n";
                    // $arquivoTxt .= "UpdatedAt : ".$updatedAt."\n";
                    // $arquivoTxt .= "DeletedAt : ".$deletedAt."\n";
                }
                $arquivoTxt .= "*******************\n";
                $arquivoTxt .= "\n\n\n";
            }

            
            /**
             * @var InboxItem $inboxItem
             */
            foreach($inboxItems as $key => $inboxItem) {
                $createdAt = !is_null($inboxItem->getCreatedAt()) ? $inboxItem->getCreatedAt()->format('d/m/Y H:i:s') : '-';
                $updatedAt = !is_null($inboxItem->getUpdatedAt()) ? $inboxItem->getUpdatedAt()->format('d/m/Y H:i:s') : '-';
                $deletedAt = !is_null($inboxItem->getDeletedAt()) ? $inboxItem->getDeletedAt()->format('d/m/Y H:i:s') : '-';
                $arquivoTxt .= "Nome: ".$inboxItem->getNome()."\n";
                $arquivoTxt .= "Ação: ".$inboxItem->getAcao()."\n";
                $arquivoTxt .= "Link: ".$inboxItem->getLink()."\n";
                $arquivoTxt .= "Categoria: ";
                $arquivoTxt .= !is_null($inboxItem->getCategoriaItem()) ? $inboxItem->getCategoriaItem()->getCategoria() : '-';
                $arquivoTxt .= "\n";
                $arquivoTxt .= "Origem: ".InboxItem::ORIGENS[$inboxItem->getOrigem()]."\n";
                $arquivoTxt .= "Criado em: ".$createdAt."\n";
                $arquivoTxt .= "Ultima att: ".$updatedAt."\n";
                $arquivoTxt .= "*******************\n";
                $arquivoTxt .= "\n\n\n";
            }

            return new JsonResponse(compact('arquivoTxt'), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/backup/exportProjetos", name="backupExportProjetos")
     */
    public function exportProjetos(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $projetos = $this->projetosService->findAll($usuario, [], []);

            foreach($projetos as $key => $projeto) {
                $projetos[$key]->serializarTarefas();
            }

            $habitos = $this->habitosService->findAll($usuario);
            foreach($habitos as $key => $habito) {
                $habitos[$key]->serializarHabitoRealizados();
            }
            
            $inboxItems = $this->inboxItemService->findAll($usuario, [], []);

            $categoriaItems = $this->categoriaItemsService->findAll($usuario, [], []);

            return new JsonResponse(compact('projetos', 'habitos', 'inboxItems', 'categoriaItems'), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    
    /**
     * @Route("/backup/importProjetos", name="backupImportProjetos")
     */
    public function importProjetos(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            // pega dados
            $usuario = $this->getUser();
            $requestData = $request->getContent();
            $requestData = json_decode($requestData);
            // prepara transaction
            $entityManager = $doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            // $file = $request->files->get('file');
            // if($file == null) {
            //     throw new BadRequestException('File not sent');
            // }
            // $mimetype = $file->getClientMimeType();
            // $path = $file->getPathname();

            // $data = file_get_contents($path);
            // $data = json_decode($data,true);

            // busca projetos para deletar
            $projetos = $doctrine->getRepository(Projeto::class)->findBy([
                'usuario' => $usuario
            ]);
            foreach ($projetos as $key => $projeto) {
                foreach ($projeto->getTarefas() as $key => $tarefa){
                    $entityManager->remove($tarefa);
                }
                $entityManager->remove($projeto);
            }

            $habitos = $doctrine->getRepository(Habito::class)->findBy([
                'usuario' => $usuario
            ]);
            foreach ($habitos as $key => $habito) {
                foreach ($habito->getHabitoRealizados() as $key => $realizado){
                    $entityManager->remove($realizado);
                }
                $entityManager->remove($habito);
            }
            
            $inboxItems = $doctrine->getRepository(InboxItem::class)->findBy([
                'usuario' => $usuario
            ]);
            foreach ($inboxItems as $key => $inboxItem) {
                $entityManager->remove($inboxItem);
            }
            
            $categoriaItems = $doctrine->getRepository(CategoriaItem::class)->findBy([
                'usuario' => $usuario
            ]);
            foreach ($categoriaItems as $key => $categoriaItem) {
                $entityManager->remove($categoriaItem);
            }

            foreach ($requestData->projetos as $key => $projeto) {
                //$projeto['name'] .= ' bkp'; //padrão backup
                $projetoObj = new Projeto();
                $projetoObj->setNome($projeto->nome);
                $projetoObj->setAnotacoes($projeto->anotacoes);
                $projetoObj->setSituacao($projeto->situacao);
                $projetoObj->setPrioridade($projeto->prioridade);
                $projetoObj->setDataPrazo(new DateTimeImmutable($projeto->dataPrazo));
                // created at
                $timezone = new DateTimeZone($projeto->createdAt->timezone);
                $createdAt = new DateTimeImmutable($projeto->createdAt->date, $timezone);
                $projetoObj->setCreatedAt($createdAt);
                // updated at
                if($projeto->updatedAt != null) {
                    $timezone = new DateTimeZone($projeto->updatedAt->timezone);
                    $updatedAt = new DateTimeImmutable($projeto->updatedAt->date, $timezone);
                    $projetoObj->setUpdatedAt($updatedAt);
                }
                // deleted at
                if($projeto->deletedAt != null) {
                    $timezone = new DateTimeZone($projeto->deletedAt->timezone);
                    $deletedAt = new DateTimeImmutable($projeto->deletedAt->date, $timezone);
                    $projetoObj->setDeletedAt($deletedAt);
                }
                // usuario
                $projetoObj->setUsuario($usuario);
                // persist
                $entityManager->persist($projetoObj);
                $projetoObj->getId();

                foreach($projeto->tarefas as $key => $tarefa) {
                    $tarefaObj = new Tarefa();
                    $tarefaObj->setDescricao($tarefa->descricao);
                    $tarefaObj->setMotivo($tarefa->motivo);
                    // situacao
                    if(property_exists($tarefa,'situacao')) {
                        $tarefaObj->setSituacao($tarefa->situacao);
                    } else {
                        $tarefaObj->setSituacao(Tarefa::SITUACAO_PENDENTE);
                    }
                    $timezone = new DateTimeZone($tarefa->createdAt->timezone);
                    //hora
                    $hora = new DateTimeImmutable($tarefa->hora, $timezone);
                    $tarefaObj->setHora($hora);
                    // created at
                    $createdAt = new DateTimeImmutable($tarefa->createdAt->date, $timezone);
                    $tarefaObj->setCreatedAt($createdAt);
                    // updated at
                    if($tarefa->updatedAt != null) {
                        $timezone = new DateTimeZone($tarefa->updatedAt->timezone);
                        $updatedAt = new DateTimeImmutable($tarefa->updatedAt->date, $timezone);
                        $tarefaObj->setUpdatedAt($updatedAt);
                    }
                    // deleted at
                    if($tarefa->deletedAt != null) {
                        $timezone = new DateTimeZone($tarefa->deletedAt->timezone);
                        $deletedAt = new DateTimeImmutable($tarefa->deletedAt->date, $timezone);
                        $tarefaObj->setDeletedAt($deletedAt);
                    }
                    //usuario / projeto
                    $tarefaObj->setUsuario($usuario);
                    $tarefaObj->setProjeto($projetoObj);
                    // persist
                    $entityManager->persist($tarefaObj);
                    $tarefaObj->getId();
                }
                // persist todas tarefas
                $entityManager->flush();
            }

            foreach ($requestData->habitos as $key => $habito) {
                $habitoObj = new Habito();
                $habitoObj->setDescricao($habito->descricao);
                $habitoObj->setMotivo($habito->motivo);
                $habitoObj->setSituacao($habito->situacao);
                // created at
                $timezone = new DateTimeZone($habito->createdAt->timezone);
                $hora = new DateTimeImmutable($habito->hora, $timezone);
                $habitoObj->setHora($hora);
                $createdAt = new DateTimeImmutable($habito->createdAt->date, $timezone);
                $habitoObj->setCreatedAt($createdAt);
                // updated at
                if($habito->updatedAt != null) {
                    $timezone = new DateTimeZone($habito->updatedAt->timezone);
                    $updatedAt = new DateTimeImmutable($habito->updatedAt->date, $timezone);
                    $habitoObj->setUpdatedAt($updatedAt);
                }
                // deleted at
                if($habito->deletedAt != null) {
                    $timezone = new DateTimeZone($habito->deletedAt->timezone);
                    $deletedAt = new DateTimeImmutable($habito->deletedAt->date, $timezone);
                    $habitoObj->setDeletedAt($deletedAt);
                }
                // usuario
                $habitoObj->setUsuario($usuario);
                // persist
                $entityManager->persist($habitoObj);
                $habitoObj->getId();

                foreach($habito->habitoRealizados as $key => $realizado) {
                    $realizadoObj = new HabitoRealizado();
                    $timezone = new DateTimeZone($realizado->createdAt->timezone);
                    //realizadoEm
                    $realizadoEm = new DateTimeImmutable($realizado->realizadoEm, $timezone);
                    $realizadoObj->setRealizadoEm($realizadoEm);
                    // created at
                    $createdAt = new DateTimeImmutable($realizado->createdAt->date, $timezone);
                    $realizadoObj->setCreatedAt($createdAt);
                    // updated at
                    if($realizado->updatedAt != null) {
                        $timezone = new DateTimeZone($realizado->updatedAt->timezone);
                        $updatedAt = new DateTimeImmutable($realizado->updatedAt->date, $timezone);
                        $realizadoObj->setUpdatedAt($updatedAt);
                    }
                    // deleted at
                    if($realizado->deletedAt != null) {
                        $timezone = new DateTimeZone($realizado->deletedAt->timezone);
                        $deletedAt = new DateTimeImmutable($realizado->deletedAt->date, $timezone);
                        $realizadoObj->setDeletedAt($deletedAt);
                    }
                    //usuario / habito
                    $realizadoObj->setUsuario($usuario);
                    $realizadoObj->setHabito($habitoObj);
                    // persist
                    $entityManager->persist($realizadoObj);
                    $realizadoObj->getId();
                }
                $entityManager->flush();
            }
            
            foreach ($requestData->categoriaItems as $key => $categoriaItem) {
                $categoriaItemObj = new CategoriaItem();
                $categoriaItemObj->setCategoria($categoriaItem->categoria);
                // created at
                $timezone = new DateTimeZone($categoriaItem->createdAt->timezone);
                $createdAt = new DateTimeImmutable($categoriaItem->createdAt->date, $timezone);
                $categoriaItemObj->setCreatedAt($createdAt);
                // updated at
                if($categoriaItem->updatedAt != null) {
                    $timezone = new DateTimeZone($categoriaItem->updatedAt->timezone);
                    $updatedAt = new DateTimeImmutable($categoriaItem->updatedAt->date, $timezone);
                    $categoriaItemObj->setUpdatedAt($updatedAt);
                }
                // deleted at
                if($categoriaItem->deletedAt != null) {
                    $timezone = new DateTimeZone($categoriaItem->deletedAt->timezone);
                    $deletedAt = new DateTimeImmutable($categoriaItem->deletedAt->date, $timezone);
                    $categoriaItemObj->setDeletedAt($deletedAt);
                }
                // usuario
                $categoriaItemObj->setUsuario($usuario);
                // persist
                $entityManager->persist($categoriaItemObj);
                $categoriaItemObj->getId();
                $entityManager->flush();
            }

            $categoriasItens = $this->categoriaItemsService->findAll($usuario);

            foreach ($requestData->inboxItems as $key => $inboxItem) {
                $inboxItemObj = new InboxItem();
                $inboxItemObj->setNome($inboxItem->nome);
                $inboxItemObj->setAcao($inboxItem->acao);
                $inboxItemObj->setLink($inboxItem->link);

                if(!is_null($inboxItem->categoriaItem)){
                    foreach ($categoriasItens as $categoriaItem){
                        if($inboxItem->categoriaItem->categoria == $categoriaItem->getCategoria()){
                            $inboxItem->categoriaItem = $categoriaItem;
                            break;
                        }
                    }
                }
                $inboxItemObj->setCategoriaItem($inboxItem->categoriaItem);
                $inboxItemObj->setOrigem($inboxItem->origem);
                // created at
                $timezone = new DateTimeZone($inboxItem->createdAt->timezone);
                $createdAt = new DateTimeImmutable($inboxItem->createdAt->date, $timezone);
                $inboxItemObj->setCreatedAt($createdAt);
                // updated at
                if($inboxItem->updatedAt != null) {
                    $timezone = new DateTimeZone($inboxItem->updatedAt->timezone);
                    $updatedAt = new DateTimeImmutable($inboxItem->updatedAt->date, $timezone);
                    $inboxItemObj->setUpdatedAt($updatedAt);
                }
                // deleted at
                if($inboxItem->deletedAt != null) {
                    $timezone = new DateTimeZone($inboxItem->deletedAt->timezone);
                    $deletedAt = new DateTimeImmutable($inboxItem->deletedAt->date, $timezone);
                    $inboxItemObj->setDeletedAt($deletedAt);
                }
                // usuario
                $inboxItemObj->setUsuario($usuario);
                // persist
                $entityManager->persist($inboxItemObj);
                $inboxItemObj->getId();
                $entityManager->flush();
            }

            $entityManager->getConnection()->commit();
            $mensagem = "Backup successfully restored";
            return new JsonResponse(compact('mensagem'), 200);
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollback();
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/backup/export", name="backupExport")
     */
    public function export(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $dias = $doctrine->getRepository(Dia::class)->findBy([
                'usuario' => $usuario
            ]);

            foreach($dias as $key => $dia) {
                $dias[$key]->serializarTarefas();
            }

            return new JsonResponse(compact('dias'), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/backup/import", name="backupImport")
     */
    public function import(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();
            $requestData = $request->getContent();
            $requestData = json_decode($requestData);
            
            $entityManager = $doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            // $file = $request->files->get('file');
            // if($file == null) {
            //     throw new BadRequestException('File not sent');
            // }
            // $mimetype = $file->getClientMimeType();
            // $path = $file->getPathname();

            // $data = file_get_contents($path);
            // $data = json_decode($data,true);

            $dias = $doctrine->getRepository(Dia::class)->findBy([
                'usuario' => $usuario
            ]);

            foreach ($dias as $key => $dia) {
                // foreach ($dia->getHoras() as $key => $hora) {
                    foreach ($dia->getAtividades() as $key => $atividade){
                        $entityManager->remove($atividade);
                    }
                    $entityManager->remove($dia);
                // }
                $entityManager->remove($dia);
            }
            // $entityManager->flush();

            foreach ($requestData->dias as $key => $dia) {

                //$dia['name'] .= ' bkp'; //padrão backup

                $diaObj = new Dia();
                $dataCompleta = new DateTimeImmutable($dia->dataCompleta);
                $diaObj->setDataCompleta($dataCompleta);
                
                $timezone = new DateTimeZone($dia->createdAt->timezone);
                $createdAt = new DateTimeImmutable($dia->createdAt->date, $timezone);
                $diaObj->setCreatedAt($createdAt);

                if($dia->updatedAt != null) {
                    $timezone = new DateTimeZone($dia->updatedAt->timezone);
                    $updatedAt = new DateTimeImmutable($dia->updatedAt->date, $timezone);
                    $diaObj->setUpdatedAt($updatedAt);
                }

                if($dia->deletedAt != null) {
                    $timezone = new DateTimeZone($dia->deletedAt->timezone);
                    $deletedAt = new DateTimeImmutable($dia->deletedAt->date, $timezone);
                    $diaObj->setDeletedAt($deletedAt);
                }

                $diaObj->setUsuario($usuario);

                $entityManager->persist($diaObj);

                $diaObj->getId();

                foreach($dia->atividades as $key => $atividade) {
                            
                    $atividadeObj = new Atividade();
                    $atividadeObj->setDescricao($atividade->descricao);
                    if(property_exists($atividade,'situacao')) {
                        $atividadeObj->setSituacao($atividade->situacao);
                    } else {
                        $atividadeObj->setSituacao(Atividade::SITUACAO_PENDENTE);
                    }

                    $timezone = new DateTimeZone($atividade->createdAt->timezone);
                    $createdAt = new DateTimeImmutable($atividade->createdAt->date, $timezone);
                    $atividadeObj->setCreatedAt($createdAt);
                    
                    $hora = new DateTimeImmutable($atividade->hora, $timezone);
                    $atividadeObj->setHora($hora);
    
                    if($atividade->updatedAt != null) {
                        $timezone = new DateTimeZone($atividade->updatedAt->timezone);
                        $updatedAt = new DateTimeImmutable($atividade->updatedAt->date, $timezone);
                        $atividadeObj->setUpdatedAt($updatedAt);
                    }
    
                    if($atividade->deletedAt != null) {
                        $timezone = new DateTimeZone($atividade->deletedAt->timezone);
                        $deletedAt = new DateTimeImmutable($atividade->deletedAt->date, $timezone);
                        $atividadeObj->setDeletedAt($deletedAt);
                    }
                    
                    $atividadeObj->setUsuario($usuario);
                    $atividadeObj->setDia($diaObj);

                    $entityManager->persist($atividadeObj);
                    // $entityManager->flush();

                    $atividadeObj->getId();

                }

                // foreach ($dia->horas as $key => $hora) {
                    
                //     $horaObj = new Hora();
                //     $horaObj->setHora($hora->hora);

                //     $timezone = new DateTimeZone($hora->createdAt->timezone);
                //     $createdAt = new DateTimeImmutable($hora->createdAt->date, $timezone);
                //     $horaObj->setCreatedAt($createdAt);
    
                //     if($hora->updatedAt != null) {
                //         $timezone = new DateTimeZone($hora->updatedAt->timezone);
                //         $updatedAt = new DateTimeImmutable($hora->updatedAt->date, $timezone);
                //         $horaObj->setUpdatedAt($updatedAt);
                //     }
    
                //     if($hora->deletedAt != null) {
                //         $timezone = new DateTimeZone($hora->deletedAt->timezone);
                //         $deletedAt = new DateTimeImmutable($hora->deletedAt->date, $timezone);
                //         $horaObj->setDeletedAt($deletedAt);
                //     }
                    
                //     $horaObj->setUsuario($usuario);
                //     $horaObj->setDia($diaObj);

                //     $entityManager->persist($horaObj);
                //     // $entityManager->flush();

                //     $horaObj->getId();

                //     foreach($hora->atividades as $key => $atividade) {
                            
                //         $atividadeObj = new Atividade();
                //         $atividadeObj->setDescricao($atividade->descricao);
                //         if(property_exists($atividade,'situacao')) {
                //             $atividadeObj->setSituacao($atividade->situacao);
                //         } else {
                //             $atividadeObj->setSituacao(Atividade::SITUACAO_PENDENTE);
                //         }

                //         $timezone = new DateTimeZone($atividade->createdAt->timezone);
                //         $createdAt = new DateTimeImmutable($atividade->createdAt->date, $timezone);
                //         $atividadeObj->setCreatedAt($createdAt);
        
                //         if($atividade->updatedAt != null) {
                //             $timezone = new DateTimeZone($atividade->updatedAt->timezone);
                //             $updatedAt = new DateTimeImmutable($atividade->updatedAt->date, $timezone);
                //             $atividadeObj->setUpdatedAt($updatedAt);
                //         }
        
                //         if($atividade->deletedAt != null) {
                //             $timezone = new DateTimeZone($atividade->deletedAt->timezone);
                //             $deletedAt = new DateTimeImmutable($atividade->deletedAt->date, $timezone);
                //             $atividadeObj->setDeletedAt($deletedAt);
                //         }
                        
                //         $atividadeObj->setUsuario($usuario);
                //         $atividadeObj->setHora($horaObj);

                //         $entityManager->persist($atividadeObj);
                //         // $entityManager->flush();

                //         $atividadeObj->getId();

                //     }

                // }

                $entityManager->flush();
            }

            $entityManager->getConnection()->commit();

            $mensagem = "Backup successfully restored";

            return new JsonResponse(compact('mensagem'), 200);
            
        } catch (\Exception $e) {
            $entityManager->getConnection()->rollback();
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
