<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Logconfig Module
 * @link http://xaraya.com/index.php/release/6969.html
 * @author Logconfig module development team
 */
/**
 * add new item
 * @return array
 */
function logconfig_admin_new()
{
    $data = xarModAPIFunc('logconfig','admin','menu');

    if (!xarVarFetch('itemtype','id',$itemtype)) return;
    if (!xarSecurityCheck('AdminLogConfig')) return;

    // get the Dynamic Object defined for this module (and itemtype, if relevant)
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module' => 'logconfig',
                                              'itemtype' => $itemtype));
     $data['itemtype'] = $itemtype;

    // Return the template variables defined in this function
    return $data;
}

?>