<?php
/**
 * Xaraya Autolinks
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Autolinks Module
 * @author Jim McDonald; Jason Judge
*/
/**
 * Main menu for utility functions
 */
function autolinks_util_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminAutolinks')) {return;}

    $data = array();

    return $data;
}

?>