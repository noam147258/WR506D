<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use App\Entity\Movie;
use App\Entity\Category;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Xylis\FakerCinema\Provider\Movie as FakerMovie;
use Xylis\FakerCinema\Provider\Person as FakerPerson;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new FakerMovie($faker));
        $faker->addProvider(new FakerPerson($faker));
        
        $categories = [];
        foreach (['Action', 'Comédie', 'Drame', 'Science-Fiction', 'Horreur'] as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setCreatedAt(new DateTimeImmutable());
            $manager->persist($category);
            $categories[] = $category;
        }
        
        $actors = [];
        for ($i = 0; $i < 30; $i++) {
            $actor = new Actor();
            $actor->setFirstname($faker->firstName());
            $actor->setLastname($faker->lastName());
            $actor->setCreatedAt(new DateTimeImmutable());
            $manager->persist($actor);
            $actors[] = $actor;
        }
        
        for ($i = 0; $i < 50; $i++) {
            $movie = new Movie();
            /** @phpstan-ignore-next-line */
            $movie->setName($faker->movie());
            $movie->setOnline($faker->boolean(70)); // 70% de chance d'être online
            $movie->setCreatedAt(new DateTimeImmutable());
            $movie->addCategory($faker->randomElement($categories));
            $movie->addActor($faker->randomElement($actors));
            $manager->persist($movie);
        }
        
        $manager->flush();
    }
}
