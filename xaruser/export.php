<?php
/**
 * DOSSIER user viewAll
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @author Chad Kraeft <cdavidkraeft@miragelab.com>

 */

/**
 * Export configuration page
 *
 * @return array of menu links
 */
function dossier_user_export()
{
    $output = array();

    /**
     * Security check first
     */
    if (xarSecurityCheck('AdminDOSSIER',0)) {

        xarModAPIFunc('dossier','user','export');

    }

    return xarModAPIFunc('dossier','util','handleexception',array('output'=>$output));

} // END export

?>
