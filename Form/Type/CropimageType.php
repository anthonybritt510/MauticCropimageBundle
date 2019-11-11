<?php

/*
 * @copyright   2019 Jovan Marcovic. All rights reserved
 * @author      Jovan Marcovic
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCropimageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Mautic\CoreBundle\Helper\FileHelper;
use Mautic\CoreBundle\Form\DataTransformer\ArrayStringTransformer;
use Mautic\FormBundle\Validator\Constraint\FileExtensionConstraint;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

/**
 * Class CropimageType.
 */
class CropimageType extends AbstractType
{

    const PROPERTY_ALLOWED_FILE_EXTENSIONS = 'allowed_file_extensions';
    const PROPERTY_ALLOWED_FILE_SIZE       = 'allowed_file_size';
    const PROPERTY_RATIO_WIDTH             = 'crop_image_ratio_width';
    const PROPERTY_RATIO_HEIGHT            = 'crop_image_ratio_height';

    /** @var CoreParametersHelper */
    private $coreParametersHelper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(CoreParametersHelper $coreParametersHelper, TranslatorInterface $translator)
    {
        $this->coreParametersHelper = $coreParametersHelper;
        $this->translator           = $translator;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (empty($options['data'][self::PROPERTY_ALLOWED_FILE_EXTENSIONS])) {
            $options['data'][self::PROPERTY_ALLOWED_FILE_EXTENSIONS] = $this->coreParametersHelper->getParameter('allowed_extensions');
        }
        if (empty($options['data'][self::PROPERTY_ALLOWED_FILE_SIZE])) {
            $options['data'][self::PROPERTY_ALLOWED_FILE_SIZE] = $this->coreParametersHelper->getParameter('max_size');
        }

        if (empty($options['data'][self::PROPERTY_RATIO_WIDTH])) {
            $options['data'][self::PROPERTY_RATIO_WIDTH] = 16;
        }

        if (empty($options['data'][self::PROPERTY_RATIO_HEIGHT])) {
            $options['data'][self::PROPERTY_RATIO_HEIGHT] = 9;
        }

        $arrayStringTransformer = new ArrayStringTransformer();
        $builder->add(
            $builder->create(
                self::PROPERTY_ALLOWED_FILE_EXTENSIONS,
                TextareaType::class,
                [
                    'label'      => 'mautic.form.field.file.allowed_extensions',
                    'label_attr' => ['class' => 'control-label'],
                    'required'   => false,
                    'attr'       => [
                        'class'   => 'form-control',
                        'tooltip' => 'mautic.form.field.file.tooltip.allowed_extensions',
                    ],
                    'data'        => $options['data'][self::PROPERTY_ALLOWED_FILE_EXTENSIONS],
                    'constraints' => [new FileExtensionConstraint()],
                ]
            )->addViewTransformer($arrayStringTransformer)
        );

        $maxUploadSize = FileHelper::getMaxUploadSizeInMegabytes();
        $builder->add(
            self::PROPERTY_ALLOWED_FILE_SIZE,
            TextType::class,
            [
                'label'      => 'mautic.form.field.file.allowed_size',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'   => 'form-control',
                    'tooltip' => $this->translator->trans('mautic.form.field.file.tooltip.allowed_size', ['%uploadSize%' => $maxUploadSize]),
                ],
                'data'        => $options['data'][self::PROPERTY_ALLOWED_FILE_SIZE],
                'constraints' => [new LessThanOrEqual(['value' => $maxUploadSize])],
            ]
        );

        $builder->add(
            'placeholder',
            'text',
            [
                'label'      => 'mautic.form.field.form.property_placeholder',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
            ]
        );
        $builder->add(
            self::PROPERTY_RATIO_WIDTH,
            'text',
            [
                'label'      => 'mautic.integration.cropimage.crop_image_ratio_width',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
                'data'        => $options['data'][self::PROPERTY_RATIO_WIDTH],
            ]
        );
        $builder->add(
            self::PROPERTY_RATIO_HEIGHT,
            'text',
            [
                'label'      => 'mautic.integration.cropimage.crop_image_ratio_height',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => ['class' => 'form-control'],
                'required'   => false,
                'data'        => $options['data'][self::PROPERTY_RATIO_HEIGHT],
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cropimage';
    }

}
