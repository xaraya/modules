function openDialog(sLocation, sTitle, iWidth, iHeight) {
        
    window.open(sLocation, 
                sTitle, 
                "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=" + iWidth + ",height=" + iHeight + ",left=20,top=20");
    return true;
}

function PropertiesDialog(sLocation, sFileName) {
    return openDialog(sLocation, sFileName, 500, 375);
}

function AttachmentsDialog(sLocation, sTitle) {
    return openDialog(sLocation, sTitle, 500, 375);
}