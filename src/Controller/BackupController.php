<?php

namespace App\Controller;

use App\Entity\Atividade;
use DateTime;
use DateTimeZone;
use App\Entity\Dia;
use App\Entity\Hora;
use App\Entity\Note;
use App\Entity\Notebook;
use DateTimeImmutable;
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
     * @Route("/backup/export", name="backupExport")
     */
    public function index(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        try {
            $usuario = $this->getUser();

            $dias = $doctrine->getRepository(Dia::class)->findBy([
                'usuario' => $usuario
            ]);

            foreach($dias as $key => $dia) {
                $dias[$key]->serializarHoras();
                $dias[$key]->serializarAtividades();
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
            $requestData = json_decode($request->getContent());
            
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
                foreach ($dia->getHoras() as $key => $hora) {
                    foreach ($hora->getAtividades() as $key => $atividade){
                        $entityManager->remove($atividade);
                    }
                    $entityManager->remove($hora);
                }
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

                foreach ($dia->horas as $key => $hora) {
                    
                    $horaObj = new Hora();
                    $horaObj->setHora($hora->hora);

                    $timezone = new DateTimeZone($hora->createdAt->timezone);
                    $createdAt = new DateTimeImmutable($hora->createdAt->date, $timezone);
                    $horaObj->setCreatedAt($createdAt);
    
                    if($hora->updatedAt != null) {
                        $timezone = new DateTimeZone($hora->updatedAt->timezone);
                        $updatedAt = new DateTimeImmutable($hora->updatedAt->date, $timezone);
                        $horaObj->setUpdatedAt($updatedAt);
                    }
    
                    if($hora->deletedAt != null) {
                        $timezone = new DateTimeZone($hora->deletedAt->timezone);
                        $deletedAt = new DateTimeImmutable($hora->deletedAt->date, $timezone);
                        $horaObj->setDeletedAt($deletedAt);
                    }
                    
                    $horaObj->setUsuario($usuario);
                    $horaObj->setDia($diaObj);

                    $entityManager->persist($horaObj);
                    // $entityManager->flush();

                    $horaObj->getId();

                    foreach($hora->atividades as $key => $atividade) {
                            
                        $atividadeObj = new Atividade();
                        $atividadeObj->setDescricao($atividade->descricao);

                        $timezone = new DateTimeZone($atividade->createdAt->timezone);
                        $createdAt = new DateTimeImmutable($atividade->createdAt->date, $timezone);
                        $atividadeObj->setCreatedAt($createdAt);
        
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
                        $atividadeObj->setHora($horaObj);

                        $entityManager->persist($atividadeObj);
                        // $entityManager->flush();

                        $atividadeObj->getId();

                    }

                }

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
