<?php

namespace App\Command;

use App\Repository\ActorRepository;
use App\Repository\CategoryRepository;
use App\Repository\MediaObjectRepository;
use App\Repository\MovieRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:stats',
    description: 'Affiche les statistiques de l\'application'
)]
class StatsCommand extends Command
{
    public function __construct(
        private readonly MovieRepository $movieRepository,
        private readonly ActorRepository $actorRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly MediaObjectRepository $mediaObjectRepository,
        private readonly string $projectDir
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Affiche les statistiques de l\'application')
            ->addArgument(
                'type',
                InputArgument::REQUIRED,
                'Type de données demandées (all, movies, actors, categories, images)'
            )
            ->addOption('details', null, InputOption::VALUE_NONE, 'Affiche des détails supplémentaires')
            ->addOption('log-file', null, InputOption::VALUE_OPTIONAL, 'Chemin vers le fichier de log');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getArgument('type');
        $details = $input->getOption('details');
        $logFile = $input->getOption('log-file');

        $output->writeln('=== Statistiques de l\'application ===');
        $output->writeln('');

        $content = '';

        // Nombre de films
        if ($type === 'all' || $type === 'movies') {
            $nbMovies = $this->movieRepository->count([]);
            $line = "Nombre de films : {$nbMovies}";
            $output->writeln($line);
            $content .= $line . "\n";
        }

        // Nombre d'acteurs
        if ($type === 'all' || $type === 'actors') {
            $nbActors = $this->actorRepository->count([]);
            $line = "Nombre d'acteurs : {$nbActors}";
            $output->writeln($line);
            $content .= $line . "\n";
        }

        // Nombre de catégories et films par catégorie
        if ($type === 'all' || $type === 'categories') {
            $nbCategories = $this->categoryRepository->count([]);
            $line = "Nombre de catégories : {$nbCategories}";
            $output->writeln($line);
            $content .= $line . "\n";

            if ($details) {
                $categories = $this->categoryRepository->findAll();
                foreach ($categories as $category) {
                    $nbMoviesInCategory = $category->getMovies()->count();
                    $line = "  - {$category->getName()} : {$nbMoviesInCategory} film(s)";
                    $output->writeln($line);
                    $content .= $line . "\n";
                }
            }
        }

        // Nombre d'images et poids
        if ($type === 'all' || $type === 'images') {
            try {
                $nbImages = $this->mediaObjectRepository->count([]);
                $line = "Nombre d'images : {$nbImages}";
                $output->writeln($line);
                $content .= $line . "\n";

                // Calculer le poids total
                $totalSize = 0;
                $mediaObjects = $this->mediaObjectRepository->findAll();
                
                foreach ($mediaObjects as $mediaObject) {
                    if ($mediaObject->getFilePath()) {
                        $filePath = $this->projectDir . '/public/media/' . $mediaObject->getFilePath();
                        if (file_exists($filePath)) {
                            $totalSize += filesize($filePath);
                        }
                    }
                }

                $totalSizeMo = round($totalSize / 1024 / 1024, 2);
                $line = "Poids total des images : {$totalSizeMo} Mo";
                $output->writeln($line);
                $content .= $line . "\n";
            } catch (\Exception $e) {
                $line = "Impossible de récupérer les statistiques des images (table non créée)";
                $output->writeln($line);
                $content .= $line . "\n";
            }
        }

        // Écrire dans un fichier si l'option est fournie
        if ($logFile) {
            $filePath = $logFile;
            if (!str_starts_with($filePath, '/')) {
                $filePath = $this->projectDir . '/' . $filePath;
            }
            
            $timestamp = date('Y-m-d H:i:s');
            $fileContent = "=== Statistiques générées le {$timestamp} ===\n";
            $fileContent .= $content;
            
            file_put_contents($filePath, $fileContent, FILE_APPEND);
            $output->writeln('');
            $output->writeln("Statistiques enregistrées dans : {$filePath}");
        }

        return Command::SUCCESS;
    }
}
