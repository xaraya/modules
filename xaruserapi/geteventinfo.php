<?php
/**
 * Get information on a linked event from articles
 *
 * @package modules
 * @copyright (C) 2002-2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */

/**
 * Get information on linked event. Currently assumes linking module = articles
 *
 * @author Jorn, MichelV. <michelv@xarayahosting.nl>
 *
 * @param id  iid id of hooked item (in hooking module, e.g. an article id)
 * @param itemtype: type of hooked item
 * @param modid:    id of hooking module
 * @param event:    current event data
 */
function julian_userapi_geteventinfo($args)
{
    extract($args);

    if (!isset($iid) || !is_numeric($iid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'geteventinfo', 'Julian');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // Load up database
    $dbconn =& xarDBGetConn();
  //  $xartable =& xarDBGetTables();
    $sitePrefix = xarDBGetSiteTablePrefix();
    // Name for articles database entities
    $articlestable = $sitePrefix . '_articles';
    // Try to find the link for the current module, item type and item id.
    $query = "SELECT xar_title, xar_summary, xar_status FROM $articlestable WHERE xar_aid=$iid"; //Only select approved and frontpage articles
    $result = $dbconn->Execute($query);
    if (!empty($result)) {
        if (!$result->EOF) {
            $obj = $result->FetchObject(false);
            $event['summary'] = $obj->xar_title;
            $event['description'] = $obj->xar_summary;
            $event['artstatus'] = $obj->xar_status;
            $event['viewURL'] = xarModURL('articles','user','display',array('aid'=>$iid));
        }
    }

    return $event;
}
?>
