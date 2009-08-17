<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array containing the item types and their description
 */
function crispbb_userapi_getitemtypes($args)
{
    extract($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $fields = array('itemtype', 'fid', 'component');
    $select = array();
    foreach ($fields as $required => $field) {
        $select[] = $itemtypestable . '.xar_' . $field;
    }
    $from = $itemtypestable;
    if (empty($fieldlist)) $fieldlist = array('fname');
    $join = '';
    $wheres = array();
    if (!empty($fieldlist)) {
        $forumfields = array('fname', 'fdesc');
        $forumstable = $xartable['crispbb_forums'];
        $dojoin = false;
        foreach ($fieldlist as $fieldname) {
            if (in_array($fieldname, $forumfields)) {
                $select[] = $forumstable.'.xar_'.$fieldname;
                $fields[] = $fieldname;
                $dojoin = true;
            }
        }
        if ($dojoin) {
            $join = ' LEFT JOIN ' . $forumstable;
            $join .= ' ON ' . $forumstable . '.xar_fid' . ' = ' . $itemtypestable . '.xar_fid';
        }
    }
    $query = 'SELECT ' . join(', ', $select);

    $from .= $join;

    $query .= ' FROM ' . $from;

    $bindvars = array();
    if (!empty($itemtype) && is_numeric($itemtype)) {
        $wheres[] = $itemtypestable.".xar_itemtype = ?";
        $bindvars[] = $itemtype;
    }
    if (isset($fid) && is_numeric($fid)) {
        $wheres[] = $itemtypestable.".xar_fid = ?";
        $bindvars[] = $fid;
    }
    $components = array('forum','topics','posts');
    if (!empty($component) && in_array($component, $components)) {
        $wheres[] = $itemtypestable.".xar_component = ?";
        $bindvars[] = $component;
    }

    if (!empty($wheres)) {
        $query .= ' WHERE ' . join(' AND ', $wheres);
    }

    $query .= ' ORDER BY ' . $itemtypestable . '.xar_fid ASC, '.$itemtypestable . '.xar_component ASC';

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;
    $itemtypes = array();
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $item = array();
        foreach ($fields as $key => $field) {
            $value = array_shift($data);
            if ($field == 'cid') {
                $item['catid'] = $value;
            } else {
                $item[$field] = $value;
            }
        }
        if ($item['fid'] == 0) {
            if ($item['component'] == 'forum') {
                $label = xarML('All Forums in crispBB');
                $url = xarModURL('crispbb', 'user', 'main');
            } else {
                $label = xarML('All #(1) in crispBB', ucfirst($item['component']));
                $url = xarModURL('crispbb', 'user', 'search');
            }
        } else {
            if ($item['component'] == 'forum') {
                $label = $item['fname'];
                $url = xarModURL('crispbb', 'user', 'view', array('fid' => $item['fid']));
            } else {
                $label = xarML('All #(1) in #(2)', ucfirst($item['component']), $item['fname']);
                $url = xarModURL('crispbb', 'user', 'view', array('fid' => $item['fid']));
            }
        }
        $item['label'] = xarVarPrepForDisplay($label);
        $item['title'] = xarVarPrepForDisplay(xarML('View #(1)', $item['fname']));
        $item['url']   = $url;
        $itemtypes[$item['itemtype']] = $item;
    }
    $result->Close();
    // extra functionality to keep hooks in synch
    // this function can be called by many modules, but primarily
    // we're interested in modules module hook functions here
    // only do this when no args are passed
    $isupdated = false;
    if (empty($args)) {
        $hooklist = xarModAPIFunc('crispbb', 'user', 'gethooklist');
        $cachedhooks = xarSessionGetVar('crispbb_cachedhooks');
        $cachedhooks = !empty($cachedhooks) ? unserialize($cachedhooks) : array();
        xarSessionDelVar('crispbb_cachedhooks');
        $hookcache = array();
        foreach ($hooklist as $hookmod => $hookdata) {
            // module hooked to all items in crispbb?
            $hookcache[$hookmod][0] = xarModIsHooked($hookmod,'crispbb', 0);
            // if module is hooked to all items, modules module hook functions did the work
            // we also check on cats and hitcount, they should not be hooked to all items
            if ($hookcache[$hookmod][0]) {
                if ($hookmod == 'categories' || $hookmod == 'hitcount') {
                    xarModAPIFunc('modules','admin','disablehooks',
                        array(
                            'callerModName' => 'crispbb',
                            'callerItemType' => 0,
                            'hookModName' => $hookmod
                        ));
                    $isupdated = true;
                } else {
                    continue;
                }
            }
            // array to store All {component} itemtypes
            $types = array();
            // loop through each of our itemtypes
            foreach ($itemtypes as $k => $v) {
                // module hooked to this itemtype?
                $hookcache[$hookmod][$k] = xarModIsHooked($hookmod,'crispbb', $k);
                // handle the components
                foreach ($components as $component) {
                    if ($v['component'] == $component) {
                        // All {component} itemtypes (fid = 0)
                        if ($v['fid'] == 0) {
                            $types[$component] = $k;
                            // if module is categories
                            if ($hookmod == 'categories') {
                                // should be hooked to forum component (all forums)
                                if ($component == 'forum') {
                                    // if not hooked
                                    if (!$hookcache[$hookmod][$k]) {
                                        // hook it now
                                        $hookcache[$hookmod][$k] = xarModAPIFunc('modules','admin','enablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $isupdated = true;
                                    }
                                // should not be hooked to all items other components
                                } else {
                                    if ($hookcache[$hookmod][$k]) {
                                        // if it is, unhook it now
                                        xarModAPIFunc('modules','admin','disablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $hookcache[$hookmod][$k] = false;
                                        $isupdated = true;
                                    }
                                }
                            // else if module is hitcount
                            } elseif ($hookmod == 'hitcount') {
                                // should be hooked to topics component (all topics)
                                if ($component == 'topics') {
                                    // if not hooked
                                    if (!$hookcache[$hookmod][$k]) {
                                        // hook it now
                                        $hookcache[$hookmod][$k] = xarModAPIFunc('modules','admin','enablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $isupdated = true;
                                    }
                                // should not be hooked to all items other components
                                } else {
                                    if ($hookcache[$hookmod][$k]) {
                                        // if it is, unhook it now
                                        xarModAPIFunc('modules','admin','disablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $hookcache[$hookmod][$k] = false;
                                        $isupdated = true;
                                    }
                                }
                            // else if module is crispsubs
                            } elseif ($hookmod == 'crispsubs') {
                                // can only be hooked to topics component
                                if ($component != 'topics') {
                                    if ($hookcache[$hookmod][$k]) {
                                        // if it is, unhook it now
                                        xarModAPIFunc('modules','admin','disablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $hookcache[$hookmod][$k] = false;
                                        $isupdated = true;
                                    }
                                }
                            }
                        // any other fid is a regular forum itemtype
                        } else {
                            // check if this module is hooked to all items of this component
                            if ($hookcache[$hookmod][$types[$component]]) {
                                // crispsubs can only be hooked to topics
                                if ($hookmod == 'crispsubs' && $component != 'topics') {
                                    if ($hookcache[$hookmod][$k]) {
                                        // if it is, unhook it now
                                        xarModAPIFunc('modules','admin','disablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $hookcache[$hookmod][$k] = false;
                                        $isupdated = true;
                                    }
                                } else {
                                    // if it is, check the module is hooked to this itemtype
                                    if (!$hookcache[$hookmod][$k]) {
                                        // if not, hook it now
                                        $hookcache[$hookmod][$k] = xarModAPIFunc('modules','admin','enablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $isupdated = true;
                                    }
                                }
                            // if it isn't hooked to all items, we need to know if it changed
                            } elseif (isset($cachedhooks[$hookmod][$types[$component]]) && $cachedhooks[$hookmod][$types[$component]]) {
                                // it was previously hooked to all items
                                if ($hookcache[$hookmod][$k] || ($hookmod == 'crispsubs' && $component != 'topics')) {
                                    // unhook it if it's currently hooked
                                    xarModAPIFunc('modules','admin','disablehooks',
                                        array(
                                            'callerModName' => 'crispbb',
                                            'callerItemType' => $k,
                                            'hookModName' => $hookmod
                                        ));
                                    $hookcache[$hookmod][$k] = false;
                                    $isupdated = true;
                                }
                            } else {
                                // don't hook crispsubs to anything but topics
                                if ($hookmod == 'crispsubs' && $component != 'topics') {
                                    if ($hookcache[$hookmod][$k]) {
                                        // if it is, unhook it now
                                        xarModAPIFunc('modules','admin','disablehooks',
                                            array(
                                                'callerModName' => 'crispbb',
                                                'callerItemType' => $k,
                                                'hookModName' => $hookmod
                                            ));
                                        $hookcache[$hookmod][$k] = false;
                                        $isupdated = true;
                                    }
                                }
                            }
                        }
                    } // end current component
                } // end components loop
            } // end itemtypes loop
        } // end hooklist loop
        // cache the hooks
        xarSessionSetVar('crispbb_cachedhooks', serialize($hookcache));
        // we need to check if the hooks are currently being updated by the modules module
        // if any changes were made, the admin hooks display will be out of synch
        // first we check if the current request module, type and func is modules admin hooks
        list ($modname, $modtype, $modfunc) = xarRequestGetInfo();
        if ($modname == 'modules' && $modtype == 'admin' && $modfunc == 'hooks') {
            // we tag a flag onto the redirected url, so we can keep track on redirects
            if (!xarVarFetch('hookupdate', 'isset', $hookupdate, 0, XARVAR_NOT_REQUIRED)) return;
            // 2 is the maximum redirects it should take to get back in synch
            // we also only redirect if something actually changed
            if ($hookupdate < 2 && $isupdated) {
                // modules admin hooks function expects a hook param
                // indicating the hook module to list, so we fetch that for the return url
                if (!xarVarFetch('hook', 'isset', $hookarg, NULL, XARVAR_NOT_REQUIRED)) return;
                // and finally we redirect to the function
                xarResponseRedirect(xarModURL('modules', 'admin', 'hooks', array('hook' => $hookarg, 'hookupdate' => $hookupdate++)));
                return;
            }
        }
    }
    return $itemtypes;
}
?>