<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function listProducts(): Response
    {
        return $this->render('product/list.html.twig');
    }

    #[Route('/product/{id}', name: 'product_view', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function viewProduct(Request $request): Response
    {
        $id = $request->attributes->get('id'); // récupéré depuis l’URL
        return $this->render('product/view.html.twig', ['id' => $id]);
    }
}
