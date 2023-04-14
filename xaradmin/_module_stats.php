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
function comments_admin_module_stats( )
{

    // Security Check
    if(!xarSecurity::check('AdminComments')) return;
    if (!xarVar::fetch('modid','int:1',$modid)) return;
    if (!xarVar::fetch('itemtype','int:0',$itemtype,0,xarVar::NOT_REQUIRED)) return;

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Invalid or Missing Parameter \'modid\'');
        throw new BadParameterException($msg);
    }

    $modinfo = xarMod::getInfo($modid);
    if (empty($itemtype)) {
        $data['modname'] = ucwords($modinfo['displayname']);
        $itemtype = 0;
    } else {
        // Get the list of all item types for this module (if any)
        $mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        if (isset($mytypes) && !empty($mytypes[$itemtype])) {
            $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
        //    $data['modlink'] = $mytypes[$itemtype]['url'];
        } else {
            $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
        //    $data['modlink'] = xarController::URL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
        }
    }

    $numstats = xarModVars::get('comments','numstats');
    if (empty($numstats)) {
        $numstats = 100;
    }
    if (!xarVar::fetch('startnum', 'id', $startnum, NULL, xarVar::DONT_SET)) return;
    if (empty($startnum)) {
        $startnum = 1;
    }

    // get all items and their number of comments (excluding root nodes) for this module
    $moditems = xarMod::apiFunc('comments','user','getitems',
                              array('moditemscommentcount' => true,
                                    'modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'numitems' => $numstats,
                                    'startnum' => $startnum));

    // get the number of inactive comments for these items
    $inactive = xarMod::apiFunc('comments','user','getitems',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemids' => array_keys($moditems),
                                    'status' => 'inactive'));

    // get the title and url for the items
    $showtitle = xarModVars::get('comments','showtitle');
    if (!empty($showtitle)) {
       $itemids = array_keys($moditems);
       try{
           $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                                      array('itemtype' => $itemtype,
                                            'itemids' => $itemids)); // don't throw an exception here
        } catch (Exception $e) {}
    } else {
       $itemlinks = array();
    }

    $pages = array();

    $data['gt_total']     = 0;
    $data['gt_inactive']  = 0;

    foreach ($moditems as $itemid => $numcomments) {
        $pages[$itemid] = array();
        $pages[$itemid]['pageid'] = $itemid;
        $pages[$itemid]['total'] = $numcomments;
        $pages[$itemid]['delete_url'] = xarController::URL('comments','admin', 'delete',
                                                  array('dtype' => 'object',
                                                        'modid' => $modid,
                                                        'itemtype' => $itemtype,
                                                        'objectid' => $itemid));
        $data['gt_total'] .= $numcomments;
        if (isset($inactive[$itemid])) {
            $pages[$itemid]['inactive'] = $inactive[$itemid];
            $data['gt_inactive'] += $inactive[$itemid];
        } else {
            $pages[$itemid]['inactive'] = 0;
        }
        if (isset($itemlinks[$itemid])) {
            $pages[$itemid]['link'] = $itemlinks[$itemid]['url'];
            $pages[$itemid]['title'] = $itemlinks[$itemid]['label'];
        }
    }

    $data['data']             = $pages;
    $data['delete_all_url']   = xarController::URL('comments','admin','delete',
                                            array('dtype' => 'module',
                                                  'modid' => $modid,
                                                  'itemtype' => $itemtype));

    // get statistics for all comments (excluding root nodes)
    $modlist = xarMod::apiFunc('comments','user','getmodules',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype));
    if (isset($modlist[$modid]) && isset($modlist[$modid][$itemtype])) {
        $numitems = $modlist[$modid][$itemtype]['items'];
    } else {
        $numitems = 0;
    }
    if ($numstats < $numitems) {
        $data['pager'] = xarTplPager::getPager($startnum,
                                          $numitems,
                                          xarController::URL('comments','admin','module_stats',
                                                    array('modid' => $modid,
                                                          'itemtype' => $itemtype,
                                                          'startnum' => '%%')),
                                          $numstats);
    } else {
        $data['pager'] = '';
    }

    return $data;

}

?>
