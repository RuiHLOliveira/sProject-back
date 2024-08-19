<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\InboxItem;
use Facebook\WebDriver\WebDriverBy;
use Doctrine\Persistence\ManagerRegistry;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class InboxItemService
{
    
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array
     */
    public function findAll(User $usuario, array $filters = [], array $orderBy = null): array
    {
        $filters['usuario'] = $usuario;
        return $this->doctrine->getRepository(InboxItem::class)->findBy($filters, $orderBy);
    }

    /**
     * @param string $idInboxItem
     * @param User $usuario
     */
    public function find(string $idInboxItem, User $usuario): InboxItem
    {
        return $this->doctrine->getRepository(InboxItem::class)->findOneBy([
            'id' => $idInboxItem,
            'usuario' => $usuario
        ]);
    }

    /**
     * @param User $usuario
     * @param array $orderBy
     * @return array<InboxItem>
     */
    public function listaInboxItemsUseCase(User $usuario, array $filters = [], array $orderBy = null): array
    {
        try {
            $InboxItems = $this->findAll($usuario, $filters, $orderBy);
            return $InboxItems;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    /**
     * @param InboxItem $inboxItem
     * @return InboxItem
     */
    public function atualizaInboxItemUseCase(InboxItem $inboxItem): InboxItem
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $inboxItem->setUpdatedAt(new DateTimeImmutable());

            $entityManager->persist($inboxItem);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            return $inboxItem;
        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    /**
     * @param string $nome
     * @param User $usuario
     * @return InboxItem
     */
    public function factoryInboxItem($link, $nome, $usuario) {

        $inboxItem = new InboxItem();
        $inboxItem->setUsuario($usuario);
        $inboxItem->setLink($link);
        $inboxItem->setNome($nome);
        $inboxItem->setOrigem(0);
        $inboxItem->setCategoriaItem(null);
        $inboxItem->setAcao('');
        $this->fillOrigenAutomatica($inboxItem);
        $this->grabTitle($inboxItem);
        
        return $inboxItem;
    }

    public function fillOrigenAutomatica(InboxItem $inboxItem)
    {
        if($inboxItem->getOrigem() > 0) return;
        $originStrings = [
            ['string' => 'youtube.com', 'origin' => InboxItem::ORIGEM_YOUTUBE],
            ['string' => 'youtu.be/', 'origin' => InboxItem::ORIGEM_YOUTUBE],
            ['string' => 'www.instagram.com', 'origin' => InboxItem::ORIGEM_INSTAGRAM],
        ];
        foreach ($originStrings as $originString) {
            if( strstr($inboxItem->getLink(), $originString['string']) !== false ) {
                $inboxItem->setOrigem($originString['origin']);
                return;
            }
        }
    }

    public function grabTitle(InboxItem $inboxItem)
    {
        if($inboxItem->getNome() != '') return;
        if($inboxItem->getOrigem() == InboxItem::ORIGEM_INSTAGRAM) return;
        $html = file_get_contents($inboxItem->getlink());
        // Utilizando expressões regulares para encontrar a tag <title>
        preg_match('/<title>(.*)<\/title>/i', $html, $matches);
        if(count($matches) > 0)
            $title = $matches[1];
        else
            $title = '';
        $inboxItem->setNome($title);
    }

    public function createNewInboxItem(InboxItem $inboxItem)
    {
        $entityManager = $this->doctrine->getManager();
        try {
            $entityManager->getConnection()->beginTransaction();

            $this->fillOrigenAutomatica($inboxItem);
            $this->grabTitle($inboxItem);
            $inboxItem->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($inboxItem);
            $entityManager->flush();

            $entityManager->getConnection()->commit();

            return $inboxItem;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

    
    public function editarUseCase(InboxItem $inboxItem, User $usuario){
        
        try {
            $entityManager = $this->doctrine->getManager();
            $entityManager->getConnection()->beginTransaction();

            $this->fillOrigenAutomatica($inboxItem);
            $this->grabTitle($inboxItem);
            $inboxItem->setUpdatedAt(new DateTimeImmutable());
            $entityManager->persist($inboxItem);
            $entityManager->flush();

            $entityManager->getConnection()->commit();


            return $inboxItem;

        } catch (\Throwable $th) {
            $entityManager->getConnection()->rollback();
            throw $th;
        }
    }

}