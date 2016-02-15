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
        if(isset($_GET['p']))
        {
            $page = $_GET['p'];
        } else{
            $page = 1;
        }
        $category = $this->getDoctrine()->getRepository('BlogBundle:Category')->findOneBy(['name' => $category_name]);
        if($category)
        {
            $articles = $this->get('blog.pagination')->getArticlesByCategory(10,$page, $category);

            $pagination =  $this->get('blog.pagination')->getCategoryPagination(10, $category);
        }else {
            $articles = null;
            $pagination = null;
        }
        return $this->render('BlogBundle:Article:articles.html.twig',[
            'articles'      => $articles,
            'pagination'    => $pagination
        ]);
    }
}
