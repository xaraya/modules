<?php

/**
 * File: $Id$
 *
 * Comments administration display functions
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @author Carl P. Corliss <rabbitt@xaraya.com>
*/

include_once('modules/comments/xarincludes/defines.php');


/**
 * Overview Menu
 */
function comments_admin_main()
{
    if(!xarSecurityCheck('Comments-Admin')){
        return;
    }
    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0){
        xarResponseRedirect(xarModURL('comments', 'admin', 'view'));
    } else {
        xarResponseRedirect(xarModURL('comments', 'admin', 'stats'));
    }
    // success
    return true;
}

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function comments_admin_modifyconfig()
{

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    $numstats = xarModGetVar('comments','numstats');
    if (empty($numstats)) {
        xarModSetVar('comments', 'numstats', 100);
    }

    $output['authid'] = xarSecGenAuthKey();

    return $output;
}

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function comments_admin_view()
{

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    return array();
}

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function comments_admin_updateconfig()
{
    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    // Get parameters
    $xar_depth      = xarVarCleanFromInput('xar_depth');
    $xar_render     = xarVarCleanFromInput('xar_render');
    $xar_sortby     = xarVarCleanFromInput('xar_sortby');
    $xar_order      = xarVarCleanFromInput('xar_order');
    $xar_postanon   = xarVarCleanFromInput('xar_postanon');
    $xar_authorize  = xarVarCleanFromInput('xar_authorize');

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey())
        return;

    // Update module variables.
    if (isset($xar_postanon) && $xar_postanon == 'on') {
        $xar_postanon = 1;
    } else {
        $xar_postanon = 0;
    }

    if (isset($xar_authorize) && $xar_authorize == 'on') {
        $xar_authorize = 1;
    } else {
        $xar_authorize = 0;
    }

    if (!isset($xar_depth)) {
        $xar_depth = _COM_MAX_DEPTH;
    }

    if (!isset($xar_render)) {
        $xar_render = _COM_VIEW_THREADED;
    }

    if (!isset($xar_sortby)) {
        $xar_sortby = _COM_SORTBY_THEAD;
    }

    if (!isset($xar_order)) {
        $xar_order = _COM_SORT_ASC;
    }

    xarModSetVar('comments', 'AllowPostAsAnon', $xar_postanon);
    xarModSetVar('comments', 'AuthorizeComments', $xar_authorize);
    xarModSetVar('comments', 'depth', $xar_depth);
    xarModSetVar('comments', 'render', $xar_render);
    xarModSetVar('comments', 'sortby', $xar_sortby);
    xarModSetVar('comments', 'order', $xar_order);

    if (!xarVarFetch('numstats', 'int', $numstats, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showtitle', 'checkbox', $showtitle, false, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('comments', 'numstats', $numstats);
    xarModSetVar('comments', 'showtitle', $showtitle);

    // Call updateconfig hooks
    xarModCallHooks('module', 'updateconfig', 'comments', array('module' => 'comments'));

    //Redirect
    xarResponseRedirect(xarModURL('comments', 'admin', 'modifyconfig'));

    // Return
    return true;
}


/**
 * View Statistics about comments per module
 *
 */
function comments_admin_stats() {

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    $output['gt_pages']     = 0;
    $output['gt_total']     = 0;
    $output['gt_inactive']  = 0;

    // get statistics for all comments (excluding root nodes)
    $modlist = xarModAPIFunc('comments','user','getmodules');

    // get statistics for all inactive comments
    $inactive = xarModAPIFunc('comments','user','getmodules',
                              array('status' => 'inactive'));

    $data = array();
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarModGetInfo($modid);
        // Get the list of all item types for this module (if any)
        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        foreach ($itemtypes as $itemtype => $stats) {
            $moditem = array();
            $moditem['pages'] = $stats['items'];
            $moditem['total'] = $stats['comments'];
            if (isset($inactive[$modid]) && isset($inactive[$modid][$itemtype])) {
                $moditem['inactive'] = $inactive[$modid][$itemtype]['comments'];
            } else {
                $moditem['inactive'] = 0;
            }
            if ($itemtype == 0) {
                $moditem['modname'] = ucwords($modinfo['displayname']);
            //    $moditem['modlink'] = xarModURL($modinfo['name'],'user','main');
            } else {
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                //    $moditem['modlink'] = $mytypes[$itemtype]['url'];
                } else {
                    $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                //    $moditem['modlink'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                }
            }
            $moditem['module_url'] = xarModURL('comments','admin','module_stats',
                                               array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype));
            $moditem['delete_url'] = xarModURL('comments','admin','delete',
                                               array('dtype' => 'module',
                                                     'modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype));
            $data[] = $moditem;
            $output['gt_pages'] += $moditem['pages'];
            $output['gt_total'] += $moditem['total'];
        }
    }
    $output['data']             = $data;
    $output['delete_all_url']   = xarModURL('comments',
                                            'admin',
                                            'delete',
                                            array('dtype' => 'all'));

    return $output;

}


function comments_admin_module_stats( ) {

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    $modid = xarVarCleanFromInput('modid');
    $itemtype = xarVarCleanFromInput('itemtype');

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Invalid or Missing Parameter \'modid\'');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $modinfo = xarModGetInfo($modid);
    if (empty($itemtype)) {
        $output['modname'] = ucwords($modinfo['displayname']);
        $itemtype = 0;
    } else {
        // Get the list of all item types for this module (if any)
        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        if (isset($mytypes) && !empty($mytypes[$itemtype])) {
            $output['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
        //    $output['modlink'] = $mytypes[$itemtype]['url'];
        } else {
            $output['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
        //    $output['modlink'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
        }
    }

    $numstats = xarModGetVar('comments','numstats');
    if (empty($numstats)) {
        $numstats = 100;
    }
    $startnum = xarVarCleanFromInput('startnum');
    if (empty($startnum)) {
        $startnum = 1;
    }

    // get all items and their number of comments (excluding root nodes) for this module
    $moditems = xarModAPIFunc('comments','user','getitems',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'numitems' => $numstats,
                                    'startnum' => $startnum));

    // get the number of inactive comments for these items
    $inactive = xarModAPIFunc('comments','user','getitems',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemids' => array_keys($moditems),
                                    'status' => 'inactive'));

    // get the title and url for the items
    $showtitle = xarModGetVar('comments','showtitle');
    if (!empty($showtitle)) {
       $itemids = array_keys($moditems);
       $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                  array('itemtype' => $itemtype,
                                        'itemids' => $itemids),
                                  0); // don't throw an exception here
    } else {
       $itemlinks = array();
    }

    $pages = array();

    $output['gt_total']     = 0;
    $output['gt_inactive']  = 0;

    foreach ($moditems as $itemid => $numcomments) {
        $pages[$itemid] = array();
        $pages[$itemid]['pageid'] = $itemid;
        $pages[$itemid]['total'] = $numcomments;
        $pages[$itemid]['delete_url'] = xarModURL('comments','admin', 'delete',
                                                  array('dtype' => 'object',
                                                        'modid' => $modid,
                                                        'itemtype' => $itemtype,
                                                        'objectid' => $itemid));
        $output['gt_total'] += $numcomments;
        if (isset($inactive[$itemid])) {
            $pages[$itemid]['inactive'] = $inactive[$itemid];
            $output['gt_inactive'] += $inactive[$itemid];
        } else {
            $pages[$itemid]['inactive'] = 0;
        }
        if (isset($itemlinks[$itemid])) {
            $pages[$itemid]['link'] = $itemlinks[$itemid]['url'];
            $pages[$itemid]['title'] = $itemlinks[$itemid]['label'];
        }
    }

    $output['data']             = $pages;
    $output['delete_all_url']   = xarModURL('comments','admin','delete',
                                            array('dtype' => 'module',
                                                  'modid' => $modid,
                                                  'itemtype' => $itemtype));

    // get statistics for all comments (excluding root nodes)
    $modlist = xarModAPIFunc('comments','user','getmodules',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype));
    if (isset($modlist[$modid]) && isset($modlist[$modid][$itemtype])) {
        $numitems = $modlist[$modid][$itemtype]['items'];
    } else {
        $numitems = 0;
    }
    if ($numstats < $numitems) {
        $output['pager'] = xarTplGetPager($startnum,
                                          $numitems,
                                          xarModURL('comments','admin','module_stats',
                                                    array('modid' => $modid,
                                                          'itemtype' => $itemtype,
                                                          'startnum' => '%%')),
                                          $numstats);
    } else {
        $output['pager'] = '';
    }

    return $output;

}


function comments_admin_delete( ) {

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    $dtype = xarVarCleanFromInput('dtype');

    $delete_args = array();

    if (!isset($dtype) || !eregi('^(all|module|object)$',$dtype)) {
        $msg = xarML('Invalid or Missing Parameter \'dtype\'');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } else {

        $delete_args['dtype'] = $dtype;
        $output['dtype'] = $dtype;

        switch (strtolower($dtype)) {
            case 'object':
                $objectid = xarVarCleanFromInput('objectid');

                if (!isset($objectid) || empty($objectid)) {
                    $msg = xarML('Invalid or Missing Parameter \'objectid\'');
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return;
                }
                $output['objectid'] = $objectid;
                $delete_args['objectid'] = $objectid;

            // if dtype == object, then fall through to
            // the module section below cuz we need both
            // the module id and the object id
            case 'module':
                $modid = xarVarCleanFromInput('modid');

                if (!isset($modid) || empty($modid)) {
                    $msg = xarML('Invalid or Missing Parameter \'modid\'');
                    xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                    return;
                }
                $itemtype = xarVarCleanFromInput('itemtype');
                if (empty($itemtype)) {
                    $itemtype = 0;
                }
                $modinfo = xarModGetInfo($modid);
                $output['modname']    = $modinfo['name'];
                $delete_args['modid'] = $modid;
                $delete_args['itemtype'] = $itemtype;
                break;
            case 'all':
                $output['modname']    = '\'ALL MODULES\'';
                break;
            default:
                $msg = xarML('Invalid or Missing Parameter \'dtype\'');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
                return;
        }
    }

    $submitted = xarVarCleanFromInput('submitted');

    // if we're gathering submitted info form the delete
    // confirmation then we are ok to check delete choice,
    // then delete in the manner specified (or not) and
    // then redirect to the Comment's Statistics page
    if (isset($submitted) && !empty($submitted)) {

        // Confirm authorisation code
        if (!xarSecConfirmAuthKey())
            return;

        $choice = xarVarCleanFromInput('choice');

        // if choice isn't set or it has an incorrect value,
        // redirect back to the choice page
        if (!isset($choice) || !eregi('^(yes|no|true|false)$',$choice)) {
            xarResponseRedirect(xarModURL('comments','admin','delete',$delete_args));
        }

        if($choice == 'yes' || $choice == 'true') {

            if (!xarModAPILoad('comments','user')) {
                die ("COULDN'T LOAD API!!!");
            }
            $retval = TRUE;

            switch (strtolower($dtype)) {
                case 'module':
                    xarModAPIFunc('comments','admin','delete_module_nodes',
                                   array('modid'=>$modid,
                                         'itemtype' => $itemtype));
                    break;
                case 'object':
                    xarModAPIFunc('comments','admin','delete_object_nodes',
                                   array('modid'    => $modid,
                                         'itemtype' => $itemtype,
                                         'objectid' => $objectid));
                    break;
                case 'all':
                    $dbconn =& xarDBGetConn();
                    $xartable =& xarDBGetTables();

                    $ctable = &$xartable['comments_column'];

                    $sql = "DELETE
                              FROM  $xartable[comments]";

                    $result =& $dbconn->Execute($sql);

                    break;
                default:
                    $retval = FALSE;
            }

            if (!$retval) {
                $msg = xarML('Unable to delete comments!');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'UNKNOWN', new SystemException($msg));
                return;
            }
        } else {
            if ( isset($modid) )  {
                xarResponseRedirect(xarModURL('comments','admin','module_stats',
                                              array('modid' => $modid,
                                                    'itemtype' => empty($itemtype) ? null : $itemtype)));
            } else {
                xarResponseRedirect(xarModURL('comments','admin','stats'));
            }
        }

        if (isset($modid) && strtolower($dtype) == 'object') {
            xarResponseRedirect(xarModURL('comments','admin','module_stats',
                                          array('modid' => $modid,
                                                'itemtype' => empty($itemtype) ? null : $itemtype)));
        } else {
            xarResponseRedirect(xarModURL('comments','admin','stats'));
        }
    }
    // If we're here, then we haven't received authorization
    // to delete any comments yet - so here we ask for confirmation.
    $output['authid'] = xarSecGenAuthKey();
    $output['delete_url'] = xarModURL('comments','admin','delete',$delete_args);

    return $output;
}

?>
