<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    //* une route est définie par 2 arguments : son chemin (/blog) dans l'url et son nom (app_blog)
    public function index(ArticleRepository $repo): Response
    {
        //*$repo est instance de la classe ArticleRepository et possède du cout les 4 méthodes de bases find(), findOneBy(), findAll(), findBy()
        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            "articles" =>  $articles
        ]);
        //* render() permet d'afficher le contenu d'un template. Elle va chercher directement dans le dossier template
    }

    #[Route('/', name:'home')]
    public function home() : Response
    {
        return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenu sur mon blog',
            'age' => 28,
        ]);
        // * dans le render en deuxième argument on peut envoyer des données dans la vue (twig) sous forme de tableau avec indice => valeur
        //* l'indice étant le nom de la variable dans le fichier twig et valeur sa valeur réel
    }

    #[Route('/blog/modifier/{id}', name:"blog_modifier")]
    #[Route('/blog/ajout', name:"blog_ajout")]
    public function form(Request $globals, EntityManagerInterface $manager, Article $article = null ) : Response
    {
        // dd($article);
        if($article == null)
        {
            $article =  new Article ;
        }
        //  dd($article);
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($globals);
        // * handleRequest() permet de récupérer tout les données de mes inputs
        if($form->isSubmitted() && $form->isValid())
        {
            // dd($globals->request);
            // $article->setTitle('un titre');
            $article->setCreatedAt(new \Datetime);
            // dd($article);
            //*persist() va permettre de préparer ma requete SQL a envoyer par rapport a l'objet donné en argument
            $manager->persist($article);
            //* flush() permettre d'executer tout les persist précédent
            $manager->flush();
            //* redirectToRoute() permet de rediriger vers une autre page de notre site a l'aide du nom de la route (name)
            return $this->redirectToRoute('blog_gestion');
        }
        
        return $this->render('blog/form.html.twig', [
            'formArticle' => $form,
            'editMode' => $article->getId() !== null
        ]);
    }

    #[Route('/blog/gestion', name:'blog_gestion')]
    public function gestion(ArticleRepository $repo) : Response
    {
        $articles = $repo->findAll();
        return $this->render('blog/gestion.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/blog/show/{id}', name:"blog_show")]
    public function show($id, ArticleRepository $repo)
    {
        $article = $repo->find($id) ;
        return $this->render('blog/show.html.twig', [
            'article' => $article,
        ]);
    }
    /**
     * !pour récupérer un article par son id on a 2 méthodes
     * *la première :
     *      *on a besoin de l'id en paramètre de la route 
     *         ! #[Route('/chemin/{id}', name:'nomRoute')]
     *      *on récupère la valeur de l'id dans la méthode et on récupère le Repository nécessaire
     *          ! public function nomFonction($id,   MonRepository $repo)
     *  *derrrière on peut utiliser la méthode find() de mon repo pour récupérer un élément avec son id
     *          ! $uneVariable = $repo->find($id);
     * *la deuxième :
     *      *on a besoin de l'id en paramètre de la Route
     *      ! #[Route('/chemin/{id}', name:'nomRoute')]
     *      * on va déclarer dans la méthode en paramètre l'entity que l'on veut récupérer
     *      ! public function nomFonction(MonEntity $monEntity)
     * 
     */  

    #[Route('/blog/supprimer/{id}', name:"blog_supprimer")]
    public function supprimer(Article $article, EntityManagerInterface $manager)
    {
        $manager->remove($article);
        $manager-> flush();    
        return $this->redirectToRoute('blog_gestion');
    }
}

