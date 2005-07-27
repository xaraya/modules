<?php

/**
 * Get information on linked event. Currently assumes linking module = articles
 *
 * arguments:
 *
 *   iid:      id of hooked item (in hooking module, e.g. an article id)
 *   itemtype: type of hooked item
 *   modid:    id of hooking module
 *   event:    current event data
 */
function julian_userapi_geteventinfo($args)
{
    extract($args);

    // Load up database
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitePrefix = xarDBGetSiteTablePrefix();
    // Name for articles database entities
    $articlestable = $sitePrefix . '_articles';
    // Try to find the link for the current module, item type and item id.
    $query = "SELECT `xar_title`,`xar_summary` FROM $articlestable WHERE `xar_aid`='$iid'";
    $result =& $dbconn->Execute($query);
    if (!empty($result)) {
        if (!$result->EOF) {
            $obj = $result->FetchObject(false);
            $event['summary'] = $obj->xar_title;
            $event['description'] = $obj->xar_summary;
            $event['viewURL'] = xarModURL('articles','user','display',array('aid'=>$iid));
        }
    }
 
    return $event;
}

?>
