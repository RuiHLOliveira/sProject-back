<?php

namespace App\Tests;

use App\Entity\Dia;
use App\Entity\User;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DiasTest extends WebTestCase
{
    private function createTestUserViaAPI($client){

        $data = [
            'password' => '123456',
            'email' => 'rui@rui.com'
        ];

        $response = $client->jsonRequest('POST', '/auth/register', $data);
        return $response;
    }

    public function testRegistrar(): void
    {
        $client = static::createClient();

        $response = $this->createTestUserViaAPI($client);

        $this->assertResponseStatusCodeSame(200);
        // $this->assertSelectorTextContains('h2', 'Give your feedback');
    }

    public function testNaoPodeListarDias(): void
    {
        $client = static::createClient();
        $response = $client->jsonRequest('GET', '/dias');
        $this->assertResponseStatusCodeSame(401);
    }

    // public function testListarDias(ManagerRegistry $doctrine, UserPasswordEncoderInterface $encoder): void
    // {
    //     $client = static::createClient();

    //     $password = '123456';
    //     $email = 'rui@rui.com';

    //     // retrieve the test user
    //     $testUser = $userRepository->findOneByEmail('ruigx@hotmail.com');
    //     // simulate $testUser being logged in
    //     $client->loginUser($testUser);

    //     $response = $client->xmlHttpRequest('GET', '/dias');
    //     $this->assertResponseStatusCodeSame(401);
    //     // $this->assertSelectorTextContains('h2', 'Give your feedback');
    // }
}
