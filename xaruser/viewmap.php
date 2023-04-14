<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * view article map
 */
function publications_user_viewmap($args)
{
    // Get parameters
    if(!xarVar::fetch('ptid',  'id',    $ptid,   xarModVars::get('publications', 'defaultpubtype'), xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('by', 'enum:pub:cat:grid',   $by,     NULL, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('go',    'str',   $go,     NULL, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('catid', 'str',   $catid,  NULL, xarVar::NOT_REQUIRED)) {return;}
    if(!xarVar::fetch('cids',  'array', $cids,   NULL, xarVar::NOT_REQUIRED)) {return;}

    // Override if needed from argument array
    extract($args);

    $default = xarModVars::get('publications','defaultpubtype');
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
            // make sure cids are numeric
            if (!empty($cid) && is_numeric($cid)) {
                $seencid[$cid] = 1;
            }
        }
        $cids = array_keys($seencid);
        sort($cids,SORT_NUMERIC);
    }

    // Get publication types
     sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
    $data['pubtypes'] = $object->getItems();

    // redirect to filtered view
    if (!empty($go) && (!empty($ptid) || $by == 'cat')) {
        if (is_array($cids) && count($cids) > 0) {
            $catid = join('+',$cids);
        } else {
            $catid = NULL;
        }
        $url = xarController::URL('publications','user','view',array('ptid' => $ptid, 'catid' => $catid));
        xarController::redirect($url);
        return;
    }

    $data['catfilter'] = array();
    $data['cattree'] = array();
    $data['catgrid'] = array();

    $dump = '';

    $publinks = array();

    if ($by == 'cat') {
        $data['maplink'] = xarController::URL('publications','user','viewmap',array('by' => 'cat'));

    // TODO: re-evaluate this after user feedback...
        // *trick* Use the 'default' categories here, instead of all rootcats
        $basecats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications'));

        $catlist = array();
        foreach ($basecats as $basecat) {
            $catlist[$basecat['category_id']] = 1;
        }
        $data['basecids'] = array_keys($catlist);

        // create the category tree for each root category
    // TODO: make sure permissions are taken into account here !
        foreach ($catlist as $cid => $val) {
            if (empty($val)) {
                continue;
            }
            $data['cattree'][$cid] = xarMod::apiFunc('publications',
                                                   'user',
                                                   'getchildcats',
                                                         // frontpage or approved
                                                   array('state' => array(PUBLICATIONS_STATE_APPROVED,PUBLICATIONS_STATE_FRONTPAGE),
                                                         'cid' => $cid,
                                                         'ptid' => null,
                                                         // keep a link to the parent cid
                                                         'showcid' => true));
        }

    } elseif ($by == 'grid') {

        $data['catgrid'][0] = array();
        $data['catgrid'][0][0] = '';

        // Get the base categories
        if (!empty($ptid)) {
            $rootcats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $ptid));
        } else {
            $rootcats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => 0));
            $ptid = null;
        }

        if (count($rootcats) != 2) {
            $data['catgrid'][0][0] = xarML('You need 2 base categories in order to use this grid view');
        } else {
            $catlist = array();
            if (!empty($rootcats) && is_array($rootcats)) {
                foreach ($rootcats as $cid) {
                    $catlist[$catid['category_id']] = 1;
                }
            }

            $cattree = array();
            // Get the category tree for each base category
            foreach ($catlist as $cid => $val) {
                if (empty($val)) {
                    continue;
                }
                $cattree[$cid] = xarMod::apiFunc('publications',
                                               'user',
                                               'getchildcats',
                                                   // frontpage or approved
                                               array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                                     'cid' => $cid,
                                                     'ptid' => $ptid,
                                                     // keep a link to the parent cid
                                                     'showcid' => true));
            }

            // Find out which category tree is the shortest
            if (count($cattree[$rootcats[0]]) > count($cattree[$rootcats[1]])) {
                $rowcat = $rootcats[0];
                $colcat = $rootcats[1];
            } else {
                $rowcat = $rootcats[1];
                $colcat = $rootcats[0];
            }

            // Fill in the column headers
            $row = 0;
            $col = 1;
            $colcid = array();
            foreach ($cattree[$colcat] as $info) {
                $data['catgrid'][$row][$col] = '<a href="' . $info['link'] . '">' . $info['name'] . '</a>';
                $colcid[$info['id']] = $col;
                $col++;
            }
            $maxcol = $col;

            // Fill in the row headers
            $row = 1;
            $col = 0;
            $data['catgrid'][$row] = array();
            $rowcid = array();
            foreach ($cattree[$rowcat] as $info) {
                $data['catgrid'][$row][$col] = '<a href="' . $info['link'] . '">' . $info['name'] . '</a>';
                $rowcid[$info['id']] = $row;
                $row++;
            }
            $maxrow = $row;

            // Initialise the rest of the array
            for ($row = 1; $row < $maxrow; $row++) {
                if (!isset($data['catgrid'][$row])) {
                    $data['catgrid'][$row] = array();
                }
                for ($col = 1; $col < $maxcol; $col++) {
                    $data['catgrid'][$row][$col] = '';
                }
            }

            // Get the counts for all groups of (N) categories
            $pubcatcount = xarMod::apiFunc('publications',
                                         'user',
                                         'getpubcatcount',
                                         // frontpage or approved
                                         array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                               'ptid' => $ptid,
                                               'groupcids' => 2,
                                               'reverse' => 1));

            if (!empty($ptid)) {
                $what = $ptid;
            } else {
                $what = 'total';
            }
            // Fill in the count values
            foreach ($pubcatcount as $cids => $counts) {
                list($ca,$cb) = explode('+',$cids);
                if (isset($rowcid[$ca]) && isset($colcid[$cb])) {
                    $link = xarController::URL('publications','user','view',
                                      array('ptid' => $ptid,
                                            'catid' => $ca . '+' . $cb));
                    $data['catgrid'][$rowcid[$ca]][$colcid[$cb]] = '<a href="' . $link . '"> ' . $counts[$what] . ' </a>';
                }
                if (isset($rowcid[$cb]) && isset($colcid[$ca])) {
                    $link = xarController::URL('publications','user','view',
                                      array('ptid' => $ptid,
                                            'catid' => $cb . '+' . $ca));
                    $data['catgrid'][$rowcid[$cb]][$colcid[$ca]] = '<a href="' . $link . '"> ' . $counts[$what] . ' </a>';
                }
            }
        }

        if (!empty($ptid)) {
            $descr = $data['pubtypes'][$ptid]['description'];
        }

    } else {
        $data['maplink'] = xarController::URL('publications','user','viewmap',array('by' => 'pub'));

        // get the links and counts for all publication types
        $publinks = xarMod::apiFunc('publications','user','getpublinks',
                                  array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                        'all' => 1));

        // build the list of root categories for all publication types
        // and save results in publinks as well
        $catlist = array();
        for ($i=0;$i<count($publinks);$i++) {
            $pubid = $publinks[$i]['pubid'];
            $cidstring = xarModVars::get('publications','mastercids.'.$pubid);
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
                $childcats =  xarMod::apiFunc('publications',
                                            'user',
                                            'getchildcats',
                                            // frontpage or approved
                                            array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                                  'cid' => $cid,
                                                  'ptid' => $pubid,
                                                  // keep a link to the parent cid
                                                  'showcid' => true));
                $publinks[$i]['cats'][] = $childcats;
            }
        }

        $array = array();
        if (empty($ptid)) {
            $ptid = $default;
        }
        if (!empty($ptid)) {
            for ($i = 0; $i < count($publinks); $i++) {
                if ($ptid == $publinks[$i]['pubid']) {
                    $array = $publinks[$i]['rootcats'];
                }
            }
        }

        foreach ($publinks as $pub) {
            if ($pub['pubid'] == $ptid) {
                $descr = $pub['pubtitle'];
            }
        }
    }

    if (empty($descr)) {
        $descr = xarML('Publications');
        $data['descr'] = '';
    } else {
        $data['descr'] = $descr;
    }

    // Save some variables to (temporary) cache for use in blocks etc.
    xarCoreCache::setCached('Blocks.publications','ptid',$ptid);
//if ($shownavigation) {
    xarCoreCache::setCached('Blocks.categories','module','publications');
    xarCoreCache::setCached('Blocks.categories','itemtype',$ptid);
    if (!empty($descr)) {
        xarCoreCache::setCached('Blocks.categories','title',$descr);
        xarTpl::setPageTitle( xarML('Map'), xarVar::prepForDisplay($descr));
    }
//}

    if (empty($ptid)) {
        $ptid = null;
    }
    $data['publinks'] = $publinks;
    $data['ptid'] = $ptid;
    $data['viewlabel'] = xarML('Back to') . ' ' . $descr;
    $data['viewlink'] = xarController::URL('publications','user','view',
                                  array('ptid' => $ptid));
    $data['archivelabel'] = xarML('View Archives');
    $data['archivelink'] = xarController::URL('publications','user','archive',
                                     array('ptid' => $ptid));
    $data['dump'] = $dump;
    if (count($data['catfilter']) == 2) {
    }

    if (!empty($ptid)) {
        $object = DataObjectMaster::getObject(array('name' => 'publications_types'));
        $object->getItem(array('itemid' => $ptid));
        $template = $object->properties['template']->value;
    } else {
// TODO: allow templates per category ?
       $template = null;
    }
    
    // Pass the type of map to the template, so we can decide what links to show
    $data['by'] = $by;
    
    return xarTpl::module('publications', 'user', 'viewmap', $data, $template);
}

?>