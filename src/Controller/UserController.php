<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserController extends AbstractController
{
    #[Route('/user/create', name: 'user_create', methods: ['POST', 'PUT'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Получаем данные из запроса
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        // Проверяем, что email и пароль не пустые
        if (empty($email) || empty($password)) {
            return new Response('Email and password are required', 400);
        }

        // Создаем нового пользователя
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);

        // Сохраняем пользователя в базе данных
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('User created', 200);
    }

    #[Route('/user/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        // Находим пользователя по id
        $user = $entityManager->getRepository(User::class)->find($id);

        // Проверяем, что пользователь существует
        if (!$user) {
            return new Response('User not found', 404);
        }

        // Удаляем пользователя
        $entityManager->remove($user);
        $entityManager->flush();

        return new Response('User deleted', 200);
    }
}
