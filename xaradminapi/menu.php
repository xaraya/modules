<?php
/**
 * Get menu info
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 */

/**
 * Get menu info
 *
 * @return array data
 */
function window_adminapi_menu()
{
    $data = array();

    $data['menutitle'] = xarML('Window Administration');
    $data['status'] = '';

    return $data;
}
?>