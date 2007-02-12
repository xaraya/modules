<?php
/**
 * Search System - Present searches via hooks
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * @author Jo Dalle Nogare
 * the Search Overview function
 */
function search_admin_overview()
{
    if (!xarSecurityCheck('AdminSearch')) return;
    $data=array();
    // success
    return $data;
}

?>
