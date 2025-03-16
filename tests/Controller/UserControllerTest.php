<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
    }

    public function testCreateUser(): void
    {
        // Генерируем случайный email
        $email = 'test_' . uniqid() . '@example.com';
        $password = '123456';
        $url = $this->client->getContainer()->get('router')->generate('user_create');

        // Отправляем запрос на создание пользователя
        $this->client->request('POST', $url, [
            'email' => $email,
            'password' => $password,
        ]);

        // Проверяем, что запрос прошел успешно
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Проверяем, что пользователь был создан в базе данных
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        $this->assertNotNull($user, 'Пользователь не был создан в базе данных');

        // Удаляем созданного пользователя
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
