<?php

namespace BlogBundle\Controller;


use BlogBundle\Entity\Article;
use BlogBundle\Entity\Category;
use BlogBundle\Entity\Tag;
use BlogBundle\Form\Type\ArticleType;
use BlogBundle\Form\Type\CategoryType;
use BlogBundle\Form\Type\SearchType;
use BlogBundle\Form\Type\TagType;
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
            return $this->redirectToRoute('blog_article_search', ['search' => $search]);
        }
        return $this->render('BlogBundle:Default:index.html.twig',[
            'articles' => $articles,
            'search_form' => $search_form->createView()
        ]);
    }

    public function administrationAction(Request $request)
    {

//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Unable to access this page!');
        $user = $this->get('security.token_storage')->getToken()->getUser();


        $articles = $this->getDoctrine()->getRepository('BlogBundle:Article')->findAll();
        $article = new Article();
        $article->setAuthor($user);
        $form_article = $this->createForm(ArticleType::class, $article);
        $form_article->handleRequest($request);
        if ($form_article->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush($article);

            return $this->redirectToRoute('blog_administration', ['article' => true]);
        }

        $categories = $this->getDoctrine()->getRepository('BlogBundle:Category')->findBy(array(), array('id' => 'ASC'));;
        $category = new Category();
        $form_category = $this->createForm(CategoryType::class, $category);
        $form_category->handleRequest($request);
        if ($form_category->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush($category);

            return $this->redirectToRoute('blog_administration', ['category' => true]);
        }

        $tags = $this->getDoctrine()->getRepository('BlogBundle:Tag')->findBy(array(), array('id' => 'ASC'));;
        $tag = new Tag();
        $form_tag = $this->createForm(TagType::class, $tag);
        $form_tag->handleRequest($request);
        if ($form_tag->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush($tag);

            return $this->redirectToRoute('blog_administration', ['tag' => true]);
        }

        return $this->render('BlogBundle:Default:administration.html.twig', [
            'articles'      => $articles,
            'form_article'  => $form_article->createView(),
            'categories'    => $categories,
            'form_category' => $form_category->createView(),
            'tags'          => $tags,
            'form_tag'      => $form_tag->createView(),
        ]);
    }
}
