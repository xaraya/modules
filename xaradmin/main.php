<?php
/**
 * DOSSIER admin functions
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/66417.html
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>

 */
/**
 * Main admin function
 * Redirect to modifyconfig
 * @return bool true on success of with redirect
 */
function dossier_admin_main()
{
    if(!xarSecurityCheck('AdminDossier')) {
        return;
    }
    
    $data = xarModAPIFunc('dossier','admin','menu');
    
    return $data;
}

?>
