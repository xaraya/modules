<?php

// blocklayout wrapper function
// calls overlib_userapi_init()
function overlib_userapi_bl_init()
{
    // blocklayout tag functions must echo valid PHP code
    return "echo xarModAPIFunc('overlib','user','init'); ";
    
}

function overlib_userapi_bl_open($args=array())
{
    // blocklayout tag functions must echo valid PHP code
    extract($args); unset($args);

    if (empty($text) && !isset($inarray) && empty($function)) {
        // we need to make an exception
        return false;
    }

    //if (empty($trigger)) { $trigger = "onmouseover"; }
    if (empty($name)) { $name = '$olopen'; }
    $retval = "$name  = ";
    $retval .= '"return overlib(\''.preg_replace(array("!'!","![\r\n]!"),array("\'",'\r'),$text).'\'';
    if (isset($sticky) && (bool)$sticky) { $retval .= ",STICKY"; }
    if (!empty($caption)) { $retval .= ",CAPTION,'".str_replace("'","\'",$caption)."'"; }
    if (!empty($fgcolor)) { $retval .= ",FGCOLOR,'$fgcolor'"; }
    if (!empty($bgcolor)) { $retval .= ",BGCOLOR,'$bgcolor'"; }
    if (!empty($textcolor)) { $retval .= ",TEXTCOLOR,'$textcolor'"; }
    if (!empty($capcolor)) { $retval .= ",CAPCOLOR,'$capcolor'"; }
    if (!empty($closecolor)) { $retval .= ",CLOSECOLOR,'$closecolor'"; }
    if (!empty($textfont)) { $retval .= ",TEXTFONT,'$textfont'"; }
    if (!empty($captionfont)) { $retval .= ",CAPTIONFONT,'$captionfont'"; }
    if (!empty($closefont)) { $retval .= ",CLOSEFONT,'$closefont'"; }
    if (!empty($textsize)) { $retval .= ",TEXTSIZE,$textsize"; }
    if (!empty($captionsize)) { $retval .= ",CAPTIONSIZE,$captionsize"; }
    if (!empty($closesize)) { $retval .= ",CLOSESIZE,$closesize"; }
    if (!empty($width)) { $retval .= ",WIDTH,$width"; }
    if (!empty($height)) { $retval .= ",HEIGHT,$height"; }
    if (!empty($left)) { $retval .= ",LEFT"; }
    if (!empty($right)) { $retval .= ",RIGHT"; }
    if (!empty($center)) { $retval .= ",CENTER"; }
    if (!empty($above)) { $retval .= ",ABOVE"; }
    if (!empty($below)) { $retval .= ",BELOW"; }
    if (isset($border)) { $retval .= ",BORDER,$border"; }
    if (isset($offsetx)) { $retval .= ",OFFSETX,$offsetx"; }
    if (isset($offsety)) { $retval .= ",OFFSETY,$offsety"; }
    if (!empty($fgbackground)) { $retval .= ",FGBACKGROUND,'$fgbackground'"; }
    if (!empty($bgbackground)) { $retval .= ",BGBACKGROUND,'$bgbackground'"; }
    if (!empty($closetext)) { $retval .= ",CLOSETEXT,'".str_replace("'","\'",$closetext)."'"; }
    if (!empty($noclose)) { $retval .= ",NOCLOSE"; }
    if (!empty($status)) { $retval .= ",STATUS,'".str_replace("'","\'",$status)."'"; }
    if (!empty($autostatus)) { $retval .= ",AUTOSTATUS"; }
    if (!empty($autostatuscap)) { $retval .= ",AUTOSTATUSCAP"; }
    if (isset($inarray)) { $retval .= ",INARRAY,'$inarray'"; }
    if (isset($caparray)) { $retval .= ",CAPARRAY,'$caparray'"; }
    if (!empty($capicon)) { $retval .= ",CAPICON,'$capicon'"; }
    if (!empty($snapx)) { $retval .= ",SNAPX,$snapx"; }
    if (!empty($snapy)) { $retval .= ",SNAPY,$snapy"; }
    if (isset($fixx)) { $retval .= ",FIXX,$fixx"; }
    if (isset($fixy)) { $retval .= ",FIXY,$fixy"; }
    if (!empty($background)) { $retval .= ",BACKGROUND,'$background'"; }
    if (!empty($padx)) { $retval .= ",PADX,$padx"; }
    if (!empty($pady)) { $retval .= ",PADY,$pady"; }
    if (!empty($fullhtml)) { $retval .= ",FULLHTML"; }
    if (!empty($frame)) { $retval .= ",FRAME,'$frame'"; }
    if (isset($timeout)) { $retval .= ",TIMEOUT,$timeout"; }
    if (!empty($function)) { $retval .= ",FUNCTION,'$function'"; }
    if (isset($delay)) { $retval .= ",DELAY,$delay"; }
    if (!empty($hauto)) { $retval .= ",HAUTO"; }
    if (!empty($vauto)) { $retval .= ",VAUTO"; }
    $retval .= ');"';
	
	return $retval;

    
}

// string used to close a popup
function overlib_userapi_bl_close($args=array())
{
    extract($args); unset($args);
    return "$name = 'return nd();'";
}

?>
