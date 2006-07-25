function twist(sid)
{
    imgobj = document.getElementById("spinnerimg_"+sid);
    divobj = document.getElementById(sid);
    if(divobj.style.visibility == "hidden")
    {
        imgobj.src = "modules/xtasks/xarimages/rotate2.gif";
        divobj.style.position = "relative";
        divobj.style.visibility = "visible";
    } else {
        imgobj.src = "modules/xtasks/xarimages/rotate.gif";
        divobj.style.position = "absolute";
        divobj.style.visibility = "hidden";
    }
}

    