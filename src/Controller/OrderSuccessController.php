<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
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

        // modifier le status state à 1 maintenant que le paiement a bien été reçu.
        // vider la session cart
        if(!$order->getState() == 0){
            $cart->remove();
            $order->setState(1);
            $this->entityManager->flush();
        }
        //envoyer un mail au client pour lui confirmer sa commande.
        $mail = new Mail();
        $content = "Bonjour ". $order->getUser()->getFirstName() .", <hr> merci pour ta commande sur mon super site blablablabla"; 
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), "Votre commande sur La Boutique Française est bien validée", $content);
        //afficher les informations de la commande de l'utilisateur.
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
