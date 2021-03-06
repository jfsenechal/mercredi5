<?php

namespace AcMarche\Mercredi\Jour\Form;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Form\Type\ArchivedType;
use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class JourType extends AbstractType
{
    /**
     * @var string
     */
    private const LABEL = 'label';
    /**
     * @var string
     */
    private const REQUIRED = 'required';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    self::LABEL => 'Date du jour de garde',
                ]
            )
            ->add(
                'pedagogique',
                CheckboxType::class,
                [
                    self::LABEL => 'Journée pédagoque',
                    self::REQUIRED => false,
                    'label_attr' => ['class' => 'switch-custom'],
                ]
            )
            ->add(
                'archived',
                ArchivedType::class,
                [
                    'help' => 'En archivant la date ne sera plus proposée lors de l\'ajout d\'une présence',
                ]
            )
            ->add(
                'ecoles',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'query_builder' => function (EcoleRepository $ecoleRepository) {
                        return $ecoleRepository->getQbForListing();
                    },
                    'help' => 'Pour les journées pédagogiques',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Remarques',
                    'help' => 'Cette donnée est visible par les parents et dans le listing des présences',
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}
