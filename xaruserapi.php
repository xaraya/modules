<?php

// blocklayout wrapper function
// calls overlib_userapi_init()
function overlib_userapi_bl_init()
{
    // blocklayout tag functions must echo valid PHP code
    return "echo xarModAPIFunc('overlib','user','init'); ";

}

/**
 *  overlibPrepForJS
 *  Takes text for display and formats it so it does not break on display
 *  @param $s string the text to manipulate
 *  @return string text to insert
 *  @access private
 */

function __overlibPrepForJS($s)
{
    // remove linefeeds/carriage returns as this will
    $s = preg_replace('/[\r|\n]/i','',$s);
    // escape single quotes and single quote entities
    $squotes = array("'","&#39;");
    $s = str_replace($squotes,"\'",$s);
    // convert double quotes to html entity
    $s = str_replace('"','&quot;',$s);
    // ok, now we need to break really long lines
    // correct interpretation of special characters
    // we only want to break at spaces to allow for
    $tmp = explode(' ',$s);
    // we don't need these so free up some memory
    unset($s,$squotes);
    // return the new string
    return join("'+' ",$tmp);
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
    $retval .= '"return overlib(\''.__overlibPrepForJS($text).'\'';
    if (isset($sticky) && (bool)$sticky) { $retval .= ",STICKY"; }
    if (!empty($width)) { $retval .= ",WIDTH,$width"; }
    if (!empty($height)) { $retval .= ",HEIGHT,$height"; }
    if (!empty($left)) { $retval .= ",LEFT"; }
    if (!empty($right)) { $retval .= ",RIGHT"; }
    if (!empty($center)) { $retval .= ",CENTER"; }
    if (!empty($above)) { $retval .= ",ABOVE"; }
    if (!empty($below)) { $retval .= ",BELOW"; }
    if (isset($offsetx)) { $retval .= ",OFFSETX,$offsetx"; }
    if (isset($offsety)) { $retval .= ",OFFSETY,$offsety"; }
    if (!empty($autostatus)) { $retval .= ",AUTOSTATUS"; }
    if (!empty($autostatuscap)) { $retval .= ",AUTOSTATUSCAP"; }
    if (!empty($snapx)) { $retval .= ",SNAPX,$snapx"; }
    if (!empty($snapy)) { $retval .= ",SNAPY,$snapy"; }
    if (isset($fixx)) { $retval .= ",FIXX,$fixx"; }
    if (isset($fixy)) { $retval .= ",FIXY,$fixy"; }
    if (isset($relx)) { $retval .= ",RELX,$fixx"; }
    if (isset($rely)) { $retval .= ",RELY,$fixy"; }
    if (!empty($frame)) { $retval .= ",FRAME,'$frame'"; }
    if (isset($timeout)) { $retval .= ",TIMEOUT,$timeout"; }
    if (isset($delay)) { $retval .= ",DELAY,$delay"; }
    if (!empty($hauto)) { $retval .= ",HAUTO"; }
    if (!empty($vauto)) { $retval .= ",VAUTO"; }
    if (!empty($closeclick)) { $retval .= ",CLOSECLICK"; }
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
    if (!empty($fgbackground)) { $retval .= ",FGBACKGROUND,'$fgbackground'"; }
    if (!empty($bgbackground)) { $retval .= ",BGBACKGROUND,'$bgbackground'"; }
    if (!empty($capicon)) { $retval .= ",CAPICON,'$capicon'"; }
    if (isset($border)) { $retval .= ",BORDER,$border"; }
    if (!empty($background)) { $retval .= ",BACKGROUND,'$background'"; }
    if (!empty($padx)) { $retval .= ",PADX,$padx"; }
    if (!empty($pady)) { $retval .= ",PADY,$pady"; }
    if (!empty($fullhtml)) { $retval .= ",FULLHTML"; }
    if (!empty($cssoff)) { $retval .= ",CSSOFF"; }
    if (!empty($cssstyle)) { $retval .= ",CSSSTYLE"; }
    if (!empty($cssclass)) { $retval .= ",CSSCLASS"; }
    if (!empty($fgclass)) { $retval .= ",FGCLASS,$fgclass"; }
    if (!empty($bgclass)) { $retval .= ",BGCLASS,$bgclass"; }
    if (!empty($textfontclass)) { $retval .= ",TEXTFONTCLASS,$textfontclass"; }
    if (!empty($captionfontclass)) { $retval .= ",CAPTIONFONTCLASS,$captionfontclass"; }
    if (!empty($closefontclass)) { $retval .= ",CLOSEFONTCLASS,$closefontclass"; }
    if (!empty($caption)) { $retval .= ",CAPTION,'".__overlibPrepForJS($caption)."'"; }
    if (!empty($cellpad)) { $retval .= ",CELLPAD,$cellpad"; }
    if (!empty($closetitle)) { $retval .= ",CLOSETITLE,$closetitle"; }
    if (!empty($compatmode)) { $retval .= ",COMPATMODE"; }
    if (!empty($followmouse)) { $retval .= ",FOLLOWMOUSE"; }
    if (!empty($mouseoff)) { $retval .= ",MOUSEOFF,$textfontclass"; }
    if (!empty($noclose)) { $retval .= ",NOCLOSE"; }
    if (isset($inarray)) { $retval .= ",INARRAY,'$inarray'"; }
    if (isset($caparray)) { $retval .= ",CAPARRAY,'$caparray'"; }
    if (!empty($function)) { $retval .= ",FUNCTION,'$function'"; }
    if (!empty($status)) { $retval .= ",STATUS,'".__overlibPrepForJS($status)."'"; }
    if (!empty($wrap)) { $retval .= ",WRAP,'$wrap'"; }
    $retval .= ');"; ';

    return $retval;


}

// string used to close a popup
function overlib_userapi_bl_close($args=array())
{
    extract($args); unset($args);
    if (empty($name)) { $name = '$olclose'; }
    return "$name = 'return nd();'; ";
}

?>
