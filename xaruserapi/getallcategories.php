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

        // We may not have any categories set up yet.
        if (empty($catbases)) return;

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
        // It is necessary to do some trickery to get the nesting 'level' of each category.
        // The 'indentation' value on each category is an absolute value, and will depend
        // on where the root is in the overall category tree. The 'level' is calculated
        // with respect to the root category (the root being zero).

        $flatcatlist = array();
        foreach($basecats as $key1 => $basecat) {
            $base_indent = 2;
            foreach($basecatslist[$basecat['cid']] as $key2 => $cat) {
                if ($cat['parent'] == $key1) {$base_indent = (int)$cat['indentation'];}

                $cat['basecid'] = $key1;
                $flatcatlist[$cat['cid']] = $cat;
                if (!isset($basecats[$key1]['catlist'])) $basecats[$key1]['catlist'] = array();
                $basecats[$key1]['catlist'][$cat['cid']] =& $flatcatlist[$cat['cid']];

                // Set the 'level' - starting at level 0 for the root category.
                $flatcatlist[$cat['cid']]['level'] = ((int)$flatcatlist[$cat['cid']]['indentation'] - $base_indent + 1);
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