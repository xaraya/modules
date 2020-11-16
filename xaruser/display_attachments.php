<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * display rating for a specific item, and request rating
 * @param $args['objectid'] ID of the item this rating is for
 * @param $args['extrainfo'] URL to return to if user chooses to rate
 * @param $args['style'] style to display this rating in (optional)
 * @param $args['itemtype'] item type
 * @return output
 * @return output with rating information
 */
function uploads_user_display_attachments($args)
{
    extract($args);

    if (!xarVar::fetch('inode', 'regexp:/(?<!\.{2,2}\/)[\w\d]*/', $inode, '', xarVar::NOT_REQUIRED)) {
        return;
    }

    $data = array();

    $objectid = (isset($objectid)) ? $objectid : 0;
    ;
    $itemtype = 0;

    if (isset($extrainfo)) {
        if (is_array($extrainfo)) {
            if (isset($extrainfo['module']) && is_string($extrainfo['module'])) {
                $modname = $extrainfo['module'];
            }
            if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
                $itemtype = $extrainfo['itemtype'];
            }
            if (isset($extrainfo['returnurl']) && is_string($extrainfo['returnurl'])) {
                $data['returnurl'] = $extrainfo['returnurl'];
            }
        } else {
            $data['returnurl'] = $extrainfo;
        }
    }

    if (empty($modname)) {
        $modname = xarMod::getName();
    }

    $args['modName']  = $modname;
    $args['modid']    = xarModGetIdFromName($modname);
    $args['itemtype'] = isset($itemtype) ? $itemtype : 0;
    $args['itemid']   = $objectid;

    // save the current attachment info for use later on if the
    // user decides to add / remove attachments for this item
    xarModUserVars::set('uploads', 'save.attachment-info', serialize($args));

    // Run API function
    $associations = xarMod::apiFunc('uploads', 'user', 'db_get_associations', $args);

    if (!empty($associations)) {
        $fileIds = array();
        foreach ($associations as $assoc) {
            $fileIds[] = $assoc['fileId'];
        }

        $Attachments = xarMod::apiFunc('uploads', 'user', 'db_get_file', array('fileId' => $fileIds));
    } else {
        $Attachments = array();
    }

    $data = $args;
    $data['Attachments']              = $Attachments;
    $data['local_import_post_url']    = xarController::URL('uploads', 'user', 'display_attachments');
    // module name is mandatory here, because this is displayed via hooks (= from within another module)
    $data['authid'] = xarSec::genAuthKey('uploads');
    return $data;
}
