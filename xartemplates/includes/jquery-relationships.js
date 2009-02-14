
function dossier_showrelationships(contactid,targetid) {
    targetfield = document.getElementById(targetid);
    selecturl = "index.php?module=dossier&func=relationships&pageName=module";
    
    if(!isNaN(contactid)) {
        $("#"+targetid).load(selecturl + '&contactid=' + contactid);
    }
        
}