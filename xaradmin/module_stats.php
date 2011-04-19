<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
function comments_admin_module_stats( )
{

    // Security Check
    if(!xarSecurityCheck('AdminComments')) return;
    if (!xarVarFetch('modid','int:1',$modid)) return;
    if (!xarVarFetch('itemtype','int:0',$urlitemtype,0,XARVAR_NOT_REQUIRED)) return;

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Invalid or Missing Parameter \'modid\'');
        throw new BadParameterException($msg);
    }

    $modinfo = xarModGetInfo($modid);
    $data['modname'] = ucwords($modinfo['displayname']);
    if (empty($urlitemtype)) {
        $urlitemtype = -1;
    } else {
        $data['itemtype'] = $urlitemtype;
        $mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes', array(), 0);
        if (isset($mytypes) && !empty($mytypes[$urlitemtype])) {
            $data['itemtypelabel'] = $mytypes[$urlitemtype]['label'];
            //$data['modlink'] = $mytypes[$urlitemtype]['url'];
        } else {
            //$data['modlink'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $urlitemtype));
        }
    }

    $numstats = xarModVars::get('comments','numstats');
    if (empty($numstats)) {
        $numstats = 100;
    }
    if (!xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_DONT_SET)) return;
    if (empty($startnum)) {
        $startnum = 1;
    }

    $args = array('modid' => $modid, 'numitems' => $numstats, 'startnum' => $startnum);
    if (!xarVarFetch('itemtype','int',$itemtypearg,NULL,XARVAR_NOT_REQUIRED)) return;
    if (isset($itemtypearg)) {
        $args['itemtype'] = $itemtypearg;
    }
    // all the items and their number of comments (excluding root nodes) for this module
    $moditems = xarMod::apiFunc('comments','user','moditemcounts',
                              $args);

    // inactive
    $args['status'] = 'inactive';
    $inactive = xarMod::apiFunc('comments','user','moditemcounts',
                              $args);

    // get the title and url for the items
    $showtitle = xarModVars::get('comments','showtitle');
    if (!empty($showtitle)) {
       $itemids = array_keys($moditems);
       try{
           $itemlinks = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                                      array('itemtype' => $urlitemtype,
                                            'itemids' => $itemids)); // don't throw an exception here
        } catch (Exception $e) {}
    } else {
       $itemlinks = array();
    }

    $stats = array();

    $data['gt_total']     = 0;
    $data['gt_inactive']  = 0;

    foreach ($moditems as $itemid => $info) {
        $stats[$itemid] = array();
        $stats[$itemid]['pageid'] = $itemid;
        $stats[$itemid]['total'] = $info['count'];
        $stats[$itemid]['delete_url'] = xarModURL('comments','admin', 'delete',
                                                  array('dtype' => 'object',
                                                        'modid' => $modid,
                                                        'itemtype' => $info['itemtype'],
                                                        'objectid' => $itemid,
                                                        'redirect' => $modid
                                                  ));
        $data['gt_total'] += $info['count'];
        if (isset($inactive[$itemid])) {
            $stats[$itemid]['inactive'] = $inactive[$itemid]['count'];
            $data['gt_inactive'] += (int)$inactive[$itemid]['count'];
        } else {
            $stats[$itemid]['inactive'] = 0;
        }
        if (isset($itemlinks[$itemid])) {
            $stats[$itemid]['link'] = $itemlinks[$itemid]['url'];
            $stats[$itemid]['title'] = $itemlinks[$itemid]['label'];
        }
    }

    $data['data']             = $stats;
    if (isset($urlitemtype) && $urlitemtype > 0) {
        $dalltype = 'itemtype';
    } else {
        $dalltype = 'module';
    }
    $data['delete_all_url']   = xarModURL('comments','admin','delete',
                                            array('dtype' => $dalltype,
                                                  'modid' => $modid,
                                                  'itemtype' => $urlitemtype,
                                                    'redirect' => 'stats'
                                            ));

    // get statistics for all comments (excluding root nodes)
    $modlist = xarMod::apiFunc('comments','user','modcounts',
                             array('modid' => $modid,
                                   'itemtype' => $urlitemtype));
    if (isset($modlist[$modid]) && isset($modlist[$modid][$urlitemtype])) {
        $numitems = $modlist[$modid][$urlitemtype]['items'];
    } else {
        $numitems = 0;
    }
    if ($numstats < $numitems) {
        $data['pager'] = xarTplPager::getPager($startnum,
                                          $numitems,
                                          xarModURL('comments','admin','module_stats',
                                                    array('modid' => $modid,
                                                          'itemtype' => $urlitemtype,
                                                          'startnum' => '%%')),
                                          $numstats);
    } else {
        $data['pager'] = '';
    }

    return $data;

}

?>
