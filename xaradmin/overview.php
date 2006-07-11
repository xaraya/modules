<?php
/**
 * Overview for AuthLDAP
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/50.html
 * @author Chris Dudley <miko@xaraya.com>
 * @author Richard Cave <rcave@xaraya.com>
*/

/**
 * Overview displays standard Overview page
 */
function authldap_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('authldap', 'admin', 'main', $data, 'main');
}

?>