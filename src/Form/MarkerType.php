<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form;

use App\Entity\Marker;
use App\Form\Type\DateTimePickerType;
use App\Form\Type\TagsInputType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Defines the form used to create and manipulate feeds.
 *
 * @author Ryan Weaver <weaverryan@gmail.com>
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
final class MarkerType extends AbstractType
{
    // Form types are services, so you can inject other services in them if needed
    public function __construct(
        //private readonly SluggerInterface $slugger
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // For the full reference of options defined by each form field type
        // see https://symfony.com/doc/current/reference/forms/types.html

        // By default, form fields include the 'required' attribute, which enables
        // the client-side form validation. This means that you can't test the
        // server-side validation errors from the browser. To temporarily disable
        // this validation, set the 'required' attribute to 'false':
        // $builder->add('title', null, ['required' => false, ...]);

        $builder
            ->add('title', null, [
                'attr' => ['autofocus' => true],
                'label' => 'label.title',
            ])
            ->add('description', TextareaType::class, [
                'help' => 'help.marker_summary',
                'label' => 'label.description',
                'required' => false,
            ])
            ->add('date', DateTimePickerType::class, [
                'label' => 'label.marker_date',
                'help' => 'help.marker_publication',
            ])
            ->add('link', null, [
                'label' => 'label.link',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marker::class,
        ]);
    }
}