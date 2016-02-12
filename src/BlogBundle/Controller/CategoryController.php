<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Category;
use BlogBundle\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CategoryController extends Controller
{

    public function newAction(Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Unable to access this page!');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $article = new Article();
        $article->setAuthor($user);

        $form = $this->createFormBuilder($article)
            ->add('name', TextType::class)
            ->add('content', TextType::class)
            ->add('category', EntityType::class, array(
                'class' => 'BlogBundle:Category',
                'choice_label' => 'name',
            ))
            ->add('date', DateTimeType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush($article);
            return $this->redirectToRoute('blog_article', ['id' => $article->getId()]);
        }

        return $this->render('BlogBundle:Article:new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function categoriesAction(Request $request, $category_name)
    {
        $category = $this->getDoctrine()->getRepository('BlogBundle:Category')->findOneBy(['name' => $category_name]);
        if($category)
        {
            $articles = $this->getDoctrine()->getRepository('BlogBundle:Article')->findByCategory($category);
        }else {
            $articles = null;
        }
        return $this->render('BlogBundle:Article:articles.html.twig',[
            'articles' => $articles,

        ]);
    }

    public function searchAction(Request $request, $search)
    {
        $articles_by_name = $this->get('search')->searchArticle($search);
        $articles_by_tag = $this->get('search')->searchTag($search);
//        print_r($articles_by_tag);
//        $search_form = $this->createFormBuilder()
//            ->add('name', TextType::class)
//            ->add('save', SubmitType::class, array('label' => 'Rechercher'))
//            ->getForm();
        $search_form = $this->createForm(SearchType::class);
        return $this->render('BlogBundle:Article:search.html.twig',[
            'search_form'   => $search_form->createView(),
            'articles_by_name'      => $articles_by_name,
            'articles_by_tag'       => $articles_by_tag,
        ]);
    }
}
