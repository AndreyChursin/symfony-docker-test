<?php

namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class DatabaseTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        // Запускаем Symfony-кернел (для тестов)
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testDatabaseConnection(): void
    {
        $connection = $this->entityManager->getConnection();
        echo "Using Database: " . $connection->getDatabase();
        $this->assertTrue($connection->isConnected(), 'Ошибка соединения с БД');
    }

    public function testInsertAndDeleteEntity(): void
    {
        // Генерируем случайный email
        $email = 'test_' . uniqid() . '@example.com';
        $pass = '123456';

        // Создаем нового пользователя
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($pass);

        // Сохраняем в БД
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Проверяем, что запись создана
        $repo = $this->entityManager->getRepository(User::class);
        $savedUser = $repo->findOneBy(['email' => $email]);
        $this->assertNotNull($savedUser, 'Запись не найдена в БД');

        // Удаляем запись
        $this->entityManager->remove($savedUser);
        $this->entityManager->flush();

        // Проверяем, что запись удалена
        $deletedUser = $repo->findOneBy(['email' => $email]);
        $this->assertNull($deletedUser, 'Запись не была удалена');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
