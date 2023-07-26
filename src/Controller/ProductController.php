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
use function Symfony\Config\Framework\Workflows\type;


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
        return $this->render('product/detail.html.twig', ['product' => $products]);
    }

#[route ('/product/delete/{id}', name: 'product_delete')]
    public function deleteAction(ManagerRegistry $doctrine, $id)
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository('App\Entity\Product')->find($id);
        if (!is_null($product)) {


            $em->remove($product);
            $em->flush();

            $this->addFlash(
                'error',
                'Product deleted'
            );
        } else {
            $this->addFlash(
                'error',
                'product not existed!'
            );
        }
        return $this->redirectToRoute('app_product');
    }

    #[Route('/product/create', name:'create_product', methods:['GET', 'POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // uplpad file
            $productImage = $form->get('productImage')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setProductImage($newFilename);
            } else {
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'product Added'
            );
            return $this->redirectToroute('app_product');
        }

        return $this->renderForm('product/create.html.twig', ['form' => $form]);


    }




#[Route('/product/edit/{id}', name: 'product_edit')]
    public function editAction(ManagerRegistry $doctrine, int $id,Request $request,SluggerInterface $slugger): Response{
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, @$product);
        $form->handleRequest($request);
        $categories = $doctrine->getRepository('App\Entity\Category')->findAll();


        if ($form->isSubmitted() && $form->isValid()) {
            //upload file
            $productImage = $form->get('productImage')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setProductImage($newFilename);
            }else{
                $this->addFlash(
                    'notice',
                    'Edit success'
                );// ... handle exception if something happens during file upload
            }

            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('app_product', [
                'id' => $product->getId()
            ]);

        }
        return $this->renderForm('product/edit.html.twig', ['form' => $form]);
    }
}