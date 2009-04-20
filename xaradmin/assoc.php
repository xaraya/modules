<?php
/**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 *//**
 * Purpose of File
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Uploads Module
 * @link http://xaraya.com/index.php/release/666.html
 * @author Uploads Module Development Team
 */

/**
 * View statistics about file associations (adapted from categories stats)
 */
function uploads_admin_assoc()
{
    // Security Check
    if (!xarSecurityCheck('AdminUploads')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('startnum', 'isset', $startnum,     1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('fileId',   'isset', $fileId,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('action',   'isset', $action,    NULL, XARVAR_DONT_SET)) {return;}

    if (empty($fileId) || !is_numeric($fileId)) {
        $fileId = null;
    }
    if (!empty($modid) && empty($itemtype)) {
        $itemtype = 0;
    }

    if (!empty($action)) {
        if ($action == 'rescan') {
            $result = xarModAPIFunc('uploads','admin','rescan_associations',
                                    array('modid' => $modid,
                                          'itemtype' => $itemtype,
                                          'itemid' => $itemid,
                                          'fileId' => $fileId));
            if (!$result) return;

        } elseif ($action == 'missing') {
            $missing = xarModAPIFunc('uploads','admin','check_associations');
            if (!isset($missing)) return;

        } elseif ($action == 'delete' && !empty($modid)) {
            if(!xarVarFetch('confirm', 'isset', $confirm, NULL, XARVAR_DONT_SET)) {return;}
            if (!empty($confirm)) {
                // Confirm authorisation code.
                if (!xarSecConfirmAuthKey()) return;
                $result = xarModAPIFunc('uploads','admin','delete_associations',
                                        array('modid' => $modid,
                                              'itemtype' => $itemtype,
                                              'itemid' => $itemid,
                                              'fileId' => $fileId));
                if (!$result) return;
                xarResponse::Redirect(xarModURL('uploads','admin','assoc'));
                return true;
            }
        }
    }

    $data = array();
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;
    $data['fileId'] = $fileId;
    if (!empty($missing)) {
        $data['missing'] = $missing;
    }

    $modlist = xarModAPIFunc('uploads','user','db_group_associations',
                             array('fileId' => $fileId));

    if (empty($modid)) {
        $data['moditems'] = array();
        $data['numitems'] = 0;
        $data['numlinks'] = 0;
        foreach ($modlist as $modid => $itemtypes) {
            $modinfo = xarModGetInfo($modid);
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            foreach ($itemtypes as $itemtype => $stats) {
                $moditem = array();
                $moditem['numitems'] = $stats['items'];
                $moditem['numfiles'] = $stats['files'];
                $moditem['numlinks'] = $stats['links'];
                if ($itemtype == 0) {
                    $moditem['name'] = ucwords($modinfo['displayname']);
                //    $moditem['link'] = xarModURL($modinfo['name'],'user','main');
                } else {
                    if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                        $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                    //    $moditem['link'] = $mytypes[$itemtype]['url'];
                    } else {
                        $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                    //    $moditem['link'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                    }
                }
                $moditem['link'] = xarModURL('uploads','admin','assoc',
                                             array('modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype,
                                                   'fileId' => $fileId));
                $moditem['rescan'] = xarModURL('uploads','admin','assoc',
                                               array('action' => 'rescan',
                                                     'modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype,
                                                     'fileId' => $fileId));
                $moditem['delete'] = xarModURL('uploads','admin','assoc',
                                               array('action' => 'delete',
                                                     'modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype,
                                                     'fileId' => $fileId));
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numlinks'] += $moditem['numlinks'];
            }
        }
        $data['rescan'] = xarModURL('uploads','admin','assoc',
                                    array('action' => 'rescan',
                                          'fileId' => $fileId));
        $data['delete'] = xarModURL('uploads','admin','assoc',
                                    array('action' => 'delete',
                                          'fileId' => $fileId));
        if (!empty($fileId)) {
            $data['fileinfo'] = xarModAPIFunc('uploads','user','db_get_file',
                                             array('fileId' => $fileId));
        }
    } else {
        $modinfo = xarModGetInfo($modid);
        $data['module'] = $modinfo['name'];
        if (empty($itemtype)) {
            $data['modname'] = ucwords($modinfo['displayname']);
            $itemtype = null;
            if (isset($modlist[$modid][0])) {
                $stats = $modlist[$modid][0];
            }
        } else {
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
            //    $data['modlink'] = $mytypes[$itemtype]['url'];
            } else {
                $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
            //    $data['modlink'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
            }
            if (isset($modlist[$modid][$itemtype])) {
                $stats = $modlist[$modid][$itemtype];
            }
        }
        if (isset($stats)) {
            $data['numitems'] = $stats['items'];
            $data['numlinks'] = $stats['links'];
        } else {
            $data['numitems'] = 0;
            $data['numlinks'] = '';
        }
        $numstats = xarModVars::get('uploads','numstats');
        if (empty($numstats)) {
            $numstats = 100;
        }
/*
        if (!empty($fileId)) {
            $data['numlinks'] = xarModAPIFunc('uploads','user','db_count_associations',
                                              array('modid' => $modid,
                                                    'itemtype' => $itemtype,
                                                    'fileId' => $fileId));
        }
*/
        if ($numstats < $data['numlinks']) {
            $data['pager'] = xarTplGetPager($startnum,
                                            $data['numlinks'],
                                            xarModURL('uploads','admin','assoc',
                                                      array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'fileId' => $fileId,
                                                            'sort' => $sort,
                                                            'startnum' => '%%')),
                                            $numstats);
        } else {
            $data['pager'] = '';
        }
        $getitems = xarModAPIFunc('uploads','user','db_list_associations',
                                  array('modid' => $modid,
                                        'itemtype' => $itemtype,
                                        'itemid' => $itemid,
                                        'numitems' => $numstats,
                                        'startnum' => $startnum,
                                        'sort' => $sort,
                                        'fileId' => $fileId));
        //$showtitle = xarModVars::get('uploads','showtitle');
        $showtitle = true;
        if (!empty($getitems) && !empty($showtitle)) {
           $itemids = array_keys($getitems);
           $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                      array('itemtype' => $itemtype,
                                            'itemids' => $itemids),
                                      0); // don't throw an exception here
        } else {
           $itemlinks = array();
        }
        $seenfileid = array();
        if (!empty($fileId)) {
            $seenfileid[$fileId] = 1;
        }
        $data['moditems'] = array();
        foreach ($getitems as $itemid => $filelist) {
            $data['moditems'][$itemid] = array();
            $data['moditems'][$itemid]['numlinks'] = count($filelist);
            $data['moditems'][$itemid]['filelist'] = $filelist;
            foreach ($filelist as $id) {
                $seenfileid[$id] = 1;
            }
            $data['moditems'][$itemid]['rescan'] = xarModURL('uploads','admin','assoc',
                                                             array('action' => 'rescan',
                                                                   'modid' => $modid,
                                                                   'itemtype' => $itemtype,
                                                                   'itemid' => $itemid,
                                                                   'fileId' => $fileId));
            $data['moditems'][$itemid]['delete'] = xarModURL('uploads','admin','assoc',
                                                             array('action' => 'delete',
                                                                   'modid' => $modid,
                                                                   'itemtype' => $itemtype,
                                                                   'itemid' => $itemid,
                                                                   'fileId' => $fileId));
            if (isset($itemlinks[$itemid])) {
                $data['moditems'][$itemid]['link'] = $itemlinks[$itemid]['url'];
                $data['moditems'][$itemid]['title'] = $itemlinks[$itemid]['label'];
            }
        }
        unset($getitems);
        unset($itemlinks);
        if (!empty($seenfileid)) {
            $data['fileinfo'] = xarModAPIFunc('uploads','user','db_get_file',
                                             array('fileId' => array_keys($seenfileid)));
        } else {
            $data['fileinfo'] = array();
        }
        $data['rescan'] = xarModURL('uploads','admin','assoc',
                                    array('action' => 'rescan',
                                          'modid' => $modid,
                                          'itemtype' => $itemtype,
                                          'fileId' => $fileId));
        $data['delete'] = xarModURL('uploads','admin','assoc',
                                    array('action' => 'delete',
                                          'modid' => $modid,
                                          'itemtype' => $itemtype,
                                          'fileId' => $fileId));
        $data['sortlink'] = array();
        if (empty($sort) || $sort == 'itemid') {
             $data['sortlink']['itemid'] = '';
        } else {
             $data['sortlink']['itemid'] = xarModURL('uploads','admin','assoc',
                                                     array('modid' => $modid,
                                                           'itemtype' => $itemtype,
                                                           'fileId' => $fileId));
        }
        if (!empty($sort) && $sort == 'numlinks') {
             $data['sortlink']['numlinks'] = '';
        } else {
             $data['sortlink']['numlinks'] = xarModURL('uploads','admin','assoc',
                                                      array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'fileId' => $fileId,
                                                            'sort' => 'numlinks'));
        }

        if (!empty($action) && $action == 'delete') {
            $data['action'] = 'delete';
            $data['authid'] = xarSecGenAuthKey();
        }
    }

    return $data;
}

?>
