<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles,
            ]);
    }

    /**
     * @Route("/", name="home")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home()
    {
        return $this->render('blog/home.html.twig', ['title'=> "Bienvenue les amis"]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/blog/new", name="blog_new")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, ObjectManager $manager)
    {

        if(!$article)
        {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if(!$article->getId())
            {
                $article->setCreateAt(new \DateTime());
            }
            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id'=> $article->getId()]);

        }

        return $this->render('blog/create.html.twig',
        ['formArticle' =>$form->createView(),
            'editMode' => $article->getId() !== null,
        ]
        );
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show(UserInterface $user, Article $article, Comment $comment = null, Request $request, ObjectManager $manager)
    {

        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article = $repo->find($article);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        dump($form);
        if ($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTime());
            $comment->setArticle($article);
            $comment->setAuthor($user->getUsername());
            //dd($comment);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('blog_show', ['id'=> $article->getId()]);

        }

        return $this->render('blog/show.html.twig',
            ['article' => $article,
            'formComment' => $form->createView(),
            ]);
    }

}
