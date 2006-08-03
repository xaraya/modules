
function loadcontacts(basefieldname,basefieldid) {
    selectfield = document.getElementById('companyselect_'+basefieldid);
    selecturl = "index.php?module=addressbook&func=select";
//    selecturl = "&xar-modurl-addressbook-user-select;";
    company = selectfield.options[selectfield.selectedIndex].value;
    return loadContent(selecturl + '&amp;company=' + company + '&amp;fieldname='+basefieldname+'&amp;fieldid='+basefieldid, 'contactselect_'+basefieldid);
}