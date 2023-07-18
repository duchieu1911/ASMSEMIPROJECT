<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine ->getRepository('App\Entity\Category')->findAll();
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products'=> $products,
            'categories'=> $categories
        ]);
    }
}
