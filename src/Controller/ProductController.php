<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Reference;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository('App\Entity\Category')->findAll();
        $products = $doctrine->getRepository('App\Entity\Product')->findAll();
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            'products' => $products,
            'categories' => $categories
        ]);
    }

#[route ('/product/details/{id}', name: 'product_details')]
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $products = $doctrine->getRepository('App\Entity\Product')->find($id);
        return $this->render ('product/detail.html.twig',['product'=>$products]);
    }

#[route ('/', name: 'product_delete')]
    public function deleteAction(ManagerRegistry $doctrine, $id)
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository('App:Product')->find($id);
        $em->remove($product);
        $em->flush();

        $this->addFlash(
            'error',
            'Product deleted'
        );

        return $this->redirectToRoute('product_list');
    }

    #[route('/', name: 'product_edit')]
    public function editAction(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, @$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_list', [
                'id' => $product->getId()
            ]);

        }


        return $this->renderForm('product/edit.html.twig', ['form' => $form,]);
    }

}