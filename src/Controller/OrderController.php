<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart): Response
    {
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        
        $form = $this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(), 
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function addCart(Cart $cart, Request $request): Response
    {
        if(!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        
        $form = $this->createForm(OrderType::class, null, [
            'user' =>$this->getUser()
        ]);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $date = new DateTime();
            $reference = $date->format('dmY'). '-'.uniqid();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $deliverycontent = $delivery->getFirstName(). ' ' . $delivery->getLastName();

            if($delivery->getCompany()){
                $deliverycontent .= '<br/>'. $delivery->getCompany();
            }

            $deliverycontent .= '<br/>' . $delivery->getAddress();
            $deliverycontent .= '<br/>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $deliverycontent .= '<br/>' . $delivery->getCountry();

            //enregistrement de la commande Order
            $order = new Order();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreateAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($deliverycontent);
            $order->setState(0);

            $this->entityManager->persist($order);

            //enregistrement des produits OrderDetails
            foreach ($cart->getFull() as $article){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($article['product']->getName());
                $orderDetails->setQuantity($article["quantity"]);
                $orderDetails->setPrice($article['product']->getPrice());
                $orderDetails->setTotal($article['product']->getPrice() * $article['quantity']);
                $this->entityManager->persist($orderDetails);
            }
            // il faut d'abord persister Order, et les OrderDetails avant de les injecter dans la bdd, ensemble.
            $this->entityManager->flush();
            //on ne peut accéder à cette page que si le formulaire a bien été soumis et est valide.
            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $deliverycontent,
                'reference' => $order->getReference()
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
