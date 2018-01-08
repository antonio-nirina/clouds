<?php

namespace AdminBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignDateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("date_launch", DateTimeType::class, array(
            "label" => false,
            'date_widget' => "single_text",
            'time_widget' => "choice",
            'with_seconds' => false,
            'html5' => false,
            'date_format'=>"dd/MM/yyyy hh:mm"
        ));
    }
}
