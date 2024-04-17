<?php

namespace App\Tests;

use App\Entity\Dia;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Atividade;
use App\Entity\Configuracao;
use App\Service\DiasService;
use App\Entity\InvitationToken;
use App\Repository\UserRepository;
use App\Service\AtividadesService;
use App\Service\ConfiguracoesService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppWebTestCase extends WebTestCase
{

    protected $httpClient;
    protected $entityManager;
    protected $authService;
    protected $user;
    protected $token;
    protected $refreshToken;

    /**
     * @var ConfiguracoesService
     */
    protected $configuracoesService;

    /**
     * @var DiasService
     */
    protected $diasService;

    /**
     * @var AtividadesService
     */
    protected $atividadesService;

    protected function request($method, $uri, $data){
        $headers = [];
        if($this->token != null){
            $headers['HTTP_AUTHORIZATION'] = $this->token;
        }
        $this->httpClient->jsonRequest($method, $uri, $data, $headers);
        $response = $this->httpClient->getResponse();
        $responseData = json_decode($response->getContent());
        if($responseData != null) $response->setData($responseData);
        return [$response,$responseData];
    }

    /**
     * @return InvitationToken
     */
    protected function serviceCreateInvitationToken($dados = []){

        $invitationToken = new InvitationToken();
        $invitationToken->setInvitationToken(isset($dados['token']) ? $dados['token'] : '123456');
        $invitationToken->setEmail(isset($dados['email']) ? $dados['email'] : null);

        if(isset($dados['active'])) {
            $invitationToken->setActive($dados['active']);
        }
        $this->entityManager->persist($invitationToken);
        $this->entityManager->flush();
        return $invitationToken;
    }

    /**
     * @return InvitationToken
     */
    protected function serviceCreateUser($dados = []){

        $token = $this->serviceCreateInvitationToken();
        $user = new User();
        $user->setEmail('rui@rui');
        $user->setPassword(isset($dados['password']) ? $dados['password'] : '123456');
        $user = $this->authService->registerUser($user, $token->getInvitationToken());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    protected function serviceLoggedInUser(){
        $this->user = $this->serviceCreateUser();
        $data = [
            'password' => '123456',
            'email' => $this->user->getEmail()
        ];
        [$response, $json] = $this->request('POST','/auth/login', $data);
        $this->token = $json->token;
        $this->refreshToken = $json->refreshToken;
        return $json;
    }

    protected function testerCreateNewDiaFromDataCompleta($dados = []) : Dia
    {
        $dataCompleta = isset($dados['dataCompleta']) ? $dados['dataCompleta'] : '2001-03-03';
        $dataCompleta = new DateTimeImmutable($dataCompleta);
        $dia = $this->diasService->createNewDiaFromDataCompleta($dataCompleta, $this->user);
        return $this->entityManager->getRepository(Dia::class)->findOneBy(['id' => $dia->getId(), 'usuario' => $this->user]);
    }

    protected function testerCreateNewAtividade(int $dia, string $hora, array $dados = []) : Atividade
    {
        $descricao = isset($dados['descricao']) ? $dados['descricao'] : 'descricao teste';
        
        $atividade = $this->atividadesService->factoryAtividade($descricao, $dia, $hora, $this->user);
        $atividade = $this->atividadesService->createNewAtividade($atividade);
        return $this->entityManager->getRepository(Atividade::class)->findOneBy(['id' => $atividade->getId(), 'usuario' => $this->user]);
    }

    protected function testerCreateNewConfiguracao($dados = []): Configuracao
    {
        $configuracao = $this->configuracoesService->factoryConfiguracao(Configuracao::CHAVE_EXIBIR_DIA_SEMANA_HABIT_TRACKER, '1', $this->user);
        $configuracao = $this->configuracoesService->createNewConfiguracao($configuracao);
        return $this->entityManager->getRepository(Configuracao::class)->findOneBy(['id' => $configuracao->getId(), 'usuario' => $this->user]);
    }

}
