<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * show a particular version of a module item
 */
function changelog_admin_showversion($args)
{
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('logid',    'isset', $logid,   NULL, XARVAR_NOT_REQUIRED)) {return;}

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
            if (is_array($value) || is_object($value)) {
                $value = serialize($value);
            }
            $data['fields'][$field] = nl2br(xarVarPrepForDisplay($value));
        }
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
                                               'logid' => $nextid));
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
                                               'logid' => $previd));
        $data['prevdiff'] = xarModURL('changelog','admin','showdiff',
                                         array('modid' => $modid,
                                               'itemtype' => $itemtype,
                                               'itemid' => $itemid,
                                               'logids' => $previd.'-'.$logid));
    }
    return $data;
}

?>
