<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    
    
    /**
     * This fonction allow us to edit le profile of user     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'user_edit')]
    public function index(UserPasswordHasherInterface $hasher,User $chosenUser,Request $request,EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class,$chosenUser);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($chosenUser, $form->getData()->getPlainPassword())){
                $chosenUser = $form->getData();
                $em->persist($chosenUser);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Le profile de l\'utilisateur a été bien modifié'
                );
                return $this->redirectToRoute('recipe_index');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
            

        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * This fonction allow us to edit le mot de passe of a user
     *
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param User $user
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === chosenUser")]
    #[Route('/utilisateur/edition_mot_de_passe/{id}', name: 'user_password_edit')]
    public function editPassword(EntityManagerInterface $em,Request $request, User $chosenUser, UserPasswordHasherInterface $hasher):Response
    {
      
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($hasher->isPasswordValid($chosenUser,$form->getData()['plainPassword'])){
                $chosenUser->setUpdatedAt(new \DateTimeImmutable());
                $chosenUser->setPlainPassword($form->getData()['newPassword']);
              
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Le mot de passe de l\'utilisateur a été bien modifié'
                );

                return $this->redirectToRoute('recipe_index');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }
       
        return $this->render('pages/user/edit_password.html.twig',[
            'form'=>$form->createView()
        ]);
    }
}
