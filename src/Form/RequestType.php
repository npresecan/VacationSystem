<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\EndDateAfterStartDate;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Carbon\Carbon;
use App\Repository\HolidayRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RequestType extends AbstractType
{
    private HolidayRepository $holidayRepository;

    public function __construct(HolidayRepository $holidayRepository)
    {
        $this->holidayRepository = $holidayRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('startDate', DateType::class, [
            'widget' => 'single_text',
            'required' => true,
            'label' => 'Start date',
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new Assert\NotBlank(['message' => 'Start date is required.']),
            ],
        ])
        ->add('endDate', DateType::class, [
            'widget' => 'single_text',
            'required' => true,
            'label' => 'End date',
            'format' => 'yyyy-MM-dd',
            'constraints' => [
                new Assert\NotBlank(['message' => 'End date is required.']),
                new EndDateAfterStartDate(),
            ],
        ])
        ->add('comment', TextareaType::class, [
            'label' => 'Comment',
            'required' => false,
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'Submit request',
        ])
        ->add('_token', HiddenType::class, [
            'mapped' => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $request = $form->getData();

            if (!$request instanceof Request) {
                return;
            }

            if ($request->getStartDate() && $request->getEndDate()) {
                $startDate = Carbon::instance($request->getStartDate());
                $endDate = Carbon::instance($request->getEndDate());

                if ($endDate < $startDate) {
                    $form->get('endDate')->addError(new FormError('End date cannot be before start date.'));
                }
                
                $holidayDates = $this->holidayRepository->getHolidayDates();

                $numberOfDays = $startDate->diffInDaysFiltered(function (Carbon $date) use ($holidayDates) {
                    return !$date->isWeekend() && !in_array($date->format('Y-m-d'), $holidayDates);
                }, $endDate) + 1;

                $employee = $request->getEmployee();
                if ($employee && $numberOfDays > $employee->getVacationDays()) {
                    $form->get('startDate')->addError(new FormError('You do not have enough vacation days.'));
                } else {
                    $request->setNumberOfDays($numberOfDays);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
            'csrf_protection' => true, 
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'request_form',
        ]);
    }
}