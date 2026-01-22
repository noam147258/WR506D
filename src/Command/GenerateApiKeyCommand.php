<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-api-key',
    description: 'Génère une clé API pour un utilisateur'
)]
class GenerateApiKeyCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email de l\'utilisateur')
            ->setHelp('Cette commande génère une clé API pour un utilisateur spécifique.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $io->error("Utilisateur avec l'email '{$email}' non trouvé.");
            return Command::FAILURE;
        }

        // Générer la clé API
        $randomBytes = random_bytes(32);
        $apiKey = bin2hex($randomBytes);
        $apiKeyHash = hash('sha256', $apiKey);
        $apiKeyPrefix = substr($apiKey, 0, 16);

        // Stocker le hash et le préfixe
        $user->setApiKeyHash($apiKeyHash);
        $user->setApiKeyPrefix($apiKeyPrefix);
        $user->setApiKeyEnabled(true);
        $user->setApiKeyCreatedAt(new \DateTimeImmutable());
        $user->setApiKeyLastUsedAt(null);

        $this->entityManager->flush();

        $io->success('Clé API générée avec succès !');
        $io->writeln('');
        $io->writeln('Clé API complète (à copier immédiatement, elle ne sera plus affichée) :');
        $io->writeln('<fg=green>' . $apiKey . '</>');
        $io->writeln('');
        $io->writeln("Préfixe : {$apiKeyPrefix}");
        $io->writeln("Utilisateur : {$user->getEmail()}");
        $io->writeln("Statut : Activée");

        return Command::SUCCESS;
    }
}
