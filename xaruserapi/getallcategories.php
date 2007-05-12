<?php

/**
 * Get all possible category information for events.
 * Results are cached for speed.
 * The assumption is that there will be a relatively small number of categories
 * used in the events module.
 */

function ievents_userapi_getallcategories($args)
{
    static $info = NULL;

    if (isset($info)) return $info;

    // Fetch all the config items we need at once.
    list($module, $modid, $itemtype_events) =
        xarModAPIfunc('ievents', 'user', 'params', array('names' => 'module,modid,itemtype_events'));

    // Return empty array if not hooked to categories.
    if (!xarModIsHooked('categories', $module, $itemtype_events)) {
        $info = array();
    } else {
        // Get the base categories for events.
        $catbases = xarModAPIfunc(
            'categories', 'user', 'getallcatbases',
            array('module' => $module, 'itemtype' => $itemtype_events, 'format' => 'flat')
        );

        $basecats = array();
        $basecatslist = array();

        // Get the category list for each catbase.
        foreach($catbases as $key => $catbase) {
            $basecats[$catbase['cid']] = $catbase;
            $basecatslist[$catbase['cid']] = xarModAPIfunc(
                'categories', 'user', 'getcat',
                array('cid' => $catbase['cid'], 'getchildren' => true, 'indexby' => 'cid')
            );
        }

        // To determine which base category each of the event categories are under,
        // we need a list of categories with their bases.

        $flatcatlist = array();
        foreach($basecats as $key1 => $basecat) {
            foreach($basecatslist[$basecat['cid']] as $key2 => $cat) {
                $cat['basecid'] = $key1;
                $flatcatlist[$cat['cid']] = $cat;
                if (!isset($basecats[$key1]['catlist'])) $basecats[$key1]['catlist'] = array();
                $basecats[$key1]['catlist'][$cat['cid']] =& $flatcatlist[$cat['cid']];
            }
        }

        $info = array(
            'basecats' => $basecats,
            'flatlist' => $flatcatlist,
        );
    }

    return $info;
}

?>