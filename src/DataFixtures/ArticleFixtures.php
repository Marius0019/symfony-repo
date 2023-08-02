<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        
        for($i=1; $i <= 15; $i++)
{
    $article = new Article;

    $article->setTitle("Titre de l'aticle n°$i")
            ->setImage("https://picsum.photos/id/237/200/300")
            ->setContent("<p>Contenu de l'article n°$i")
            ->setCreatedAt(new \Datetime());
    $manager->persist($article);

}
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}