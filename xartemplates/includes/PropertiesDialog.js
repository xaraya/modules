function openDialog(sLocation, sTitle, iWidth, iHeight) {
        
    window.open(sLocation, 
                sTitle, 
                'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=' + iWidth + ',height=' + iHeight + ',left=20,top=20');
}

function PropertiesDialog(sLocation, sFileName) {
    openDialog(sLocation, sFileName, 500, 375);
}

