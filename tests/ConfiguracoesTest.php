<?php

namespace App\Tests;

use App\Entity\Dia;
use App\Entity\Configuracao;
use App\Service\AuthService;
use App\Service\ConfiguracoesService;
use Doctrine\Persistence\ManagerRegistry;

class ConfiguracoesTest extends AppWebTestCase
{

    protected $user;
    
    /**
     * @var AuthService
     */
    protected $authService;

    /**
     * @var ConfiguracoesService
     */
    protected $configuracoesService;
    
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
        $this->configuracoesService = $kernel->getContainer()->get('App\Service\ConfiguracoesService');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testNaoPodeListarConfiguracoesNaoLogado(): void
    {
        [$response, $json] = $this->request('GET', '/configuracoes', []);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testListarConfiguracoes(): void
    {
        $this->serviceLoggedInUser();
        $configuracoes[] = $this->testerCreateNewConfiguracao();
        $configuracoes[] = $this->testerCreateNewConfiguracao();

        [$response, $json] = $this->request('GET', '/configuracoes', []);

        $this->assertResponseStatusCodeSame(200);
        $this->assertCount(count($configuracoes), $json);
    }

    public function testCriarConfiguracao(): void
    {
        $this->serviceLoggedInUser();
        $configuracaoBody = [
            'chave' => Configuracao::CHAVE_EXIBIR_DIA_SEMANA_HABIT_TRACKER,
            'valor' => '1'
        ];
        [$response, $json] = $this->request('POST', '/configuracoes', $configuracaoBody);

        $this->assertResponseStatusCodeSame(201);
        $configuracaoDb = $this->entityManager->getRepository(Configuracao::class)->findOneBy(['id' => $json->id, 'usuario' => $this->user]);
        $this->assertNotNull($configuracaoDb);
        $this->assertEquals($configuracaoDb->getId(), $json->id);
    }
}
