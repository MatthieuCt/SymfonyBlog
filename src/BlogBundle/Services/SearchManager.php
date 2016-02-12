<?php
namespace BlogBundle\Services;

use BlogBundle\Entity\Article;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class SearchManager
{

    private $doctrine;


    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function searchArticle($search)
    {
        $repository = $this->doctrine->getRepository('BlogBundle:Article');

        $articles = $repository -> searchByName($search);
//        print_r($articles);

        return $articles;
    }

    public function searchTag($search)
    {

        $repository = $this->doctrine->getRepository('BlogBundle:Tag');
        $articles = $repository->getArticles($search);

        return $articles;
    }
}