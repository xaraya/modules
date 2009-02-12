<?php
/**
 * Add a new item
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author St.Ego
 */
/**
 * add new item
 */
function dossier_admin_fiximportdata()
{    
    if (!xarVarFetch('returnurl', 'str::', $returnurl, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('TeamDossierAccess')) {
        return;
    }
    
    if(empty($returnurl)) $returnurl = xarModURL('dossier', 'admin');

    if(!xarModAPIFunc('dossier', 'admin', 'fixstates')) {return;}

    if(!xarModAPIFunc('dossier', 'admin', 'fixcountries')) {return;}
    
    xarResponseRedirect($returnurl);
    
    return "";
}

?>
