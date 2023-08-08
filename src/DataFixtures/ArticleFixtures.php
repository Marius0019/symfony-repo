<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    /**
     *  @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for($j = 1; $j <= 3 ; $j++  )
        {
            $category = new Category;
            $category->setTitle($this->faker->sentence(1))
                    ->setDescription($this->faker->paragraph());
            $manager->persist($category);

            
            for($i=1; $i <= mt_rand(8, 12); $i++)
            {
                $article = new Article;
    
                $article->setTitle($this->faker->sentence())
                        ->setImage($this->faker->imageUrl())
                        ->setContent($this->faker->paragraph(200))
                        ->setCreatedAt($this->faker->dateTimeBetween('-10 months'))
                        ->setCategory($category);
                        
                $manager->persist($article);

                for($k= 1; $k <= mt_rand(4, 10); $k++)
                {
                    $comment = new Comment;

                    //* on récupère la date d'aujourd'hui
                    $now = new \DateTime();
                    //* on fait la différence entre la date d'aujourd'hui et la date de création de notre article
                    $interval = $now->diff($article->getCreatedAt());
                    //* on récupère la différence en nombre de jours
                    $days = $interval->days;
                    $minimum = '-' . $days . ' days';

                    $comment->setContent($this->faker->name)
                            ->setContent($this->faker->paragraph())
                            ->setArticle($article)
                            ->setCreatedAt($this->faker->dateTimeBetween($minimum));
                    $manager->persist($comment);
                }

            }
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
