<?php
/**
 * Change Log Module version information/restore
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * show a particular version of a module item (or restore it if possible)
 */
function changelog_admin_showversion($args)
{
    extract($args);

// TODO: add more restore options
    // List of currently supported restore modules (see API calls below)
    $supported = array('articles', 'dynamicdata', 'xarpages');

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('logid',    'isset', $logid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('restore',  'isset', $restore, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('confirm',  'isset', $confirm, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminChangeLog',1,'Item',"$modid:$itemtype:$itemid")) return;

    $data = xarModAPIFunc('changelog','admin','getversion',
                          array('modid' => $modid,
                                'itemtype' => $itemtype,
                                'itemid' => $itemid,
                                'logid' => $logid));
    if (empty($data) || !is_array($data)) return;

    if (xarSecurityCheck('AdminChangeLog',0)) {
        $data['showhost'] = 1;
    } else {
        $data['showhost'] = 0;
    }

    $data['profile'] = xarModURL('roles','user','display',
                                 array('uid' => $data['editor']));
    if (!$data['showhost']) {
        $data['hostname'] = '';
    }
    if (!empty($data['remark'])) {
        $data['remark'] = xarVarPrepForDisplay($data['remark']);
    }
    // 2template $data['date'] = xarLocaleFormatDate($data['date']);

    $data['link'] = xarModURL('changelog','admin','showlog',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    $data['fields'] = array();

    $modinfo = xarModGetInfo($modid);
    if (empty($modinfo['name'])) {
        return $data;
    }
    $data['modid'] = $modid;
    $data['itemtype'] = $itemtype;
    $data['itemid'] = $itemid;
    $data['modname'] = $modinfo['name'];

    if (empty($restore)) {
        $restore = null;
    } else {
        $restore = 1;
    }

    // Check for supported restore modules
    if (!empty($restore) && !in_array($modinfo['name'],$supported)) {
        $msg = xarML('Restoring items from module #(1) is currently not supported',
                     $modinfo['name']);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                    new SystemException($msg));
        return false;
    }

    // Check for confirmation
    if (!empty($confirm) && !xarSecConfirmAuthKey()) return;

    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);
    if (isset($itemlinks[$itemid])) {
        $data['itemlink'] = $itemlinks[$itemid]['url'];
        $data['itemtitle'] = $itemlinks[$itemid]['title'];
        $data['itemlabel'] = $itemlinks[$itemid]['label'];
    }

    if (!empty($data['content'])) {
        $fields = unserialize($data['content']);
        $data['content'] = '';

        if (!empty($itemtype)) {
            $getlist = xarModGetVar('changelog',$modinfo['name'].'.'.$itemtype);
        }
        if (!isset($getlist)) {
            $getlist = xarModGetVar('changelog',$modinfo['name']);
        }
        if (!empty($getlist)) {
            $fieldlist = split(',',$getlist);
        }
        ksort($fields);
        foreach ($fields as $field => $value) {
            // skip some common uninteresting fields
            if ($field == 'module' || $field == 'itemtype' || $field == 'itemid' ||
                $field == 'mask' || $field == 'pass' || $field == 'changelog_remark') {
                continue;
            }
            // skip fields we don't want here
            if (!empty($fieldlist) && !in_array($field,$fieldlist)) {
                continue;
            }
            // Note: we'll do the formatting in the template now
            $data['fields'][$field] = $value;
        }
    }

    // Restore this version of the module item
    if (!empty($confirm)) {
        if (empty($data['fields'])) {
            $msg = xarML('Nothing to restore');
            xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                            new SystemException($msg));
            return false;
        }
        switch ($modinfo['name']) {
            case 'articles':
                // Check mandatory fields (if necessary)
                if (empty($data['fields']['aid'])) {
                    $data['fields']['aid'] = $itemid;
                }
/*
                // Prepare optional fields (if necessary)
                if (!isset($data['fields']['module'])) {
                    $data['fields']['module'] = $modinfo['name'];
                }
                if (!isset($data['fields']['itemtype'])) {
                    $data['fields']['itemtype'] = $itemtype;
                }
*/
                // Call the update API function
                $result = xarModAPIFunc('articles','admin','update',
                                        $data['fields']);
                if (empty($result)) return;
                break;

            case 'dynamicdata':
                // Call the update API function
                $result = xarModAPIFunc('dynamicdata','admin','update',
                                        array('modid' => $modid,
                                              'itemtype' => $itemtype,
                                              'itemid' => $itemid,
                                              'values' => $data['fields']));
                if (empty($result)) return;
                break;

            case 'xarpages':
                // Check mandatory fields (if necessary)
                if (empty($data['fields']['pid'])) {
                    $data['fields']['pid'] = $itemid;
                }
                // Call the update API function
                $result = xarModAPIFunc('xarpages','admin','updatepage',
                                        $data['fields']);
                if (empty($result)) return;
                break;

        // TODO: add more restore options
            default:
                $msg = xarML('Restoring items from module #(1) is currently not supported',
                             $modinfo['name']);
                xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                            new SystemException($msg));
                return false;
        }
        xarResponseRedirect(xarModURL('changelog', 'admin', 'showlog',
                                      array('modid' => $modid,
                                            'itemtype' => $itemtype,
                                            'itemid' => $itemid)));
        return true;
    }

    // get all changes
    $changes = xarModAPIFunc('changelog','admin','getchanges',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid));
    $numchanges = count($changes);
    $data['numversions'] = $numchanges;
    $nextid = 0;
    $previd = 0;
    $lastid = 0;
    $version = array();
    foreach (array_keys($changes) as $id) {
        $version[$id] = $numchanges;
        $numchanges--;
        if ($id == $logid) {
            $nextid = $lastid;
        } elseif ($lastid == $logid) {
            $previd = $id;
        }
        $lastid = $id;
    }

    $data['version'] = $version[$logid];
    if (!empty($nextid)) {
        $data['nextversion'] = xarModURL('changelog','admin','showversion',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logid' => $nextid,
                                               'restore' => $restore));
        $data['nextdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $logid.'-'.$nextid));
    }
    if (!empty($previd)) {
        $data['prevversion'] = xarModURL('changelog','admin','showversion',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logid' => $previd,
                                               'restore' => $restore));
        $data['prevdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $previd.'-'.$logid));
    }

    if (!empty($restore)) {
        $data['showlink'] = xarModURL('changelog','admin','showversion',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logid' => $logid));
        $data['confirmbutton'] = xarML('Confirm');
        // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        $data['restore'] = 1;

    } elseif (in_array($modinfo['name'],$supported)) {
        $data['restorelink'] = xarModURL('changelog','admin','showversion',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logid' => $logid,
                                               'restore' => 1));
    }

    return $data;
}

?>
