<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Delete all comments attached to the specified module id
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access  private
 * @param   integer     $modid      the id of the module that the comments are associated with
 * @param   integer     $itemtype   the item type that the comments are associated with
 * @returns bool true on success, false otherwise
 */
function comments_adminapi_delete_module_nodes( $args )
{
    extract($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Missing or Invalid parameter \'modid\'!!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    $return_value = TRUE;

    $pages = xarModAPIFunc('comments','user','get_object_list',
                            array('modid' => $modid,
                                  'itemtype' => $itemtype ));

    if (count($pages) <= 0 || empty($pages)) {
        return $return_value;
    } else {
        foreach ($pages as $object) {
            xarModAPIFunc('comments','admin','delete_object_nodes',
                          array('modid'     => $modid,
                                'itemtype'  => $itemtype,
                                'objectid'  => $object['pageid']));
        }
    }
    return $return_value;
}
?>