<?php

/**
 * view article map
 */
function articles_user_viewmap()
{
    // Don't use standard categories function for this
    //xarModLoad('categories', 'user');
    //return xarModFunc('categories', 'user', 'viewmap');

    // Get parameters
    if(!xarVarFetch('ptid',  'isset', $ptid,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('by',    'isset', $by,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('go',    'isset', $go,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid', 'isset', $catid,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',  'isset', $cids,   NULL, XARVAR_DONT_SET)) {return;}


    $default = xarModGetVar('articles','defaultpubtype'); 
    if (empty($by)) {
        if (empty($default) && empty($ptid)) {
            $by = 'cat';
        } else {
            $by = 'pub';
        }
    }

    // turn $catid into $cids array (and set $andcids flag)
    if (!empty($catid)) {
        if (strpos($catid,' ')) {
            $cids = explode(' ',$catid);
            $andcids = true;
        } elseif (strpos($catid,'+')) {
            $cids = explode('+',$catid);
            $andcids = true;
        } else {
            $cids = explode('-',$catid);
            $andcids = false;
        }
    }
    $seencid = array();
    if (isset($cids) && is_array($cids)) {
        foreach ($cids as $cid) {
            if (!empty($cid)) {
                $seencid[$cid] = 1;
            }
        }
        $cids = array_keys($seencid);
        sort($cids,SORT_NUMERIC);
    }

    // redirect to filtered view
    if (!empty($go)) {
        $catid = join('+',$cids);
        $url = xarModURL('articles','user','view',array('ptid' => $ptid, 'catid' => $catid));
        xarResponseRedirect($url);
        return;
    }

    if (!xarModAPILoad('articles', 'user')) return;
    if (!xarModAPILoad('categories', 'user')) return;
    if (!xarModAPILoad('categories', 'visual')) return;

    $publinks = array();
    $cattree = array();
    $dump = '';

    if ($by == 'cat') {

    // TODO: re-evaluate this after user feedback...
        // *trick* Use the 'default' categories here, instead of all rootcats
        $cidstring = xarModGetVar('articles','mastercids');
        $rootcats = explode (';', $cidstring);

        $catlist = array();
        if (!empty($rootcats) && is_array($rootcats)) {
            foreach ($rootcats as $cid) {
                $catlist[$cid] = 1;
            }
        }


    $maplink = xarModURL('articles','user','viewmap',array('by' => 'cat'));

    $dump .= '<br /><form method="post" action="' . $maplink . '">';

    $dump .= xarML('Filter') . ' : ';

    foreach ($catlist as $cid => $val) {
        $dump .= '&nbsp;&nbsp;&nbsp;';
        $dump .= xarModAPIFunc('categories',
                              'visual',
                              'makeselect',
                              Array('cid' => $cid,
                                    'return_itself' => true,
                                    'select_itself' => true,
                                    'values' => &$seencid,
                                    'multiple' => 0));
    }
    $dump .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="go" value="Go"></form><br />';


        // create the category tree for each root category
    // TODO: make sure permissions are taken into account here !
        foreach ($catlist as $cid => $val) {
            if (empty($val)) {
                continue;
            }
            $cattree[$cid] = xarModAPIFunc('articles',
                                          'user',
                                          'getchildcats',
                                          // frontpage or approved
                                          array('status' => array(3,2),
                                                'cid' => $cid,
                                                'ptid' => null,
                                                // keep a link to the parent cid
                                                'showcid' => true));
        }

    } else {

    // get the links and counts for all publication types
    $publinks = xarModAPIFunc('articles','user','getpublinks',
                             array('status' => array(3,2),
                                   'all' => 1));

    // build the list of root categories for all publication types
    // and save results in publinks as well
    $catlist = array();
    for ($i=0;$i<count($publinks);$i++) {
        $pubid = $publinks[$i]['pubid'];
        $cidstring = xarModGetVar('articles','mastercids.'.$pubid);
        if (!empty($cidstring)) {
            $rootcats = explode(';',$cidstring);
            foreach ($rootcats as $cid) {
                $catlist[$cid] = 1;
            }
            $publinks[$i]['rootcats'] = $rootcats;
        } else {
            $publinks[$i]['rootcats'] = array();
        }
    }

    // for all publication types
    for ($i=0;$i<count($publinks);$i++) {
        $publinks[$i]['cats'] = array();
        $pubid = $publinks[$i]['pubid'];
        // for each root category of this publication type
        foreach ($publinks[$i]['rootcats'] as $cid) {
            // add the category tree to the list of categories to show
            $childcats =  xarModAPIFunc('articles',
                                        'user',
                                        'getchildcats',
                                        // frontpage or approved
                                        array('status' => array(3,2),
                                              'cid' => $cid,
                                              'ptid' => $pubid,
                                              // keep a link to the parent cid
                                              'showcid' => true));
            $publinks[$i]['cats'][] = $childcats;
            //$cattree[$cid] = $childcats;
        }
    }

// TODO: show matrix/pivottable for categories (this is just a demo)
// TODO: improve this nightmarish (but somewhat working) code

// Some experimental output - here be dragons :-)

/* already retrieved via getchildcats above
    // create the category tree for each root category
// TODO: make sure permissions are taken into account here !
    $cattree = array();
    foreach ($catlist as $cid => $val) {
        if (empty($val)) {
            continue;
        }
        $list = xarModAPIFunc('categories',
                             'visual',
                             'listarray',
                             array('cid' => $cid));

        // Add link and count information
        for ($i=0;$i<count($list);$i++) {
            $info = $list[$i];
// TODO: show icons instead of (or in addition to) a link if available ?
            $list[$i]['link'] = xarModURL('articles',
                                         'user',
                                         'view',
                                         array('catid' => $info['id']));
            $list[$i]['name'] = xarVarPrepForDisplay($info['name']);
        }
        $cattree[$cid] = $list;
    }
*/

    $maplink = xarModURL('articles','user','viewmap',array('by' => 'pub'));

    $dump .= '<br /><form method="post" action="' . $maplink . '">';

    $dump .= xarML('Filter') . ' : <select name="ptid" onchange="submit()"><option value=""> ' . xarML('Publication');
    foreach ($publinks as $pub) {
        if ($pub['pubid'] == $ptid) {
            $dump .= '<option value="' . $pub['pubid'] . '" selected> - ' . $pub['pubtitle'];
        } else {
            $dump .= '<option value="' . $pub['pubid'] . '"> - ' . $pub['pubtitle'];
        }
    }
    $dump .= '</select>';

    if (empty($ptid)) {
//        $array = array_keys($catlist);
        $array = array();
    } else {
        $array = array();
        for ($i = 0; $i < count($publinks); $i++) {
            if ($ptid == $publinks[$i]['pubid']) {
                $array = $publinks[$i]['rootcats'];
            }
        }
    }
    foreach ($array as $cid) {
        $dump .= '&nbsp;&nbsp;&nbsp;';
        $dump .= xarModAPIFunc('categories',
                              'visual',
                              'makeselect',
                              Array('cid' => $cid,
                                    'return_itself' => true,
                                    'select_itself' => true,
                                    'values' => &$seencid,
                                    'multiple' => 0));
    }
    $dump .= '&nbsp;&nbsp;&nbsp;<input type="submit" name="go" value="Go"></form><br />';

/* skip this for real sites...
    // get the counts for all groups of (N) categories
    $pubcatcount2 = xarModAPIFunc('articles',
                                 'user',
                                 'getpubcatcount',
                                 // frontpage or approved
                                 array('status' => array(3,2),
                                       'groupcids' => 2, // depends on ptid cids.
                                       'reverse' => 1));
    $dump .= 'TODO: show matrix/pivottable for categories (under construction)<br />';

    $testptid = $publinks[0]['pubid'];
    list($one,$two) = $publinks[0]['rootcats'];
    if (count($cattree[$one]) <= count($cattree[$two])) {
        $three = $one;
        $one = $two;
        $two = $three;
    }
    $typeone = array_shift($cattree[$one]);
    $typetwo = array_shift($cattree[$two]);
    foreach ($cattree[$one] as $info1) {
        $matrix[$info1['id']] = array();
        $name[$info1['id']] = $info1['name'];
        foreach ($cattree[$two] as $info2) {
            $matrix[$info1['id']][$info2['id']] = 0;
            $name[$info2['id']] = $info2['name'];
        }
    }
    foreach ($pubcatcount2 as $cids => $counts) {
        list($ca,$cb) = explode('+',$cids);
        if (isset($matrix[$ca][$cb])) {
            $matrix[$ca][$cb] = $counts['total'];
        } elseif (isset($matrix[$cb][$ca])) {
            $matrix[$cb][$ca] = $counts['total'];
        } else {
            $dump .= "not found : $cids";
        }
    }
    $dump .= '<table border="1" cellpadding="3"><tr><td align="right">' .
             $typetwo['name'] . '<br />-<br />' . $typeone['name'] . '</td>';
    foreach ($matrix as $cid1 => $list) {
        foreach ($list as $cid2 => $val) {
            $link = xarModURL('articles','user','view',array('catid' => $cid2,'ptid' => $testptid));
            $showname = wordwrap($name[$cid2],9,'<br />',1);
            $dump .= '<td valign="top" align="middle"><a href="' . $link . '">' . $showname . '</a></td>';
        }
        break;
    }
    $dump .= '</tr>';
    foreach ($matrix as $cid1 => $list) {
        $link = xarModURL('articles','user','view',array('catid' => $cid1,'ptid' => $testptid));
        $dump .= '<td><a href="' . $link . '">' . $name[$cid1] . '</a></td>';
        foreach ($list as $cid2 => $val) {
            if ($val > 0) {
                $cids = array($cid1,$cid2);
                sort($cids,SORT_NUMERIC);
                $catid = join('+',$cids);
                $link = xarModURL('articles','user','view',array('catid' => $catid,'ptid' => $testptid));
                $dump .= '<td align="center"><a href="' . $link . '">&nbsp;' .$val . '&nbsp;</a></td>';
            } else {
                $dump .= '<td align="center">&nbsp;</td>';
            }
        }
        $dump .= '</tr>';
    }
    $dump .= '</table>';
*/

    if (empty($ptid)) {
        $ptid = $default;
    }
    foreach ($publinks as $pub) {
        if ($pub['pubid'] == $ptid) {
            $descr = $pub['pubtitle'];
        }
    }

// end of $by != 'cat'
    }

    if (empty($descr)) {
        $descr = xarML('Articles');
    }

    $archivelink = xarModURL('articles','user','archive',
                            array('ptid' => $ptid));

    // Save some variables to (temporary) cache for use in blocks etc.
    xarVarSetCached('Blocks.articles','ptid',$ptid);
//if ($shownavigation) {
    xarVarSetCached('Blocks.categories','module','articles');
    xarVarSetCached('Blocks.categories','itemtype',$ptid);
    if (!empty($descr)) {
        xarVarSetCached('Blocks.categories','title',$descr);
        xarTplSetPageTitle(xarVarPrepForDisplay($descr), xarML('Map'));
    }
//}

    $data = array('publinks' => $publinks,
                 'cattree' => $cattree,
                 'ptid' => $ptid,
                 'maplabel' => xarML('View Article Map'),
                 'viewlabel' => xarML('Back to') . ' ' . $descr,
                 'viewlink' => xarModURL('articles','user','view',array('ptid' => $ptid)),
                 'archivelabel' => xarML('View Archives'),
                 'archivelink' => $archivelink,
                 'dump' => $dump);

    if (!empty($ptid)) {
        // Get publication types
        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
        $template = $pubtypes[$ptid]['name'];
    } else {
// TODO: allow templates per category ?
       $template = null;
    }

    return xarTplModule('articles', 'user', 'viewmap', $data, $template);
}

?>
