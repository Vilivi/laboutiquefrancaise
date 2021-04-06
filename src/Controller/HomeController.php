<?php

namespace App\Controller;

use App\Entity\Header;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $headers = $this->em->getRepository(Header::class)->findAll();
        // phpinfo();
        $products = $this->em->getRepository(Product::class)->findByIsBest(1);
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headers' => $headers
        ]);
    }
}
