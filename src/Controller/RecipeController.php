<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use App\Repository\IncredientRepository;
use App\Repository\MarkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    /**
     * This function displays all the recipes
     *
     * @param Request $request
     * @param RecipeRepository $recipeRepository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/recette', name: 'recipe_index')]
    public function index(Request $request, RecipeRepository $recipeRepository, PaginatorInterface $paginator): Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findBy(['user'=>$this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    #[Route('/recette/public',name: 'recipe_index_public', methods:['GET'])]
    public function indexPublic(Request $request,PaginatorInterface $paginator, RecipeRepository $recipeRepository):Response
    {
        $recipes = $paginator->paginate(
            $recipeRepository->findPublicRecipe(null),
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        return $this->render('pages/recipe/index_public.html.twig',[
            'recipes'=>$recipes
        ]);
    }

    #[Security("is_granted('ROLE_USER') and (recipe.isIsPublic() === true || user === recipe.getUser())")]
    #[Route('/recette/{id}',name:'recipe_show',methods:['GET','POST'])]
    public function show(Recipe $recipe,Request $request, EntityManagerInterface $em, MarkRepository $markRepository){
        $mark = new Mark;
        $form = $this->createForm(MarkType::class,$mark);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);
           
            $existingMark = $markRepository->findOneBy([
                'user'=>$this->getUser(),
                'recipe'=>$recipe
            ]);

            if(!$existingMark){
                 $em->persist($mark);
               
              
            }else{
                $existingMark->setMark($form->getData()->getMark());
                
            }
        $em->flush();

        $this->addFlash(
            'success',
            'Votre note a bien été prise en compte'
        );

        return $this->redirectToRoute('recipe_show',[
            'id'=>$recipe->getId()
        ]);

        }
        return $this->render('pages/recipe/show.html.twig',[
            'recipe'=>$recipe,
            'form'=>$form->createView()
        ]);
    }

    /**
     * This function allow us to create a new recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param IncredientRepository $incredientRepository
     * @return void
     */
    #[Route('/recipe/nouveau', name: 'recipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, IncredientRepository $incredientRepository)
    {
        $recipe = new Recipe;
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $recipe->setUser($this->getUser());
            $em->persist($recipe);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre recette a été crée avec success!'
            );

            return $this->redirectToRoute('recipe_index', []);
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * This function allow us to edit a  recipe
     *
     * @param Request $request
     * @param EntityManagerInterface $em
    
     * @return void
     */
    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recipe/edition/{id}', name: 'recipe_edit', methods: ['GET', 'POST'])]

    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response
    {

        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $em->persist($ingredient);
            $em->flush();
            $this->addFlash(
                'success',
                'Votre ingrédient a été modifié avec success!'
            );

            return $this->redirectToRoute('recipe_index', []);
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * This fonction allow to delete a recipe     *
     * @param Recipe $recipe
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/recipe/delete/{id}', name: 'recipe_delete')]
    
    public function delete(Recipe $recipe, EntityManagerInterface $em): Response
    {

        $em->remove($recipe);
        $em->flush();
        $this->addFlash(
            'success',
            'La recette en question a  été bien supprimé.'
        );
        return $this->redirectToRoute('recipe_index', []);
    }
}
