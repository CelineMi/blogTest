<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');


        //create 3 categories

        for($i =1; $i<=3; $i++)
        {
            $category = new Category();
            $category->setTitle($faker->sentence())
                     ->setDescription($faker->paragraph());
            $manager->persist($category);

        // create between 4 and 6 articles

            for($j = 1; $j <= mt_rand(4,6); $j++)
            {
                $article = new Article();
                $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '<p>';

                $article->setTitle($faker->sentence())
                    ->setContent($content)
                    ->setImage($faker->imageUrl())
                    ->setCreateAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);
                $manager->persist($article);

                // create between 4 and 6 comments

                for($k = 1; $k <= mt_rand(4,6); $k++)
                {
                    $content = '<p>' . join($faker->paragraphs(2), '</p><p>') . '<p>';
                    $now = new \DateTime();
                    $interval = $now->diff($article->getCreateAt());
                    $days = $interval->days;
                    $minimum = '-' . $days . 'days';

                    $comment = new Comment();
                    $comment->setAuthor($faker->name())
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween($minimum))
                            ->setArticle($article);
                    $manager->persist($comment);
                }
            }

        }

        $manager->flush();
    }
}
