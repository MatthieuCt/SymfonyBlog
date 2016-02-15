<?php
namespace BlogBundle\Services;

use BlogBundle\Entity\Article;
use BlogBundle\Entity\Category;
use BlogBundle\Entity\Comment;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class PaginationManager
{

    private $doctrine;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getArticles($number,$page)
    {
        $repository = $this->doctrine->getRepository('BlogBundle:Article');
        $allArticles = $repository->countAll()[0][1];

        if ($number >= $allArticles)
        {
            $articles = $repository->findBy(array(), array('id' => 'ASC'));
        } elseif ($page == 1) {
            $articles = $repository->findBy(array(),array('id' => 'ASC'),$number);
        } else {
            $articles = $repository->findBy(array(),array('id' => 'ASC'),$number,$number*$page-$number);
        }

        return $articles;
    }

    public function getArticlesByCategory($number,$page, Category $category)
    {
        $repository = $this->doctrine->getRepository('BlogBundle:Article');
        $allArticles = $repository->countAllByCat($category)[0][1];

        if ($number >= $allArticles)
        {
            $articles = $repository->findBy(array('category' => $category), array('id' => 'ASC'));
        } elseif ($page == 1) {
            $articles = $repository->findBy(array('category' => $category),array('id' => 'ASC'),$number);
        } else {
            $articles = $repository->findBy(array('category' => $category),array('id' => 'ASC'),$number,$number*$page-$number);
        }

        return $articles;
    }

    public function getPagination($number)
    {
        $repository = $this->doctrine->getRepository('BlogBundle:Article');
        $allArticles = $repository->countAll()[0][1];

        $maxPages = $allArticles/$number;

        if ($maxPages <= 1)
        {
            $maxPages = 1;
        } elseif ($allArticles%$number !== 0)
        {
            $maxPages = ceil($maxPages);
        }

        return $maxPages;
    }

    public function getCategoryPagination($number, Category $category)
    {
        $repository = $this->doctrine->getRepository('BlogBundle:Article');
        $allArticles = $repository->countAllByCat($category)[0][1];

        $maxPages = $allArticles/$number;

        if ($maxPages <= 1)
        {
            $maxPages = 1;
        } elseif ($allArticles%$number !== 0)
        {
            $maxPages = ceil($maxPages);
        }

        return $maxPages;
    }
}