<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFull()
        ]);
    }
    
    /**
     * @Route("/cart/add/{id}", name="add_cart")
     */
    public function addToCart(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_cart")
     */
    public function decreaseCart(Cart $cart, $id): Response
    {
        $cart->decrease($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * Pour supprimer un article du panier
     * @Route("/cart/delete/{id}", name="delete_cart")
     */
    public function deleteAnArticleToCart(Cart $cart, $id): Response
    {
        $cart->delete($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * Pour supprimer l'ensemble du panier
     * @Route("/cart/remove/", name="remove_cart")
     */
    public function removeToCart(Cart $cart): Response
    {
        $cart->remove();
        return $this->redirectToRoute('products');
    }
}
