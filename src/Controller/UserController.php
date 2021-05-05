<?php


namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager){
        $this->manager = $manager;
    }

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
    public function inscription(Request $request){

        $user = new User();
        //création formulaire
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ){
            $user->setStatut("utilisateur");
            $user->setCreateAt(new \DateTime());
            $user->setMdp(password_hash($user->getMdp(), PASSWORD_DEFAULT));

            try {
                $this->manager->persist($user);
                $this->manager->flush();
                $this->addFlash(
                    "success",
                    "Inscription réussie avec succès !"
                );
            }catch (UniqueConstraintViolationException $constraintViolationException){
                $this->addFlash(
                    "warning",
                    "Impossible de dupliquer le login"
                );
                return $this->render("user/inscription.html.twig", ["form" => $form->createView()]);
            }

            return $this->redirectToRoute('home');
        }

        return $this->render("user/inscription.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route ("/connexion/membre", name="connexion", methods={"GET", "POST"})
     */
    public function connexion(Request $request, UserRepository $repository){

        $user = new User();

       if( $request->request->get("login") ){
           $login = $request->request->get("login");
           $mdp = $request->request->get("mdp");

           $user = $repository->connexionUser($login, $mdp);

           if( $user ){
               $session = $request->getSession();
               $session->set("id", $user->getid());
               $session->set("login", $user->getLogin());
               $session->set("prenom", $user->getPrenom());
               $session->set("nom", $user->getNom());

               return $this->redirectToRoute('home');
           }

       //    dd($user);
       }

        return $this->render('user/connexion.html.twig');
    }

    /**
     * @Route ("/deconnexion/membre", name="deconnexion")
     */
    public function deconnexion(Request $request){
        $session = $request->getSession();
        $session->clear();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route ("/liest_des_membres/blog", name="listeMembre")
     */
    public function listeMembre(){
        $membres = $this->manager->getRepository(User::class)->findAll();

        //return $this->render('user/liste.html.twig', [compact($membres)]);
        return $this->render('user/liste.html.twig', ["membres" => $membres]);
    }

    /**
     * @Route ("/delete/{id}", name="delete")
     */
    public function delete($id){
        $membre = $this->manager->getRepository(User::class)->find($id);

        if( $membre ){
            $this->manager->remove($membre);
            $this->manager->flush();

            $this->addFlash(
                "success",
                "Suppression réussie avec succès !"
            );
        }else{
            $this->addFlash(
                "warning",
                "Suppression not possible !"
            );
        }
        return $this->redirectToRoute('listeMembre');
    }

    /**
     * @Route ("/membre/{id}/update", name="edit")
     */
    public function edit(User $user, Request $request){
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid() ){
            try {
                $user->setMdp( password_hash($user->getMdp(), PASSWORD_DEFAULT) );
                $this->manager->persist($user);
                $this->manager->flush();

                $this->addFlash(
                    "success",
                    "Mise à jour réussie avec succès !"
                );
                return $this->redirectToRoute('listeMembre');
            }catch (UniqueConstraintViolationException $constraintViolationException){
                $this->addFlash(
                    "warning",
                    "Impossible de dupliquer le login"
                );
            }
        }
        return $this->render("user/inscription.html.twig", ["form" => $form->createView()]);
    }

}