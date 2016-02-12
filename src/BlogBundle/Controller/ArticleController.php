<?php

namespace BlogBundle\Controller;

use BlogBundle\Entity\Category;
use BlogBundle\Entity\Comment;
use BlogBundle\Form\Type\SearchType;
use BlogBundle\Form\Type\CommentType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use BlogBundle\Entity\Article;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends Controller
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

    public function articleAction(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository('BlogBundle:Article')->find($id);
        $comment_form = $this->createForm(CommentType::class,new Comment(), array(
            'action' => $this->generateUrl('blog_comment',array('id' => $id)),
            'method' => 'POST',
        ));

        return $this->render('BlogBundle:Article:article.html.twig', [
            'article'       => $article,
            'comments'      => $article->getComments(),
            'comment_form'  => $comment_form->createView(),
        ]);
    }

    public function articlesAction(Request $request)
    {
        $articles = $this->getDoctrine()->getRepository('BlogBundle:Article')->findAll();

        return $this->render('BlogBundle:Article:articles.html.twig',[
            'articles' => $articles,

        ]);
    }



    public function commentAction(Request $request, $id)
    {
        if(isset($_POST['comment']['author']) && isset($_POST['comment']['content']))
        {
            $author = $_POST['comment']['author'];
            $content = $_POST['comment']['content'];
            $repository = $this
                ->getDoctrine()
                ->getRepository('BlogBundle:Article')
            ;
            $article = $repository->findOneBy(array('id'=>$id));

            $this->get('blog.comment')->createComment($article, $author, $content);

            return $this->redirect(
                $this->generateUrl('blog_article', array('id' => $id))
            );
        }



    }

    public function searchAction(Request $request, $search)
    {
        $articles_by_name = $this->get('blog.search')->searchArticle($search);
        $articles_by_tag = $this->get('blog.search')->searchTag($search);
        $search_form = $this->createForm(SearchType::class);
        return $this->render('BlogBundle:Article:search.html.twig',[
            'search_form'           => $search_form->createView(),
            'articles_by_name'      => $articles_by_name,
            'articles_by_tag'       => $articles_by_tag,
        ]);
    }
}
