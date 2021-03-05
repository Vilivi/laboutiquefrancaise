<?php 

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart 
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->entityManager = $em;
    }

    public function add($id)
    {
        // on prend le panier, on le modifie, et on set le panier avec les nouvelles valeurs
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        // on prend le panier, on le modifie, et on set le panier avec les nouvelles valeurs
        $cart = $this->session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        $this->session->set('cart', $cart);
    }

    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $this->session->set('cart', $cart);
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function getFull()
    {
        $cartComplete = [];
        
        if($this->get()){
            foreach($this->get() as $id => $quantity){
                //Si l'id rentré ne correspond pas à un produit, on le supprime de la session avant de continuer
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

                if (!$product_object){
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'product' => $this->entityManager->getRepository(Product::class)->findOneById($id), 
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }
}