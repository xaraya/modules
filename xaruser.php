<?php
/**
 * AuthInvision module - authenticate against Invision PB forum
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authinvision
 * @link http://xaraya.com/index.php/release/950.html
 * @author ladyofdragons
 */
function authinvision_user_login() 
{
    $mainfile = xarModGetVar('authinvision','mainfile');
    require($mainfile);
    
}

function authinvision_user_main() 
{
    $mainfile = xarModGetVar('authinvision','mainfile');
    include("$mainfile");;
    $data['regform'] = show_reg();
    return $data;
}

?>