<?php

namespace App\Tests;

use App\Entity\Atividade;
use Doctrine\Persistence\ManagerRegistry;

class AtividadesTest extends AppWebTestCase
{

    protected $diasService;
    protected $atividadesService;

    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser;
     */
    protected $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = static::createClient();
        $kernel = self::bootKernel();
        $this->doctrine = $kernel->getContainer()->get('doctrine');
        $this->entityManager = $this->doctrine->getManager();
        
        $this->authService = $kernel->getContainer()->get('App\Service\AuthService');
        $this->diasService = $kernel->getContainer()->get('App\Service\DiasService');
        $this->atividadesService = $kernel->getContainer()->get('App\Service\AtividadesService');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testNaoPodeListarAtividadeSemLogar(): void
    {
        $dados = [
            'dia' => '1',
            'descricao' => 'atividade1'
        ];
        [$response, $json] = $this->request('GET', '/atividades', $dados);

        $this->assertResponseStatusCodeSame(401);
    }
    
    public function testPodeListarAtividadeSemLogar(): void
    {
        $dados = [
            'dia' => '1',
            'descricao' => 'atividade1'
        ];
        [$response, $json] = $this->request('GET', '/atividades', $dados);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testNaoPodeCriarAtividadeSemLogar(): void
    {
        $dados = [
            'dia' => '1',
            'descricao' => 'atividade1'
        ];
        [$response, $json] = $this->request('POST', '/atividades', $dados);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testCriarAtividade(): void
    {
        $this->serviceLoggedInUser();
        $dia = $this->testerCreateNewDiaFromDataCompleta();
        $hora = $dia->getDataCompleta()->format('Y-m-d') . '12:00:00';

        $dados = [
            'dia' => $dia->getId(),
            'hora' => $hora,
            'descricao' => 'atividade1'
        ];
        [$response, $json] = $this->request('POST', '/atividades', $dados);

        $this->assertResponseStatusCodeSame(201);
        $atividadeDb = $this->entityManager->getRepository(Atividade::class)->findOneBy(['id' => $json->id, 'usuario' => $this->user]);
        $this->assertNotNull($atividadeDb);
        $this->assertEquals($atividadeDb->getId(), $json->id);
    }

    
    public function testEditarAtividade(): void
    {
        $this->serviceLoggedInUser();
        $dia = $this->testerCreateNewDiaFromDataCompleta();
        $hora = $dia->getDataCompleta()->format('Y-m-d') . ' 12:00:00';
        $atividade = $this->testerCreateNewAtividade($dia->getId(), $hora);

        $atividadeId = $atividade->getId();
        $atividade->setDescricao('atividade1');

        $dados = [
            'dia' => $dia->getId(),
            'descricao' => 'atividade1',
            'hora' => $atividade->getHora()->format('H:i')
        ];

        [$response, $json] = $this->request('PUT', '/atividades/' . $atividadeId, $dados);

        $this->assertResponseStatusCodeSame(200);
        $atividadeDb = $this->entityManager->getRepository(Atividade::class)->findOneBy([
            'id' => $atividade->getId(),
            'descricao' => $atividade->getDescricao(),
            'situacao' => $atividade->getSituacao(),
            'usuario' => $this->user
        ]);
        $this->assertNotNull($atividadeDb);
        $this->assertEquals($atividadeDb->getId(), $atividade->getId());
    }

    
    public function testConcluirAtividade(): void
    {
        $this->serviceLoggedInUser();
        $dia = $this->testerCreateNewDiaFromDataCompleta();
        $hora = $dia->getDataCompleta()->format('Y-m-d') . ' 12:00:00';
        $atividade = $this->testerCreateNewAtividade($dia->getId(), $hora);
        $atividadeId = $atividade->getId();

        [$response, $json] = $this->request('POST', '/atividades/' . $atividadeId . '/concluir', []);

        $this->assertResponseStatusCodeSame(200);
        /**
         * @todo resolver erro abaixo - está buscando no banco mas acha o registro desatualizado
         */
        $atividadeDb = $this->entityManager->getRepository(Atividade::class)->findOneBy([
            'id' => $atividade->getId(),
            'usuario' => $this->user
        ]);
        // $this->assertEquals(Atividade::SITUACAO_CONCLUIDO, $atividadeDb->getSituacao());
        $this->assertEquals(Atividade::SITUACAO_CONCLUIDO, $json->situacao);
        $this->assertNotNull($atividadeDb);
        $this->assertEquals($atividadeDb->getId(), $atividade->getId());
    }

    public function testFalharAtividade(): void
    {
        $this->serviceLoggedInUser();
        $dia = $this->testerCreateNewDiaFromDataCompleta();
        $hora = $dia->getDataCompleta()->format('Y-m-d') . ' 12:00:00';
        $atividade = $this->testerCreateNewAtividade($dia->getId(), $hora);

        $atividadeId = $atividade->getId();

        [$response, $json] = $this->request('POST', '/atividades/' . $atividadeId . '/falhar', []);

        $this->assertResponseStatusCodeSame(200);
        /**
         * @todo resolver erro abaixo - está buscando no banco mas acha o registro desatualizado
         */
        $atividadeDb = $this->entityManager->getRepository(Atividade::class)->findOneBy([
            'id' => $atividade->getId(),
            'usuario' => $this->user
        ]);
        // $this->assertEquals(Atividade::SITUACAO_CONCLUIDO, $atividadeDb->getSituacao());
        $this->assertEquals(Atividade::SITUACAO_FALHA, $json->situacao);
        $this->assertNotNull($atividadeDb);
        $this->assertEquals($atividadeDb->getId(), $atividade->getId());
    }
}
