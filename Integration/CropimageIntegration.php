<?php

/*
 * @copyright   2019 Jovan Marcovic. All rights reserved
 * @author      Jovan Marcovic
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCropimageBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

/**
 * Class CropimageIntegration.
 */
class CropimageIntegration extends AbstractIntegration
{
    const INTEGRATION_NAME = 'Cropimage';

    public function getName()
    {
        return self::INTEGRATION_NAME;
    }

    public function getDisplayName()
    {
        return 'CropImage';
    }

    public function getAuthenticationType()
    {
        return 'none';
    }

    public function getRequiredKeyFields()
    {
        return [];
    }
}
