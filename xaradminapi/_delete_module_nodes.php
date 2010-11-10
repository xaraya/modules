<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
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
        throw new BadParameterException($msg);
    }
    if (!isset($itemtype)) {
        $itemtype = 0;
    }

    $return_value = TRUE;

    $pages = xarMod::apiFunc('comments','user','get_object_list',
                            array('modid' => $modid,
                                  'itemtype' => $itemtype ));

    if (count($pages) <= 0 || empty($pages)) {
        return $return_value;
    } else {
        foreach ($pages as $object) {
            xarMod::apiFunc('comments','admin','delete_object_nodes',
                          array('modid'     => $modid,
                                'itemtype'  => $itemtype,
                                'objectid'  => $object['pageid']));
        }
    }
    return $return_value;
}
?>