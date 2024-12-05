<?php

namespace App\Form;

use App\Entity\Approved;
use App\Entity\Employee;
use App\Entity\Request;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApprovedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateApprovedTl', null, [
                'widget' => 'single_text',
            ])
            ->add('dateApprovedPm', null, [
                'widget' => 'single_text',
            ])
            ->add('statusTeamLeader')
            ->add('statusProjectManager')
            ->add('request', EntityType::class, [
                'class' => Request::class,
                'choice_label' => 'id',
            ])
            ->add('teamLeader', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
            ->add('projectManager', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Approved::class,
        ]);
    }
}
