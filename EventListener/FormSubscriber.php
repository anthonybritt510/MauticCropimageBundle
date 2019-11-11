<?php

/*
 * @copyright   2019 Jovan Marcovic. All rights reserved
 * @author      Jovan Marcovic
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCropimageBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticCropimageBundle\Integration\CropimageIntegration;
use MauticPlugin\MauticCropimageBundle\CropimageEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Mautic\PluginBundle\Integration\AbstractIntegration;

class FormSubscriber extends CommonSubscriber
{
    const MODEL_NAME_KEY_LEAD = 'lead.lead';

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ModelFactory
     */
    protected $modelFactory;

    /**
     * @var string
     */
    protected $siteKey;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var boolean
     */
    private $cropimageIsConfigured = false;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param IntegrationHelper $integrationHelper
     * @param ModelFactory $modelFactory
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        IntegrationHelper $integrationHelper,
        ModelFactory $modelFactory
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->modelFactory    = $modelFactory;
        $integrationObject     = $integrationHelper->getIntegrationObject(CropimageIntegration::INTEGRATION_NAME);
        
        if ($integrationObject instanceof AbstractIntegration) {
            $keys            = $integrationObject->getKeys();
            // $this->siteKey   = isset($keys['site_key']) ? $keys['site_key'] : null;
            // $this->secretKey = isset($keys['secret_key']) ? $keys['secret_key'] : null;

            // if ($this->siteKey && $this->secretKey) {
            //     $this->cropimageIsConfigured = true;
            // }
        }
        $this->cropimageIsConfigured = true;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD         => ['onFormBuild', 0],
            CropimageEvents::ON_FORM_VALIDATE => ['onFormValidate', 0],
            CoreEvents::BUILD_MAUTIC_JS       => ['onBuildJs', 0]
        ];
    }

    /**
     * @param FormBuilderEvent $event
     */
    public function onFormBuild(FormBuilderEvent $event)
    {
        if (!$this->cropimageIsConfigured) {
            return;
        }

        $event->addFormField('plugin.cropimage', [
            'label'          => 'mautic.plugin.actions.cropimage',
            'formType'       => 'cropimage',
            'template'       => 'MauticCropimageBundle:Integration:cropimage.html.php',
            'builderOptions' => [
                'addLeadFieldList' => false,
                'addIsRequired'    => true,
                'addDefaultValue'  => false,
                'addSaveResult'    => true,
                'allow_extra_fields'=> true,
            ],
        ]);

        $event->addValidator('plugin.cropimage.validator', [
            'eventName' => CropimageEvents::ON_FORM_VALIDATE,
            'fieldType' => 'plugin.cropimage',
        ]);
    }

    /**
     * @param ValidationEvent $event
     */
    public function onFormValidate(ValidationEvent $event)
    {
        if (!$this->cropimageIsConfigured) {
            return;
        }
        return;
    }

    public function onBuildJs(BuildJsEvent $event) 
    {
        $js = "MauticJS.documentReady(function() {
            
        });";
        $event->appendJs($js, 'Cropimage');
    }
}
