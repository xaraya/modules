function twist(sid)
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

function twist_open(sid)
{
    var divobj = document.getElementById(sid);
    if(!divobj)return true;
    if(divobj.style.display == "none")
    {
        divobj.style.display = "block";
    }
    return true;
}


function confirmSubmit()
{
    var agree=confirm("This will update task status, but will not indicate that any work was completed. Continue?");
    if (agree)
    	return true ;
    else
    	return false ;
}