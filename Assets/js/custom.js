function IsSafari() {

    var is_safari = navigator.userAgent.toLowerCase().indexOf('safari/') > -1;
    return is_safari;
  
  }

function readURL() {
	var photoImage = document.getElementById("cropPhotoImage");
    var photoFile = document.getElementById("cropPhotoFile");
    if (photoFile.files && photoFile.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            photoImage.src = e.target.result;
            // alert(photoImage.src);
        }
        reader.readAsDataURL(photoFile.files[0]);

        // if(IsSafari()) {
        //     photoImage.dispatchEvent(new Event('load'));
        // }
        
    }
}

function showCropImageLoadSpin() {
    var spinner = document.getElementById('cropperImageLoaderWrapper');
    spinner.style.display = "block";
}

function hideCropImageLoadSpin() {
    var spinner = document.getElementById('cropperImageLoaderWrapper');
    spinner.style.display = "none";
}

function disableSubmitButton() {
    document.querySelector('button[type="submit"]').disabled = true;
}

function enableSubmitButton() {
    document.querySelector('button[type="submit"]').disabled = false;
}

function hideModal() {
    document.getElementById("cropPhotoModal").style.display = "none";
}

function showModal() {
    document.getElementById("cropPhotoModal").style.display = "block";
}

document.addEventListener("DOMContentLoaded", function(event) { 
    var targetModalElement = document.getElementById("cropPhotoModalContent");
    var photoFile = document.getElementById('cropPhotoFile');
    var photoImage = document.getElementById('cropPhotoImage');
    var photoCropButton = document.getElementById('cropPhotoCropButton');
    var photoCloseButton = document.getElementById('cropPhotoCloseButton');
    var photoRotateRightButton = document.getElementById('cropPhotoRotateRightButton');
    var photoRotateLeftButton = document.getElementById('cropPhotoRotateLeftButton');
    var photoUploadResult = document.getElementById('cropPhotoUploadResult');
    var cropper = null;
    var isImageUploaded = false;
    document.getElementById(document.getElementById('cropPhotoElementId').value).type = 'hidden';

    var validationMessages

    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', document.getElementById('cropPhotoGetMessageUrl').value);
    httpRequest.onreadystatechange = function(){
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                validationMessages = JSON.parse(httpRequest.responseText);
            }
        }
    };
    httpRequest.send();

    photoFile.addEventListener('change', function(){
        isImageUploaded = false;
        document.getElementById('cropPhotoValidExtension').style.display = 'none';
        document.getElementById('cropPhotoValidSize').style.display = 'none';
        var arrayAllowExtentions = document.getElementById('cropPhotoAllowFileExtension').value.split(',');
        var regularEx = /(?:\.([^.]+))?$/;
        var fileName = this.value;
        var fileExtension = regularEx.exec(fileName);

        if(fileExtension[1] == undefined ) {
            console.log(fileExtension)
            return false;
        }

        if(arrayAllowExtentions.indexOf(fileExtension[1]) < 0) {
            this.value="";
            var message1 = validationMessages.ext;
            message1 = message1.replace("%fileExtension%", fileExtension[1]);
            message1 = message1.replace("%extensions%", document.getElementById('cropPhotoAllowFileExtension').value);

            document.getElementById('cropPhotoValidExtension').innerHTML = message1;
            document.getElementById('cropPhotoValidExtension').style.display = 'block';
            return false;
        }
        var sizeMegaByte = (this.files[0].size / (1024*1024)).toFixed(2);

        if(sizeMegaByte > parseInt(document.getElementById('cropPhotoMaxFileSize').value)){
            this.value="";
            var message2 = validationMessages.size;
            console.log(message2);
            message2 = message2.replace("%fileSize%", sizeMegaByte.toString());
            message2 = message2.replace("%maxSize%", document.getElementById('cropPhotoMaxFileSize').value);

            document.getElementById('cropPhotoValidSize').innerHTML = message2;
            document.getElementById('cropPhotoValidSize').style.display = 'block';
            return false;
        }
        showModal();
        scrollLock.disablePageScroll(targetModalElement);
        readURL();
    });

    photoImage.addEventListener('load', function(){
        // alert("photoImage Load");
        if(isImageUploaded == true) {
            return;
        }
        var viewportWidthRatio = document.getElementById('cropPhotoWidth').value;
        var viewportHeightRatio = document.getElementById('cropPhotoHeight').value;
        var viewportWidth = 200;
        var viewportHeight = Math.round(200 * viewportHeightRatio / viewportWidthRatio);

        var opts = {
            viewport: { 
                width: viewportWidth, 
                height: viewportHeight 
            },
            boundary: {
                width: 300,
                height: 300
            },
            showZoomer: true,
            enableOrientation: true
        };
        cropper = new Croppie(document.getElementById('cropPhotoImage'), opts);
    });

    photoCropButton.addEventListener('click', function() {

        var imageWidthRatio = document.getElementById('cropPhotoWidth').value;
        var imageHeightRatio = document.getElementById('cropPhotoHeight').value;
        var imageWidth = 960;
        var imageHeight = Math.round(imageWidth * imageHeightRatio / imageWidthRatio);
        showCropImageLoadSpin();
        cropper.result({type: 'blob', size: {width: imageWidth, height: imageHeight}}).then((blob) => {
            const formData = new FormData();
            formData.append('croppedImage', blob, 'croppedImage.png');
            var httpRequest = new XMLHttpRequest();
            httpRequest.open('POST', document.getElementById('cropPhotoUploadUrl').value);
            httpRequest.onreadystatechange = function(){
                // Process the server response here.
                if (httpRequest.readyState === XMLHttpRequest.DONE) {
                  if (httpRequest.status === 200) {
                    var obj = JSON.parse(httpRequest.responseText);
                    isImageUploaded = true;
                    document.getElementById(document.getElementById('cropPhotoElementId').value).value = validationMessages.domain + document.getElementById('cropPhotoUploadedFolder').value + obj.name;
                    cropper.destroy();
                    document.getElementById('cropPhotoImage').src = document.getElementById('cropPhotoUploadedFolder').value + obj.name;
                    hideCropImageLoadSpin();
                    photoUploadResult.src = document.getElementById('cropPhotoImage').src;
                    photoFile.value = "";
                    photoImage.src = "";
                    hideModal();
                    scrollLock.enablePageScroll(targetModalElement);
                    photoUploadResult.scrollIntoView();
                  } else {
                    alert('There was a problem with the request.');
                  }
                }
              };
            httpRequest.send(formData)
        }/*, 'image/png' */);
    });

    photoCloseButton.addEventListener('click', function() {
        hideModal();
        scrollLock.enablePageScroll(targetModalElement);
        photoFile.value = "";
        if (typeof cropper !== "undefined" && isImageUploaded == false) {
            cropper.destroy();
            photoImage.src="";
        }
        if (isImageUploaded == true)
        {
            photoUploadResult.src = document.getElementById('cropPhotoImage').src;
        }
    })


    photoRotateRightButton.addEventListener('click', function () {
        cropper.rotate(-90);
    })
    photoRotateLeftButton.addEventListener('click', function () {
        cropper.rotate(90);
    })

    // Get the modal
    var modal = document.getElementById("cropPhotoModal");

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            hideModal();
            scrollLock.enablePageScroll(targetModalElement);
            photoFile.value = "";
            if (typeof cropper !== "undefined" && isImageUploaded == false) {
                cropper.destroy();
                photoImage.src="";
            }
        }
    };
});
