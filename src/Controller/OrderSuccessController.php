<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, Cart $cart): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        
        if(!$order || $order->getUser() != $this->getUser()){
            //si la commande n'existe pas ou si elle ne correspond pas aux commandes de l'utilisateur connecté
            return $this->redirectToRoute('home');
        }

        // modifier le status isPaid à 1 maintenant que le paiement a bien été reçu.
        // vider la session cart
        if(!$order->getIsPaid()){
            $cart->remove();
            $order->setIsPaid(1);
            $this->entityManager->flush();
        }
        //envoyer un mail au client pour lui confirmer sa commande.
        //afficher les informations de la commande de l'utilisateur.
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
