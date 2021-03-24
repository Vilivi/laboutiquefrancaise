<?php

namespace App\Controller;

use App\Classe\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart)
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        foreach ($cart->getFull() as $article){
            $products_for_stripe[] = ['price_data' => [
                'currency' => 'eur',
                'unit_amount' => $article['product']->getPrice(),
                'product_data' => [
                    'name' => $article['product']->getName(),
                    'images' => [$YOUR_DOMAIN."/uploads/.".$article['product']->getIllustration()],
                ],
                ],
                'quantity' => $article["quantity"],];
        }
        Stripe::setApiKey('sk_test_51IYYqQETPc1xlEzCZOLtKikkXG8AO3zUCT6ddulOutaVWTsOsFoP7K5hHx79X1ql7MfMEKMYqnJHixzGwDUuYtJI00E4xW0zfB');

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
