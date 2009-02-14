// use for the spinner toggle
function dossier_twist(sid)
{
    var divobj = document.getElementById(sid);
    if(!divobj)return true;
    if(divobj.style.display == "none")
    {
        divobj.style.display = "block";
    } else {
        divobj.style.display = "none";
    }
    return true;
}
// add to "new item" buttons/links
function dossier_twist_open(sid)
{
    var divobj = document.getElementById(sid);
    if(!divobj)return true;
    if(divobj.style.display == "none")
    {
        divobj.style.display = "block";
    }
    return true;
}

    