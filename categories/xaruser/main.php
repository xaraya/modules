<?php

/**
 * the main user function
 */
function categories_user_main()
{
    $out = '<h3>Categories test in progress</h3>';
    if (!xarVarFetch('cid', 'isset', $cid, NULL, XARVAR_DONT_SET)) return;
    if (empty($cid) || !is_numeric($cid)) {
        // for DMOZ-like URLs
        // xarModSetVar('categories','SupportShortURLs',1);
        // replace with DMOZ top cid
        $cid = 0;
    }

    if (!xarModAPILoad('categories','user')) return;

    $parents = xarModAPIFunc('categories','user','getparents',
                            array('cid' => $cid));
    $title = '';
    if (count($parents) > 0) {
        $trail = array();
        $link = xarModURL('categories','user','main');
        $trail[] = '<a href="' . $link . '">' . xarML('Browse') . '</a>';
        foreach ($parents as $id => $info) {
            $info['name'] = preg_replace('/_/',' ',$info['name']);
            $title .= $info['name'];
            if ($id == $cid) {
                $info['module'] = 'categories';
                $info['itemtype'] = 0;
                $info['itemid'] = $cid;
                $hooks = xarModCallHooks('item','display',$cid,$info);
                if (!empty($hooks) && is_array($hooks)) {
                // TODO: do something specific with pubsub, hitcount, comments etc.
                    $trail[] = '<strong>' . $info['name'] . '</strong> ' . join('',$hooks);
                } else {
                    $trail[] = '<strong>' . $info['name'] . '</strong>';
                }
            } else {
                $link = xarModURL('categories','user','main',array('cid' => $id));
                $trail[] = '<a href="' . $link . '">' . $info['name'] . '</a>';
                $title .= ' > ';
            }
        }
        $out .= join(' &gt; ',$trail);
        $out .= "<br /><br />\n";
    }

    // set the page title to the current category
    xarTplSetPageTitle(xarVarPrepForDisplay($title));

    $children = xarModAPIFunc('categories','user','getchildren',
                             array('cid' => $cid));
    $category = array();
    $letter = array();
    foreach ($children as $id => $info) {
        if (strlen($info['name']) == 1) {
            $letter[$id] = $info['name'];
        } else {
            $category[$id] = $info['name'];
        }
/*
        $out .= $info['name'] . ' - ' . $info['left'] . '/' . $info['right'] . ' (' . $id . ')';
        $out .= '<br />';
*/
    }

/* test only - requires *_categories_symlinks table for symbolic links :
    $xartable = xarDBGetTables();
    if (empty($xartable['categories_symlinks'])) {
        $xartable['categories_symlinks'] = xarDBGetSiteTablePrefix() . '_categories_symlinks';
    }
    // created by DMOZ import script
    $query = "CREATE TABLE $xartable['categories_symlinks'] (
              xar_cid int(11) NOT NULL default 0,
              xar_name varchar(64) NOT NULL,
              xar_parent int(11) NOT NULL default 0,
              PRIMARY KEY (xar_parent, xar_cid)
              )";

    // Symbolic links
    list($dbconn) = xarDBGetConn();

    $query = "SELECT xar_cid, xar_name FROM $xartable['categories_symlinks'] WHERE xar_parent = '$cid'";
    $result = $dbconn->Execute($query);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,$name) = $result->fields;
        $category[$id] = $name . '@';
        }

    $result->Close();
*/

    if (count($letter) > 0) {
        asort($letter);
        reset($letter);
        $list = array();
        foreach ($letter as $id => $name) {
            $link = xarModURL('categories','user','main',array('cid' => $id));
            $list[] = '<a href="'. $link .'">'. $name . '</a>';
        }
        $out .= '[ ';
        $out .= join(' | ',$list);
        $out .= ' ]';
        $out .= "<hr/>\n";
    }
    if (count($category) > 0) {
        asort($category);
        reset($category);
        $numitems = count($category);
        if ($numitems > 7) {
            $out .= '<table border="0"><tr><td valign="top">';
            $miditem = round(($numitems + 0.5) / 2);
        }
        $out .= '<ul>';
        $count = 0;
        foreach ($category as $id => $name) {
            $name = preg_replace('/_/',' ',$name);
            $link = xarModURL('categories','user','main',array('cid' => $id));
            $out .= '<li><a href="'. $link .'">'. $name . "</a></li>\n";
            $count++;
            if ($numitems > 7 && $count == $miditem) {
                $out .= '</ul></td><td valign="top"><ul>';
            }
        }
        $out .= '</ul>';
        if ($numitems > 7) {
            $out .= '</td></tr></table>';
        }
    }

    $modlist = xarModAPIFunc('categories','user','getmodules',
                            array('cid' => $cid));
    if (count($modlist) > 0) {
        foreach ($modlist as $modid => $numitems) {
            $modinfo = xarModGetInfo($modid);
            $link = xarModURL($modinfo['name'],'user','main');
            $out .= '<a href="' . $link . '">' . $modinfo['name'] . '</a><br />';
            $links = xarModAPIFunc('categories','user','getlinks',
                                  array('modid' => $modid,
                                        'cids' => array($cid)));
            if (!empty($links[$cid])) {
            // TODO: get item type from categories too
                $itemtype = 0;
                $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                           array('itemtype' => $itemtype,
                                                 'itemids' => $links[$cid]));
                if (!empty($itemlinks)) {
                    foreach ($itemlinks as $itemid => $itemlink) {
                        $out .= '<a href="' . $itemlink['url'] . '"> ' . $itemlink['label'] . '</a><br />';
                    }
                } else {
                    foreach ($links[$cid] as $iid) {
                        $link = xarModURL($modinfo['name'],'user','display',
                                         array('objectid' => $iid));
                        $out .= '<a href="' . $link . '"> item ' . $iid . '</a><br />';
                    }
                }
            }
        }
    }
    return $out;
}

?>
