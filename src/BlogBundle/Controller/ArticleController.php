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

    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('BlogBundle:Article')->findOneBy(['id' => $id]);
        $em->remove($article);
        $em->flush();
        return $this->redirectToRoute('blog_administration', ['article_delete' => true]);

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
//        $articles = $this->getDoctrine()->getRepository('BlogBundle:Article')->findAll();
        if(isset($_GET['p']))
        {
            $page = $_GET['p'];
        } else{
            $page = 1;
        }

        $articles = $this->get('blog.pagination')->getArticles(10,$page);

        $pagination =  $this->get('blog.pagination')->getPagination(10);

        return $this->render('BlogBundle:Article:articles.html.twig',[
            'articles'      => $articles,
            'pagination'    => $pagination
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
        $search_form->handleRequest($request);

        if ($search_form->isValid()) {
            $search = (string)$request->request->get('search')['search'];
            return $this->redirectToRoute('blog_article_search', ['search' => $search]);
        }
        return $this->render('BlogBundle:Article:search.html.twig',[
            'search_form'           => $search_form->createView(),
            'articles_by_name'      => $articles_by_name,
            'articles_by_tag'       => $articles_by_tag,
        ]);
    }
}
