<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Incredient;
use App\Repository\IncredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecipeType extends AbstractType
{
    private $token;
    public function __construct(TokenStorageInterface $token)
    {
        $this->token = $token;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'min-length' => 2,
                    'max-length' => 50
                ],
                'label' => 'nom',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Length(['min' => 2, 'max' => 50]),
                    new Assert\NotBlank()
                ]
            ])
            ->add('price', MoneyType::class, [
                'attr' => [
                    'class' => 'form-control',

                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(1001)
                ]
            ])
            
            ->add('time', IntegerType::class,[
                'attr'=>[
                    'class'=>'form-control',
                    'min' => 1,
                    'max' => 1440,
                ],
                'required' => false,
                
                'label' => 'Temp(en minutes)',
                'label_attr' => [
                    'class' => 'form-label mt-4'],
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(1441)
                ]
            ])
            ->add('nbPeople', IntegerType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 50,
                ],

                'label' => 'Nombre de personnes',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required' => false,
                'constraints' => [
                    new Assert\Positive(),
                    new Assert\LessThan(51)
                ]
            ])
            ->add('difficulty', RangeType::class, [
            'attr' => [
                'class'=>'form-range',
                'min' => 1,
                'max' => 5,
            ],

            'label' => 'Difficulté',
            'label_attr' => [
                'class' => 'form-label mt-4'
            ],
            'constraints' => [
                new Assert\Positive(),
                new Assert\LessThan(6)
            ]
        ])
            ->add('description', TextareaType::class,[
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 5,
                ],

                'label' => 'Description',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                   
                ]
            ])
            
            
            ->add('isFavorite',CheckboxType::class,[
                'attr' => [
                    'class' => 'form-check-input mt-4',
                    
                ],
                'required'=> false,

                'label' => 'Favoris?',
                'label_attr' => [
                    'class' => 'form-check-label mt-4'
                ],
                'constraints' => [
                    new Assert\NotNull(),
                    
                ]
            ])
            
            ->add('ingredients', EntityType::class,[
                
                'class'=> Incredient::class,
                'choice_label'=> 'name',
                'query_builder' => function (IncredientRepository $er) {
                    return $er->createQueryBuilder('i')
                        ->where('i.user = :user')
                        ->setParameter('user',$this->token->getToken()->getUser())

                        ->orderBy('i.name', 'ASC');
                },
                'multiple' => true,
                'expanded' => true,

                'label' => 'Ingredients',
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
               
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => "Image de la recette",
                'label_attr' => [
                    'class' => 'form-label mt-4'
                ],
                'required'=>false
            ])
            ->add('submit',SubmitType::class,[
                'attr'=>[
                    'class'=>'btn btn-primary mt-4'
                ],
                'label'=> 'Créer ma recette'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
