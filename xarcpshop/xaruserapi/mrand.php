<?php
/**
 * File: $Id:
 * 
 * xarcpshop  user menu
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 *
 */
/**
 * @author original author of file Ed Grosvener
 * @author adapted for use with Xaraya
 */
function xarcpshop_userapi_mrand($args)
{
    extract($args);
    
    if($l>$h){$a=$l;$b=$h;$h=$a;$l=$b;}
    if( (($h-$l)+1)<$t || $t<=0 )return false;
    
    $n = array();

if($len>0){

    if(strlen($h)<$len && strlen($l)<$len)return false;
    if(strlen($h-1)<$len && strlen($l-1)<$len && $t>1)return false;
    
    do{
        
    $x = rand($l,$h);

    if(!in_array($x,$n) && strlen($x) == $len)$n[] = $x;

    }while(count($n)<$t);

}else{

    do{
        
    $x = rand($l,$h);

    if(!in_array($x,$n))$n[] = $x;

    }while(count($n)<$t);
    
}

return $n;
}

?>
