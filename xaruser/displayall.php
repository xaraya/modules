<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Display comments from one or more modules and item types
 *
 */
function comments_user_displayall($args)
{
    if (!xarVar::fetch('modid', 'array', $args['modid'], array('all'), xarVar::NOT_REQUIRED)) {
        return;
    };
    if (!xarVar::fetch('itemtype', 'int', $args['itemtype'], null, xarVar::NOT_REQUIRED)) {
        return;
    };
    if (!xarVar::fetch('order', 'str', $args['order'], 'DESC', xarVar::GET_OR_POST)) {
        return;
    };
    if (!xarVar::fetch('howmany', 'id', $args['howmany'], 20, xarVar::GET_OR_POST)) {
        return;
    };
    if (!xarVar::fetch('first', 'id', $args['first'], 1, xarVar::GET_OR_POST)) {
        return;
    };

    if (empty($args['block_is_calling'])) {
        $args['block_is_calling'] = 0;
    }
    if (empty($args['truncate'])) {
        $args['truncate'] = '';
    }
    if (!isset($args['addmodule'])) {
        $args['addmodule'] = 'off';
    }
    if (!isset($args['addobject'])) {
        $args['addobject'] = 21;
    }
    if (!isset($args['addcomment'])) {
        $args['addcomment'] = 20;
    }
    if (!isset($args['adddate'])) {
        $args['adddate'] = 'on';
    }
    if (!isset($args['adddaysep'])) {
        $args['adddaysep'] = 'on';
    }
    if (!isset($args['addauthor'])) {
        $args['addauthor'] = 1;
    }
    if (!isset($args['addprevious'])) {
        $args['addprevious'] = 0;
    }

    /*$args['returnurl'] = '';*/
    $modarray = $args['modid'];
    // get the list of modules+itemtypes that comments is hooked to
    $hookedmodules = xarMod::apiFunc(
        'modules',
        'admin',
        'gethookedmodules',
        array('hookModName' => 'comments')
    );

    // initialize list of module and pubtype names
    $modlist = array();
    $modname = array();
    $modview = array();
    $modlist['all'] = xarML('All');
    // make sure we only retrieve comments from hooked modules
    $todolist = array();
    if (isset($hookedmodules) && is_array($hookedmodules)) {
        foreach ($hookedmodules as $module => $value) {
            $modid = xarMod::getRegID($module);
            if (!isset($modname[$modid])) {
                $modname[$modid] = array();
            }
            if (!isset($modview[$modid])) {
                $modview[$modid] = array();
            }
            $modname[$modid][0] = ucwords($module);
            $modview[$modid][0] = xarController::URL($module, 'user', 'view');
            // Get the list of all item types for this module (if any)
            $mytypes = xarMod::apiFunc(
                $module,
                'user',
                'getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(),
                0
            );
            if (!empty($mytypes) && count($mytypes) > 0) {
                foreach (array_keys($mytypes) as $itemtype) {
                    $modname[$modid][$itemtype] = $mytypes[$itemtype]['label'];
                    $modview[$modid][$itemtype] = $mytypes[$itemtype]['url'];
                }
            }
            // we have hooks for individual item types here
            if (!isset($value[0])) {
                foreach ($value as $itemtype => $val) {
                    $todolist[] = "$module.$itemtype";
                    if (isset($mytypes[$itemtype])) {
                        $type = $mytypes[$itemtype]['label'];
                    } else {
                        $type = xarML('type #(1)', $itemtype);
                    }
                    $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $type;
                }
            } else {
                $todolist[] = $module;
                $modlist[$module] = ucwords($module);
                // allow selecting individual item types here too (if available)
                if (!empty($mytypes) && count($mytypes) > 0) {
                    foreach ($mytypes as $itemtype => $mytype) {
                        if (!isset($mytype['label'])) {
                            continue;
                        }
                        $modlist["$module.$itemtype"] = ucwords($module) . ' - ' . $mytype['label'];
                    }
                }
            }
        }
    }

    // replace 'all' with the list of hooked modules (+ xarbb if necessary ?)
    if (count($modarray) == 1 && $modarray[0] == 'all') {
        $args['modarray'] = $todolist;
    } else {
        $args['modarray'] = $modarray;
    }

    $comments = xarMod::apiFunc('comments', 'user', 'get_multipleall', $args);
    // Bug 6188: why is this needed? getoptions needs one hooked module to get the options for
    //$settings = xarMod::apiFunc('comments','user','getoptions');

    if (!empty($args['order'])) {
        $settings['order']=$args['order'];
    }

    // get title and link for all module items (where possible)
    $items = array();
    if (!empty($args['addobject'])) {
        // build a list of item ids per module and item type
        foreach (array_keys($comments) as $i) {
            $modid = $comments[$i]['modid'];
            $itemtype = $comments[$i]['itemtype'];
            if (!isset($items[$modid])) {
                $items[$modid] = array();
            }
            if (!isset($items[$modid][$itemtype])) {
                $items[$modid][$itemtype] = array();
            }
            $itemid = $comments[$i]['objectid'];
            $items[$modid][$itemtype][$itemid] = '';
        }
        // for each module and itemtype, retrieve the item links (if available)
        foreach ($items as $modid => $itemtypes) {
            $modinfo = xarMod::getInfo($modid);
            foreach ($itemtypes as $itemtype => $itemlist) {
                try {
                    $itemlinks = xarMod::apiFunc(
                        $modinfo['name'],
                        'user',
                        'getitemlinks',
                        array('itemtype' => $itemtype,
                                                     'itemids' => array_keys($itemlist))
                    );
                } catch (Exception $e) {
                }
                if (!empty($itemlinks) && count($itemlinks) > 0) {
                    foreach ($itemlinks as $itemid => $itemlink) {
                        $items[$modid][$itemtype][$itemid] = $itemlink;
                    }
                }
            }
        }
    }

    // generate comments array of arrays; each date has an array of comments
    // posted on that date
    $commentsarray = array();
    $timenow = time();
    $hoursnow = xarLocale::formatDate("%H", $timenow);
    $dateprev = '';
    $numcomments = count($comments);
    for ($i=0;$i<$numcomments;$i++) {
        if ($args['adddaysep']=='on') {
            // find out whether to change day separator
            $msgunixtime=$comments[$i]['datetime'];
            $msgdate=xarLocale::formatDate("%b %d, %Y", $msgunixtime);
            $msgday=xarLocale::formatDate("%A", $msgunixtime);

            $hoursdiff=($timenow - $msgunixtime)/3600;
            if ($hoursdiff<$hoursnow && $msgdate!=$dateprev) {
                $daylabel=xarML('Today');
            } elseif ($hoursdiff>=$hoursnow && $hoursdiff<$hoursnow+24 && ($msgdate!=$dateprev)) {
                $daylabel=xarML('Yesterday');
            } elseif ($hoursdiff>=$hoursnow+24 && $hoursdiff<$hoursnow+48 && $msgdate!=$dateprev) {
                $daylabel=xarML('Two days ago');
            } elseif ($hoursdiff>=$hoursnow+48 && $hoursdiff<$hoursnow+144 && $msgdate!=$dateprev) {
                $daylabel=$msgday;
            } elseif ($hoursdiff>=$hoursnow+144 && $msgdate!=$dateprev) {
                $daylabel=$msgdate;
            }
            $dateprev=$msgdate;
        } else {
            // no need to keep track of date
            $daylabel = 'none';
        }

        // add title, url and truncate comments if requested
        $modid = $comments[$i]['modid'];
        $itemtype = $comments[$i]['itemtype'];
        $itemid = $comments[$i]['objectid'];
        if (!empty($args['addobject']) && !empty($items[$modid][$itemtype][$itemid])) {
            $comments[$i]['title'] = $items[$modid][$itemtype][$itemid]['label'];
            $comments[$i]['objurl'] = $items[$modid][$itemtype][$itemid]['url'];
        }
        if (isset($modname[$modid][$itemtype])) {
            $comments[$i]['modname']=$modname[$modid][$itemtype];
        }
        if (isset($modview[$modid][$itemtype])) {
            $comments[$i]['modview']=$modview[$modid][$itemtype];
        }

        //$comments[$i]['returnurl'] = urlencode($modview[$modid][$itemtype]);
        //$comments[$i]['returnurl'] = null;
        if ($args['truncate']) {
            if (strlen($comments[$i]['subject']) >$args['truncate']+3) {
                $comments[$i]['subject']=substr($comments[$i]['subject'], 0, $args['truncate']).'...';
            }
            if (!empty($comments[$i]['title']) && strlen($comments[$i]['title']) >$args['truncate']-3) {
                $comments[$i]['title']=substr($comments[$i]['title'], 0, $args['truncate']).'...';
            }
        }
        $comments[$i]['subject'] = xarVar::prepForDisplay($comments[$i]['subject']);
        if (!empty($comments[$i]['text'])) {
            $comments[$i]['text'] = xarVar::prepHTMLDisplay($comments[$i]['text']);
        }
        list($comments[$i]['text'],
             $comments[$i]['subject']) = xarModHooks::call(
                 'item',
                 'transform',
                 $comments[$i]['id'],
                 array($comments[$i]['text'],
                                                                   $comments[$i]['subject']),
                 'comments'
             );
        $commentsarray[$daylabel][] = $comments[$i];
    }

    // prepare for output
    $templateargs['order']          =$args['order'];
    $templateargs['howmany']        =$args['howmany'];
    $templateargs['first']          =$args['first'];
    $templateargs['adddate']        =$args['adddate'];
    $templateargs['adddaysep']      =$args['adddaysep'];
    $templateargs['addauthor']      =$args['addauthor'];
    $templateargs['addmodule']      =$args['addmodule'];
    $templateargs['addcomment']     =$args['addcomment'];
    $templateargs['addobject']      =$args['addobject'];
    $templateargs['addprevious']    =$args['addprevious'];
    $templateargs['modarray']       =$modarray;
    $templateargs['modid']          =$modarray;
    $templateargs['itemtype']       =isset($itemtype)?$itemtype:0;
    $templateargs['modlist']        =$modlist;
    /*$templateargs['decoded_returnurl'] = rawurldecode(xarController::URL('comments','user','displayall'));*/
    $templateargs['decoded_nexturl'] = xarController::URL(
        'comments',
        'user',
        'displayall',
        array(
                                                                         'first'=>$args['first']+$args['howmany'],
                                                                            'howmany'=>$args['howmany'],
                                                                            'modid'=>$modarray)
    );
    $templateargs['commentlist']    =$commentsarray;
    $templateargs['order']          =$settings['order'];

    if ($args['block_is_calling']==0) {
        $data=xarTpl::module('comments', 'user', 'displayall', $templateargs);
    } else {
        $templateargs['olderurl']=xarController::URL(
            'comments',
            'user',
            'displayall',
            array(
                                                'first'=>   $args['first']+$args['howmany'],
                                                'howmany'=> $args['howmany'],
                                                'modid'=>$modarray
                                                )
        );
        $data = xarTpl::block('comments', 'latestcommentsblock', $templateargs);
    }

    return $data;
}
