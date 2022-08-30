<?php

namespace App\Tests;

use App\Entity\Dia;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\InvitationToken;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppWebTestCase extends WebTestCase
{

    protected $token;
    protected $refreshToken;

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

    protected function testerCreateNewDiaFromDataCompleta($dados =[]){
        $dataCompleta = isset($dados['dataCompleta']) ? $dados['dataCompleta'] : '2001-03-03';
        $dataCompleta = new DateTimeImmutable($dataCompleta);
        return $this->diasService->createNewDiaFromDataCompleta($dataCompleta, $this->user);
    }

    protected function testerCreateNewAtividade($dia, $hora, $dados =[]){
        $descricao = isset($dados['descricao']) ? $dados['descricao'] : 'descricao teste';
        
        $atividade = $this->atividadesService->factoryAtividade($descricao, $hora->getId(), $this->user);
        $atividade = $this->atividadesService->createNewAtividade($atividade);
        return $atividade;
    }

}
