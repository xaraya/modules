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
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @return array
 * @return array containing the itemlink(s) for the item(s).
 */
function uploads_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();

    // get cids for security check in getall
    $fileList = xarModAPIFunc('uploads', 'user', 'db_get_file', array('fileId' => $itemids));

    if (!isset($fileList) || empty($fileList)) {
        return $itemlinks;
    }

    foreach ($itemids as $itemid) {
        if (!isset($fileList[$itemid])) {
            continue;
        }

        $file = $fileList[$itemid];

        $itemlinks[$itemid] = array('url'   => xarModURL('uploads', 'user', 'download', array('fileId' => $file['fileId'])),
                                    'title' => $file['DownloadLabel'],
                                    'label' => xarVarPrepForDisplay($file['fileName']));
    }
    return $itemlinks;
}
