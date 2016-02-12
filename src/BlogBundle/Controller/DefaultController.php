<?php

namespace BlogBundle\Controller;


use BlogBundle\Entity\Article;
use BlogBundle\Form\Type\ArticleType;
use BlogBundle\Form\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $articles = $this->getDoctrine()->getRepository('BlogBundle:Article')->getLast(5);
        $search_form = $this->createForm(SearchType::class);
        $search_form->handleRequest($request);

        if ($search_form->isValid()) {
            $search = (string)$request->request->get('search')['search'];
            print_r($search);
            return $this->redirectToRoute('blog_article_search', ['search' => $search]);
        }
        return $this->render('BlogBundle:Default:index.html.twig',[
            'articles' => $articles,
            'search_form' => $search_form->createView()
        ]);
    }

    public function administrationAction()
    {
        $form_article = $this->createForm(ArticleType::class, new Article(), array(
            'action' => $this->generateUrl('blog_article_new'),
            'method' => 'POST',
        ));

        return $this->render('BlogBundle:Default:administration.html.twig', [
            'form_article' => $form_article->createView()
        ]);
    }
}
