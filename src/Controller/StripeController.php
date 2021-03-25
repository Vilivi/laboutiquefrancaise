<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index($reference, EntityManagerInterface $entityManager)
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        if(!$order){
            new JsonResponse(['error' => 'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $article){
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($article->getProduct());
            $products_for_stripe[] = ['price_data' => [
                'currency' => 'eur',
                'unit_amount' => $article->getPrice(),
                'product_data' => [
                    'name' => $article->getProduct(),
                    'images' => [$YOUR_DOMAIN."/uploads/.".$product_object->getIllustration()],
                ],
                ],
                'quantity' => $article->getQuantity(),];
        }

        $products_for_stripe[] = ['price_data' => [
            'currency' => 'eur',
            'unit_amount' => $order->getCarrierPrice(),
            'product_data' => [
                'name' => $order->getCarrierName(),
                'images' => [$YOUR_DOMAIN],
            ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey('sk_test_51IYYqQETPc1xlEzCZOLtKikkXG8AO3zUCT6ddulOutaVWTsOsFoP7K5hHx79X1ql7MfMEKMYqnJHixzGwDUuYtJI00E4xW0zfB');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
