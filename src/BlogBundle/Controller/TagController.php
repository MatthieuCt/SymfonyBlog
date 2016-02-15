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

class TagController extends Controller
{

    public function deleteAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();
        $tag = $em->getRepository('BlogBundle:Tag')->findOneBy(['id' => $id]);
        foreach($tag->getArticles() as $article)
        {
            $tag->removeArticle($article);
            $article->removeTag($tag);
        }
        $em->remove($tag);
        $em->flush();
        return $this->redirectToRoute('blog_administration', ['tag_delete' => true]);

    }

}
