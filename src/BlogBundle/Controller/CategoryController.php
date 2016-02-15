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

    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('BlogBundle:Category')->findOneBy(['id' => $id]);
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('blog_administration', ['category_delete' => true]);

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
