<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Projeto;
use App\Entity\InboxitemCategoria;
use Facebook\WebDriver\WebDriverBy;
use Doctrine\Persistence\ManagerRegistry;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BackupService
{
    
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function backupTable(string $table, $connection)
    {
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->select('*')->from($table);
        $result = $queryBuilder->executeQuery();
        $rows = $result->fetchAllAssociative();
        $file = '';
        foreach ($rows as $row) {
            $columns = array_keys($row);
            $values = array_map(function ($value) use ($connection) {
                return $connection->quote($value);
            }, array_values($row));
            $insert = sprintf(
                "INSERT INTO %s (%s) VALUES (%s);\n",
                $table,
                implode(', ', $columns),
                implode(', ', $values)
            );
            $file .= $insert . PHP_EOL;
        }
        return $file;
    }

    public function runBackup()
    {
        try {
            $arquivoSql = '';
            $entityManager = $this->doctrine->getManager();
            $connection = $entityManager->getConnection();
            $schemaManager = $connection->createSchemaManager();
            $tables = $schemaManager->listTableNames();
            foreach ($tables as $table) {
                $arquivoSql .= $this->backupTable($table, $connection);
            }
            return $arquivoSql;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}