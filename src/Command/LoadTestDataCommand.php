<?php

namespace App\Command;

use App\Entity\Actor;
use App\Entity\Category;
use App\Entity\Movie;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-test-data',
    description: 'Charge des données de test (films, acteurs, catégories)',
)]
class LoadTestDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Chargement des données de test');

        // Créer des catégories
        $categories = [];
        $categoryNames = ['Action', 'Comédie', 'Drame', 'Science-Fiction', 'Horreur'];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setCreatedAt(new DateTimeImmutable());
            $this->entityManager->persist($category);
            $categories[] = $category;
        }
        $io->info(sprintf('Création de %d catégories...', count($categories)));

        // Créer des acteurs
        $actors = [];
        $firstNames = ['Jean', 'Marie', 'Pierre', 'Sophie', 'Paul', 'Julie', 'Marc', 'Anne', 'Luc', 'Claire'];
        $lastNames = ['Dupont', 'Martin', 'Bernard', 'Thomas', 'Petit', 'Robert', 'Richard', 'Durand', 'Dubois', 'Moreau'];
        
        for ($i = 0; $i < 20; $i++) {
            $actor = new Actor();
            $actor->setFirstname($firstNames[array_rand($firstNames)]);
            $actor->setLastname($lastNames[array_rand($lastNames)]);
            $actor->setCreatedAt(new DateTimeImmutable());
            $this->entityManager->persist($actor);
            $actors[] = $actor;
        }
        $io->info(sprintf('Création de %d acteurs...', count($actors)));

        // Créer des films
        $movieTitles = [
            'Le Seigneur des Anneaux', 'Matrix', 'Inception', 'Interstellar', 'Pulp Fiction',
            'Forrest Gump', 'Le Parrain', 'Fight Club', 'Shutter Island', 'Django Unchained',
            'Inglourious Basterds', 'Dunkirk', 'The Dark Knight', 'Gladiator', 'Titanic',
            'Avatar', 'Jurassic Park', 'Terminator', 'Alien', 'Blade Runner'
        ];

        for ($i = 0; $i < 30; $i++) {
            $movie = new Movie();
            $movie->setName($movieTitles[array_rand($movieTitles)] . ' ' . ($i + 1));
            $movie->setOnline(true);
            $movie->setCreatedAt(new DateTimeImmutable());
            
            // Ajouter une catégorie aléatoire
            if (!empty($categories)) {
                $movie->addCategory($categories[array_rand($categories)]);
            }
            
            // Ajouter 1-3 acteurs aléatoires
            $numActors = rand(1, 3);
            $selectedActors = (array) array_rand($actors, min($numActors, count($actors)));
            foreach ($selectedActors as $actorIndex) {
                $movie->addActor($actors[$actorIndex]);
            }
            
            $this->entityManager->persist($movie);
        }
        $io->info('Création de 30 films...');

        $this->entityManager->flush();

        $io->success('Données de test chargées avec succès !');
        $io->note(sprintf('- %d catégories', count($categories)));
        $io->note(sprintf('- %d acteurs', count($actors)));
        $io->note('- 30 films');

        return Command::SUCCESS;
    }
}
