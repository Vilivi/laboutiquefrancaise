<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')){
            $user = $this->em->getRepository(User::class)->findOneByEmail($request->get('email'));
            
            if($user){
                // enregistrer en bdd la demande de resetPassword avec user, token et createdAt
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->em->persist($reset_password);
                $this->em->flush();

                //envoyer un email à l'utilisateur avec un email pour mettre à jour son mdp
                $mail = new Mail();
                $url = "http://127.0.0.1:8000";
                $url .= $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                    ]);
                $content = "Bonjour ". $user->getFirstName(). ", <br/> Vous avez demandé à réinitialiser votre mot de passe sur le site La Boutique Française. <br><br>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."' >mettre à jour votre mot de passe.</a>";
                $mail->send($user->getEmail(), $user->getFirstName(). ' ' .$user->getLastName(), 'Réinitialiser votre mot de passe sur La Boutique Française', $content);
                
                $this->addFlash('notice', 'Vous allez recevoir dans quelques seconde un mail avec la procédure pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update($token, Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $reset_password = $this->em->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password){
            return $this->redirectToRoute('home');
        }

        //verifier si le reset_password a été demandé il y a moins de 3heures. 
        $now = new \DateTime();
        if($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveler.');
            return $this->redirectToRoute('reset_password');
        }

        //render une vue avec mot de passe et confirmez votre mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData();

            //encodage des mots de passe
            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password); 

            //flush
            $this->em->flush();

            //redirection de l'utilisateur vers la page de connexion
            $this->addFlash('notice', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');
        }
        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
        
    }
}
