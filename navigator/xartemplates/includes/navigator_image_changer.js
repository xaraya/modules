function navigator_image_changer(sURL, iTypeId, sSide) {

    sName = 'dynimages['+iTypeId+']['+sSide+']';
    oForm = document.forms['post'];
    oFormLength = document.forms['post'].length;

    for (var i = 0; i < oFormLength; i++) { 
        if (oForm.elements[i].value != null) {
    		if (oForm.elements[i].name == sName) {
                var iSelectValue = oForm.elements[i].value;
			} 
        }
    } 
    
    sThumbId = 'thumb_'+iTypeId;
    oThumbId = document.images[sThumbId];
    
    if (iSelectValue == 0) {
        sSource = '';
    } else {
        sSource = sURL + '&fileId=' + iSelectValue;
    }
    
    nImage = new Image;
    nImage.src = sSource;
    
    oThumbId.src = nImage.src;
    return true;
}