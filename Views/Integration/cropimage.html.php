<?php

/*
 * @copyright   2019 Jovan Marcovic. All rights reserved
 * @author      Jovan Marcovic
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$defaultInputClass = (isset($inputClass)) ? $inputClass : 'input';
$containerType     = 'hidden';

include __DIR__.'/../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$action   = $app->getRequest()->get('objectAction');
$settings = $field['properties'];

$label = (!$field['showLabel'])
    ? ''
    : <<<HTML
<label $labelAttr>{$view->escape($field['label'])}</label>
HTML;

$domainAddress = $_SERVER['HTTP_REFERER'];

$textInput = <<<HTML
    <input {$inputAttr} />
HTML;
// var_dump($field);
$formElementId = 'mauticform_input'.$formName.'_'.$field['alias'];
$stringAllowFileExtensions = implode(',', $field['properties']['allowed_file_extensions']);
$cropImageWidth = $field['properties']['crop_image_ratio_width'];
$cropImageHeight = $field['properties']['crop_image_ratio_height'];

$jsElement = '<script src="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/js/cropper.js') . '"></script>' 
            .'<script src="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/js/croppie.js') . '"></script>'
            .'<script src="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/js/scroll-lock.min.js') . '"></script>'
            .'<script src="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/js/custom.js') . '"></script>';
           
$cssElement = '<link rel="stylesheet" href="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/css/cropper.css') . '" type="text/css"/>'
            .'<link rel="stylesheet" href="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/css/croppie.css') . '" type="text/css"/>'
            .'<link rel="stylesheet" href="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/font-awesome/css/font-awesome.css') . '" type="text/css"/>'
            .'<link rel="stylesheet" href="' . $view['assets']->getUrl('plugins/MauticCropimageBundle/Assets/css/custom.css') . '" type="text/css"/>';
            
$txtUpload = $view['translator']->trans('mautic.integration.cropimage.upload');
$txtCrop = $view['translator']->trans('mautic.integration.cropimage.crop');
$txtModalTitle = $view['translator']->trans('mautic.integration.cropimage.modal.title');
$txtClose = $view['translator']->trans('mautic.integration.cropimage.close');

$html = <<<HTML

    <!-- The Modal -->
    <div id="cropPhotoModal" class="cropimage-modal">
        <!-- Modal content -->
        <div id="cropPhotoModalContent" class="cropimage-modal-content">
            <h2>{$txtModalTitle}</h2>
            <div id="cropPhotoImageWrapper">
                <button type="button" id="cropPhotoRotateRightButton" class="btn btn-default pull-right" style="border: none;"><i class="icon-repeat"></i></button>
                <button type="button" id="cropPhotoRotateLeftButton" class="btn btn-default pull-right" style="border: none;"><i class="icon-undo"></i></button>
                <div id="cropperImageLoaderWrapper"><div id="cropperImageLoader"></div></div>
                <img id="cropPhotoImage" width="100%">
            </div>
            <div style="margin-top: 15px;">
                <button type="button" class="mauticform-button btn btn-default" id="cropPhotoCloseButton">{$txtClose}</button>
                <button type="button" class="mauticform-button btn btn-primary" id="cropPhotoCropButton">{$txtCrop}</button>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>

	<div $containerAttr>
        {$jsElement}
        {$cssElement}
        {$label}{$textInput}
	    <div id="banner-message">
            <div style="margin-bottom: 15px;">
                <label for="cropPhotoFile" class="mauticform-button btn btn-default">{$txtUpload}</label>
                <input id="cropPhotoFile" style="display:none;" type="file">
            </div>
            <span class="mauticform-errormsg" style="display: none;">$validationMessage</span>
            <span id="cropPhotoValidExtension" class="mauticform-errormsg" style="display: none;">$validationMessage</span>
            <span id="cropPhotoValidSize" class="mauticform-errormsg" style="display: none;">$validationMessage</span><span id="cropPhotoCheckMobile"></span>
            <div><img id="cropPhotoUploadResult" width="100%"/></div>
        </div>
    </div>
HTML;
?>
<input type="hidden" id="cropPhotoUploadUrl" value="<?php echo $view['router']->path('mautic_cropimage_upload'); ?>"/>
<input type="hidden" id="cropPhotoGetMessageUrl" value="<?php echo $view['router']->path('mautic_cropimage_get_message'); ?>"/>
<input type="hidden" id="cropPhotoUploadedFolder" value="<?php echo $view['assets']->getUrl('media/images/cropped_images/'); ?>"/>
<input type="hidden" id="cropPhotoAllowFileExtension" value="<?php echo $stringAllowFileExtensions; ?>"/>
<input type="hidden" id="cropPhotoMaxFileSize" value="<?php echo $field['properties']['allowed_file_size']; ?>"/>
<input type="hidden" id="cropPhotoDomainAddress" value="<?php echo $domainAddress; ?>"/>
<input type="hidden" id="cropPhotoElementId" value="<?php echo $formElementId ?>" />
<input type="hidden" id="cropPhotoWidth" value="<?php echo $cropImageWidth ?>" />
<input type="hidden" id="cropPhotoHeight" value="<?php echo $cropImageHeight ?>" />
<?php
echo $html;
?>
