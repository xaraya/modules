function openDialog(sLocation, iWidth, iHeight) {
    window.open(sLocation, '_blank', "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=" + iWidth + ",height=" + iHeight + ",left=20,top=20");
}

function PropertiesDialog(sLocation) {
    return openDialog(sLocation, 500, 375);
}

function AttachmentsDialog(sLocation) {
    return openDialog(sLocation, 500, 375);
}