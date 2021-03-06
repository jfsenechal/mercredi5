<?php

namespace AcMarche\Mercredi\Facture\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'tuteur',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom du tuteur'],
                ]
            )
            ->add(
                'paye',
                ChoiceType::class,
                [
                    'label' => 'Payé',
                    'choices' => ['Payée' => 1, 'Non payée' => 0],
                    'required' => false,
                ]
            );
    }
}
