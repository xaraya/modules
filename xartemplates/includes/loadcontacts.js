
function dossier_loadcontactlist(basefieldname,basefieldid,size) {
    selectfield = document.getElementById('companyselect_'+basefieldid);
    selecturl = "index.php?module=dossier&func=select";

    if(selectfield.options[selectfield.selectedIndex].value != '') {
        company = selectfield.options[selectfield.selectedIndex].value;
    } else if(selectfield.selectedIndex != '') {
        company = selectfield.options[selectfield.selectedIndex].text;
    }
    multiplefield = document.getElementById('multiple');
    multiple = multiplefield.value;
    
    if(size == false) size=1;
//    else size=5;
    return loadContent(selecturl + '&amp;company=' + company + '&amp;fieldname='+basefieldname+'&amp;fieldid='+basefieldid+'&amp;size='+size+'&amp;multiple='+multiple, 'contactselect_'+basefieldid);
}