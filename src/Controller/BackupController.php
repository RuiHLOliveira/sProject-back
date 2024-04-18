<?php

namespace App\Controller;

use DateTime;
use DateTimeZone;
use App\Entity\Dia;
use App\Entity\Note;
use App\Entity\Tarefa;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\Notebook;
use App\Entity\Atividade;
use App\Entity\Historico;
use App\Service\ProjetosService;
use App\Service\HistoricosService;
use Doctrine\Persistence\ManagerRegistry;
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
     * @var HistoricosService $historicosService
     */
    private $historicosService;

    public function __construct(
        ProjetosService $projetosService,
        HistoricosService $historicosService
    ) {
        $this->projetosService = $projetosService;
        $this->historicosService = $historicosService;
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

            return new JsonResponse(compact('arquivoTxt'), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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

            return new JsonResponse(compact('projetos'), 200);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
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
