<?php

namespace App\Controller;

use App\Classe\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
    
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();

            $search_email = $this->em->getRepository(User::class)->findOneByEmail($user->getEmail());
            if(!$search_email){
                $password = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword($password);
                $this->em->persist($user);
                $this->em->flush();

                $mail = new Mail();
                $content = "Bonjour ". $user->getUser()->getFirstName .", <hr> tu viens d'être inscrit sur mon super site blablablabla"; 
                $mail->send($user->getEmail(), $user->getFirstName(), "Bienvenue sur La Boutique Française", $content);
    
                $notification = "Votre inscription s'est très bien déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }         
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
