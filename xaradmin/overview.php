<?php
/**
 * Overview for xarldap
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarldap
 * @link http://xaraya.com/index.php/release/25.html
 * @author Richard Cave <rcave@xaraya.com>
 */

/**
 * Overview displays standard Overview page
 */
function xarldap_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xarldap', 'admin', 'main', $data, 'main');
}

?>