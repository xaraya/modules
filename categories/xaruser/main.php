<?php

/**
 * the main user function
 */
function categories_user_main()
{
    $out = '<h3>Categories test in progress</h3>';
    if (!xarVarFetch('cid', 'isset', $cid, NULL, XARVAR_DONT_SET)) return;
    if (empty($cid) || !is_numeric($cid)) {
        // xarModSetVar('categories','SupportShortURLs',1);
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

/* test only - requires categories_symlinks table for symbolic links :
    $xartable = xarDBGetTables();   
    $query = "CREATE TABLE xar_categories_symlinks (
              xar_cid int(11) NOT NULL default 0,
              xar_name varchar(64) NOT NULL,
              xar_parent int(11) NOT NULL default 0,
              PRIMARY KEY (xar_parent, xar_cid)
              )";

    // Symbolic links
    list($dbconn) = xarDBGetConn();

    $query = "SELECT xar_cid, xar_name FROM xar_categories_symlinks WHERE xar_parent = '$cid'";
    $result = $dbconn->Execute($query);
    if ($dbconn->ErrorNo() != 0) {
        $msg = xarML('DATABASE_ERROR', $sql);
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'DATABASE_ERROR',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
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

/*
    list($dbconn) = xarDBGetConn();
    $parent = array();
    $id = $cid;
    while ($id > 0) {
        $query = "SELECT xar_parent, xar_name FROM nuke_cat2 WHERE xar_cid = '"
                 . xarVarPrepForStore($id) . "'";
        $result = $dbconn->Execute($query);
        list($newid,$name) = $result->fields;
        $link = xarModURL('categories','user','main',array('cid' => $id));
        if ($id == $cid) {
            $name = preg_replace('/_/',' ',$name);
            $parent[] = '<strong>' . $name . '</strong>';
        } else {
            $parent[] = '<a href="' . $link . '">' . $name . '</a>';
        }
        $id = $newid;
    }
    $info = array();
    $letter = array();
    $query = "SELECT xar_cid, xar_name FROM nuke_cat2 WHERE xar_parent = '"
             . xarVarPrepForStore($cid) . "' ORDER BY xar_name ASC";
    $result = $dbconn->Execute($query);
    while (!$result->EOF) {
        list($id,$name) = $result->fields;
        if (strlen($name) == 1) {
            $letter[$id] = $name;
        } else {
            $info[$id] = $name;
        }
        $result->MoveNext();
    }
    $query = "SELECT xar_cid, xar_name FROM nuke_cat2_symlinks WHERE xar_parent = '"
             . xarVarPrepForStore($cid) . "' ORDER BY xar_name ASC";
    $result = $dbconn->Execute($query);
    while (!$result->EOF) {
        list($id,$name) = $result->fields;
        $info[$id] = $name . '@';
        $result->MoveNext();
    }
    if ($cid > 1 && count($parent) > 0) {
        $parent = array_reverse($parent);
        $out .= join(' &gt; ',$parent);
        $out .= "<br /><br />\n";
    }
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
    if (count($info) > 0) {
        asort($info);
        reset($info);
        $numitems = count($info);
        if ($numitems > 7) {
            $out .= '<table border="0"><tr><td valign="top">';
            $miditem = round(($numitems + 0.5) / 2);
        }
        $out .= '<ul>';
        $count = 0;
        foreach ($info as $id => $name) {
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
*/

// TEST: get the immediate children via Celko
/*
$n = 1;
$parent = '8079';
$parent = 8496;
$parent = 0;
$nuke_cat = 'nuke_categories';
if(!xarModAPILoad('categories','user')) return;
$cat = xarModAPIFunc('categories','user','getcatinfo',Array('cid' => $parent));

$out .= $cat['name']. ' - ' . $cat['left'] . '/' . $cat['right'] . ' :<br />';
$children = xarModAPIFunc('categories','user','getchildren',Array('cid' => $parent));
foreach ($children as $cid => $info) {
    $out .= $info['name'] . ' - ' . $info['left'] . '/' . $info['right'] . ' (' . $cid . ')';
    $parents = xarModAPIFunc('categories','user','getparents',Array('cid' => $cid));
    foreach ($parents as $pcid => $pinfo) {
        $out .= ' ' . $pinfo['name'] . ' (' . $pcid . ')';
    }
    $out .= '<br />';
}
*/
/* this is OK, and faster
$left = $cat['left'] + 1;
$query = "
SELECT xar_name, xar_left, xar_right, xar_cid
  FROM $nuke_cat
 WHERE xar_parent = $parent
";
*/
/* this is OK, but slow
SELECT children.xar_name,children.xar_left,children.xar_right, COUNT(parents.xar_cid) as indent
  FROM $nuke_cat AS parents, $nuke_cat AS children
 WHERE children.xar_left BETWEEN $left AND $cat[right]
   AND parents.xar_left BETWEEN $cat[left] AND $cat[right]
   AND children.xar_left BETWEEN parents.xar_left AND parents.xar_right
   AND children.xar_cid != parents.xar_cid
GROUP BY children.xar_cid
HAVING indent = 1
";
*/
/* this is wrong
SELECT children.xar_name,children.xar_left,children.xar_right, COUNT(parents.xar_name) as indentation
  FROM $nuke_cat AS parents, $nuke_cat AS children
 WHERE children.xar_left BETWEEN $cat[left] AND $cat[right]
   AND children.xar_left BETWEEN parents.xar_left AND parents.xar_right
GROUP BY children.xar_name";
*/
/*
    $result = $dbconn->Execute($query);
if ($dbconn->ErrorNo() != 0) {
$out .= "Error in $query : " . $dbconn->ErrorMsg();
} else {
    while (!$result->EOF) {
        list($name,$left,$right,$indent) = $result->fields;
$out .= $name . ' - ' . $left . '/' . $right . ' (' . $indent . ')<br />';
        $result->MoveNext();
    }
}
*/
    return $out;
}

?>
