<?php
// Script temporaire pour créer un utilisateur
// À exécuter dans le Shell Render : php create_user_script.php

require __DIR__.'/vendor/autoload.php';

use App\Kernel;
use App\Entity\User;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__.'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'prod', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine.orm.entity_manager');
$passwordHasher = $container->get('security.user_password_hasher');

$email = 'noam.brodeur.fr@gmail.com';
$password = 'nb18072005';

// Vérifier si l'utilisateur existe déjà
$userRepository = $entityManager->getRepository(User::class);
$existingUser = $userRepository->findOneBy(['email' => $email]);

if ($existingUser) {
    echo "L'utilisateur $email existe déjà.\n";
    exit(1);
}

// Créer l'utilisateur
$user = new User();
$user->setEmail($email);
$user->setRoles(['ROLE_USER']);

$hashedPassword = $passwordHasher->hashPassword($user, $password);
$user->setPassword($hashedPassword);

$entityManager->persist($user);
$entityManager->flush();

echo "Utilisateur créé avec succès : $email\n";
