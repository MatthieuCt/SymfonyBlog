<?php
namespace BlogBundle\Services;

use BlogBundle\Entity\Article;
use BlogBundle\Entity\Comment;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class CommentManager
{

    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function createComment(Article $article, $author, $content)
    {
        $comment = new Comment();

        $comment->setArticle($article);

        $comment->setAuthor($author);
        $comment->setContent($content);

        $comment->setDate(new \DateTime());

        // Sauvegarde
        $em = $this->doctrine->getManager();
        $em->persist($comment);
        $em->flush($comment);
    }
}