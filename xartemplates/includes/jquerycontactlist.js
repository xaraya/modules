
function dossier_reloadcontactlist(basefieldname,basefieldid,size) {
    selectfield = document.getElementById(basefieldid);
    selecturl = "index.php?module=dossier&func=select&pageName=module";

    if(selectfield.options[selectfield.selectedIndex].value != '') {
        contactid = selectfield.options[selectfield.selectedIndex].value;
    } else if(selectfield.selectedIndex != '') {
        contactid = selectfield.options[selectfield.selectedIndex].text;
    }
    
    if(size == false) size=1;
    
    if(isNaN(contactid)) {
        $("#contactselect_"+basefieldid).load(selecturl + '&contactid=' + contactid + '&fieldname='+basefieldname+'&fieldid='+basefieldid+'&size='+size);
    }
        
}