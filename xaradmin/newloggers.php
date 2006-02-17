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
 */
function logconfig_admin_newloggers()
{
    $data = xarModAPIFunc('logconfig','admin','menu');

    if (!xarSecurityCheck('AdminLogConfig')) return;

    //This is used in admin/view
    $itemsnum = xarModGetVar('logconfig','itemstypenumber');

    $data['objects'] = array();
    for ($itemtype = 1; $itemtype <= $itemsnum; $itemtype++)
    {
        $object = xarModAPIFunc('dynamicdata','user','getobjectlist',
                                         array('module' => 'logconfig',
                                                  'itemtype' => $itemtype));
         $data['objects'][$itemtype] = array ('type' => $object->properties['loggerType']->default,
                                                                       'description' => $object->properties['description']->default);
    }

    // Return the template variables defined in this function
    return $data;
}

?>