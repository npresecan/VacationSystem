<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Job;
use App\Entity\Role;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('birthDate', null, [
                'widget' => 'single_text',
            ])
            ->add('vacationDays', null, [
                'data' => 20,
                'disabled' => true, 
            ])
            ->add('email')
            ->add('username')
            ->add('password', PasswordType::class)
            ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'disabled' => true,
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'name',  
                'placeholder' => 'Choose a job',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
