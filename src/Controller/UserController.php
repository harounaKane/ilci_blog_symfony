<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route ("/", name="home")
     */
    public function home(){
        $prenom = "Dupond";
        $nom = "Jeans";

        return $this->render("user/index.html.twig", [
            "prenomUser" => $prenom,
            "nom" => $nom
        ]);
    }

    /**
     * @Route("/inscription/blog", name="inscription", methods={"GET", "POST"})
     */
    public function inscription(Request $request, EntityManagerInterface $manager){

        $user = new User();
        //création formulaire
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ){
            $user->setStatut("utilisateur");
            $user->setCreateAt(new \DateTime());
            $user->setMdp(password_hash($user->getMdp(), PASSWORD_DEFAULT));

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render("user/inscription.html.twig", ["form" => $form->createView()]);
    }

}