<?php

namespace App\Controller;

use App\Entity\Incredient;
use App\Form\IngredientType;
use App\Repository\IncredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class IngredientController extends AbstractController
{   
    /**
     * This function displays all ingredients
     *
     * @param Request $request
     * @param IncredientRepository $incredientRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient', name: 'ingredient_index')]
    public function index(Request $request,IncredientRepository $incredientRepository, PaginatorInterface $paginator): Response
    {
        $ingredients = $paginator->paginate(
            $incredientRepository->findBy(['user'=>$this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
      
        return $this->render('pages/ingredient/index.html.twig', [
           'ingredients' => $ingredients
        ]);
    }
/**
 * this function show a form which create un ingredient
 *
 * @param Request $request
 * @param EntityManagerInterface $em
 * @param IncredientRepository $incredientRepository
 * @return void
 */
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient/nouveau', name: 'ingredient_new',methods:['GET','POST'])]
    public function new(Request $request,EntityManagerInterface $em,IncredientRepository $incredientRepository){
        $ingredient = new Incredient;
        $form = $this->createForm(IngredientType::class,$ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            $em->persist($ingredient);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre ingr??dient a ??t?? cr??e avec success!'
            );
            
            return $this->redirectToRoute('ingredient_index',[
                
            ]);

        }

        return $this->render('pages/ingredient/new.html.twig',[
            'form'=>$form->createView()
        ]);
    }

 #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
 #[Route('/ingredient/edition/{id}',name:'ingredient_edit', methods: ['GET', 'POST'])]
 
    public function edit(Incredient $ingredient, Request $request,EntityManagerInterface $em):Response
    {
       
       $form = $this->createForm(IngredientType::class,$ingredient);
       $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $em->persist($ingredient);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre ingr??dient a ??t?? modifi?? avec success!'
            );

            return $this->redirectToRoute('ingredient_index', []);
        }
        return $this->render('pages/ingredient/edit.html.twig',[
            'form'=>$form->createView()
        ]);
    }

#[Route('/ingredient/delete/{id}',name:'ingredient_delete')]
public function delete(Incredient $ingredient, EntityManagerInterface $em):Response{
    
    $em->remove($ingredient);
    $em->flush();
        $this->addFlash(
            'success',
            'L\'ingr??dient en question a  ??t?? bien supprim??.'
        );
    return $this->redirectToRoute('ingredient_index', []);

}
}
