<?php

/*
 * @copyright   2019 Jovan Marcovic. All rights reserved
 * @author      Jovan Marcovic
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

return [
    'name'        => 'CropImage',
    'description' => 'Enables Crop & Upload Image',
    'version'     => '1.0',
    'author'      => 'Jovan Marcovic',

    'routes' => [
        'public' => [
            'mautic_cropimage_upload' => [
                'path'       => '/cropimage/upload',
                'controller' => 'MauticCropimageBundle:Default:upload',
            ],
            'mautic_cropimage_get_message' => [
                'path'       => '/cropimage/getmessage',
                'controller' => 'MauticCropimageBundle:Default:getmessage',
            ],
        ],
    ],

    'services' => [
        'events' => [
            'mautic.cropimage.event_listener.form_subscriber' => [
                'class'     => \MauticPlugin\MauticCropimageBundle\EventListener\FormSubscriber::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.integration',
                    'mautic.model.factory',
                ],
            ],
        ],
        'forms' => [
            'mautic.form.type.cropimage' => [
                'class' => \MauticPlugin\MauticCropimageBundle\Form\Type\CropimageType::class,
                'alias' => 'cropimage',
                'arguments' => [
                    'mautic.helper.core_parameters',
                    'translator',
                ],
            ],
        ],
    ],
];
