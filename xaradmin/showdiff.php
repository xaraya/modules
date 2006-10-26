<?php
/**
 * Change Log Module version information
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
 * show the differences between 2 versions of a module item
 */
function changelog_admin_showdiff($args)
{
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
// Note : this is an array or a string here
    if (!xarVarFetch('logids',    'isset', $logids,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminChangeLog',1,'Item',"$modid:$itemtype:$itemid")) return;

    // get all changes
    $changes = xarModAPIFunc('changelog','admin','getchanges',
                             array('modid' => $modid,
                                   'itemtype' => $itemtype,
                                   'itemid' => $itemid));
    if (empty($changes) || !is_array($changes)) return;

    if (empty($logids)) {
        $logidlist = array();
    } elseif (is_string($logids)) {
        $logidlist = split('-',$logids);
    } else {
        $logidlist = $logids;
    }
    sort($logidlist,SORT_NUMERIC);
    if (count($logidlist) < 2) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'number of versions', 'admin', 'showdiff', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    } elseif (!isset($changes[$logidlist[0]]) || !isset($changes[$logidlist[1]])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'version ids', 'admin', 'showdiff', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $data = array();

    $oldid = $logidlist[0];
    $newid = $logidlist[1];

    $numchanges = count($changes);
    $data['numversions'] = $numchanges;
    $nextid = 0;
    $previd = 0;
    $lastid = 0;
    $version = array();
    foreach (array_keys($changes) as $id) {
        $version[$id] = $numchanges;
        $numchanges--;
        if ($id == $newid) {
            $nextid = $lastid;
        } elseif ($lastid == $oldid) {
            $previd = $id;
        }
        $lastid = $id;
    }

    $data['oldversion'] = $version[$oldid];
    $data['newversion'] = $version[$newid];
    if (!empty($nextid)) {
        $data['nextdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $newid.'-'.$nextid));
    }
    if (!empty($previd)) {
        $data['prevdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $previd.'-'.$oldid));
    }

    $data['changes'] = array();
    $data['changes'][$newid] = $changes[$newid];
    $data['changes'][$oldid] = $changes[$oldid];

    if (xarSecurityCheck('AdminChangeLog',0)) {
        $data['showhost'] = 1;
    } else {
        $data['showhost'] = 0;
    }

    foreach (array_keys($data['changes']) as $logid) {
        $data['changes'][$logid]['profile'] = xarModURL('roles','user','display',
                                                        array('uid' => $data['changes'][$logid]['editor']));
        if (!$data['showhost']) {
            $data['changes'][$logid]['hostname'] = '';
            $data['changes'][$logid]['link'] = '';
        } else {
            $data['changes'][$logid]['link'] = xarModURL('changelog','admin','showversion',
                                                         array('modid' => $modid,
                                                               'itemtype' => $itemtype,
                                                               'itemid' => $itemid,
                                                               'logid' => $logid));
        }
        if (!empty($data['changes'][$logid]['remark'])) {
            $data['changes'][$logid]['remark'] = xarVarPrepForDisplay($data['changes'][$logid]['remark']);
        }
        // 2template $data['changes'][$logid]['date'] = xarLocaleFormatDate($data['changes'][$logid]['date']);
        $data['changes'][$logid]['version'] = $version[$logid];
    }

    $data['link'] = xarModURL('changelog','admin','showlog',
                              array('modid' => $modid,
                                    'itemtype' => $itemtype,
                                    'itemid' => $itemid));

    $modinfo = xarModGetInfo($modid);
    if (empty($modinfo['name'])) {
        return $data;
    }
    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);
    if (isset($itemlinks[$itemid])) {
        $data['itemlink'] = $itemlinks[$itemid]['url'];
        $data['itemtitle'] = $itemlinks[$itemid]['title'];
        $data['itemlabel'] = $itemlinks[$itemid]['label'];
    }

    if (!empty($itemtype)) {
        $getlist = xarModGetVar('changelog',$modinfo['name'].'.'.$itemtype);
    }
    if (!isset($getlist)) {
        $getlist = xarModGetVar('changelog',$modinfo['name']);
    }
    if (!empty($getlist)) {
        $fieldlist = split(',',$getlist);
    }

    $old = xarModAPIFunc('changelog','admin','getversion',
                         array('modid' => $modid,
                               'itemtype' => $itemtype,
                               'itemid' => $itemid,
                               'logid' => $oldid));
    if (empty($old) || !is_array($old)) return;

    if (!empty($old['content'])) {
        $fields = unserialize($old['content']);
        $old['content'] = '';

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
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
            $old['fields'][$field] = $value;
        }
    }
    if (!isset($old['fields'])) {
        $old['fields'] = array();
    }

    $new = xarModAPIFunc('changelog','admin','getversion',
                         array('modid' => $modid,
                               'itemtype' => $itemtype,
                               'itemid' => $itemid,
                               'logid' => $newid));
    if (empty($new) || !is_array($new)) return;

    if (!empty($new['content'])) {
        $fields = unserialize($new['content']);
        $new['content'] = '';

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
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
            $new['fields'][$field] = $value;
        }
    }
    if (!isset($new['fields'])) {
        $new['fields'] = array();
    }

    $fieldlist = array_unique(array_merge(array_keys($old['fields']),array_keys($new['fields'])));
    ksort($fieldlist);

    include 'modules/changelog/xarincludes/difflib.php';

    $data['fields'] = array();
    foreach ($fieldlist as $field) {
        if (!isset($old['fields'][$field])) {
            $old['fields'][$field] = '';
        }
        if (!isset($new['fields'][$field])) {
            $new['fields'][$field] = '';
        }
        $diff = new Diff( split("\n",$old['fields'][$field]), split("\n",$new['fields'][$field]));
        if ($diff->isEmpty()) {
            $data['fields'][$field] = nl2br(xarVarPrepForDisplay($old['fields'][$field]));
        } else {
            $fmt = new UnifiedDiffFormatter;
            $difference = $fmt->format($diff);
            $data['fields'][$field] = nl2br(xarVarPrepForDisplay($difference));
        }
    }

    return $data;
}

?>
