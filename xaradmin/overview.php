<?php
/**
 * Overview for AuthLDAP
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthLDAP
 * @link http://xaraya.com/index.php/release/50.html
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