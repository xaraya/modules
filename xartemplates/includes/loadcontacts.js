
function loadcontacts(basefieldname,basefieldid) {
    selectfield = document.getElementById('companyselect_'+basefieldid);
    selecturl = "index.php?module=addressbook&func=select";

    if(selectfield.options[selectfield.selectedIndex].value != '') {
        company = selectfield.options[selectfield.selectedIndex].value;
    } else if(selectfield.selectedIndex != '') {
        company = selectfield.options[selectfield.selectedIndex].text;
    }
    
    return loadContent(selecturl + '&amp;company=' + company + '&amp;fieldname='+basefieldname+'&amp;fieldid='+basefieldid, 'contactselect_'+basefieldid);
}