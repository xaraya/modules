<?php
/**
 * Standard function to view items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * View lists (list types, lists and list items).
 */
function lists_admin_view()
{
    // Get parameters from whatever input we need
    xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_NOT_REQUIRED);

    // Same overview used for all levels.
    xarVarFetch('tid', 'id', $tid, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('lid', 'id', $lid, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('iid', 'id', $iid, NULL, XARVAR_NOT_REQUIRED);

    xarVarFetch('mode', 'enum:modify:update:new:create:delete', $mode, 'view', XARVAR_NOT_REQUIRED);
    xarVarFetch('batch', 'checkbox', $batch, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('confirm', 'int:0:1', $confirm, 0, XARVAR_NOT_REQUIRED);
    xarVarFetch('typehooks', 'enum:item:list', $typehooks, 'item', XARVAR_NOT_REQUIRED);

    /***********************************************
     * Process the display details first           *
     ***********************************************/

    // Display options are:
    // A Overview of list types                 ---
    // B View a list type; overview lists       tid=?
    // C View a list; overview of items         [tid=?] lid=?
    // D View a list item, in context           [tid=?] [lid=?] iid=?

    // Get all list types regardless.
    $types = array();
    $types = xarModAPIfunc('lists', 'user', 'getlisttypes', array('typekey'=>'id'));
    $data = array();
    $data['mode'] = $mode;
    $data['urlnew'] = xarModURL('lists','admin','view',array('mode'=>'new')); // New list type
/*
    if (empty($types)) {
        return $data;
    }
*/
    foreach($types as $key => $looptype) {
        $types[$key]['urledit'] = xarModURL('lists','admin','view',array('tid'=>$looptype['tid'], 'mode'=>'modify'));
        $types[$key]['urlview'] = xarModURL('lists','admin','view',array('tid'=>$looptype['tid']));
        $types[$key]['urlnew'] = xarModURL('lists','admin','view',array('tid'=>$looptype['tid'], 'mode'=>'new')); // New list
    }
    $data['types'] = $types;

    // Get the lid if only an iid has been specified.
    if (empty($lid) && !empty($iid)) {
        $listitem = xarModAPIfunc('lists', 'user', 'getlistitems', array('iid'=>$iid, 'listkey'=>'index'));
        if (isset($listitem[0])) {
            $lid = $listitem[0]['lid'];
            $tid = $listitem[0]['tid'];
        } else {
            // The lid is invalid, so reset it.
            $iid = NULL;
        }
    }

    // Get the tid if only a lid has been specified.
    if (empty($tid) && !empty($lid)) {
        $list = xarModAPIfunc('lists', 'user', 'getlists', array('lid'=>$lid, 'typekey'=>'id'));
        if (isset($list[$lid])) {
            $tid = $list[$lid]['tid'];
        } else {
            // The lid is invalid, so reset it.
            $lid = NULL;
        }
    }
    //echo " tid=$tid lid=$lid iid=$iid ";

    // List type specified.
    if (!empty($tid)) {
        if (isset($types[$tid])) {
            // Mark the tid-index element as 'current'.
            $types[$tid]['current'] = true;
            $data['type'] = $types[$tid];

            // Get the set of lists for this type.
            $lists = xarModAPIfunc('lists', 'user', 'getlists', array('tid'=>$tid, 'listkey'=>'id'));
            foreach($lists as $key => $looplist) {
                $lists[$key]['urledit'] = xarModURL('lists','admin','view',array('lid'=>$looplist['lid'], 'mode'=>'modify'));
                $lists[$key]['urlview'] = xarModURL('lists','admin','view',array('lid'=>$looplist['lid']));
                $lists[$key]['urlnew'] = xarModURL('lists','admin','view',array('tid'=>$tid,'lid'=>$looplist['lid'],'mode'=>'new'));
            }
            $data['lists'] =& $lists;
        } else {
            // The tid is invalid, so reset it.
            $tid = NULL;
        }
    }

    // List specified.
    if (!empty($lid)) {
        if (isset($lists[$lid])) {
            // Mark the current lid-index list as 'current'.
            $lists[$lid]['current'] = true;
            $data['list'] = $lists[$lid];

            // Get the set of items for this list.
            $items = xarModAPIfunc('lists', 'user', 'getlistitems', array('lid'=>$lid, 'items_only'=>true, 'itemkey'=>'id'));
            if (isset($items)) {
                foreach($items as $key => $loopitem) {
                    $items[$key]['urledit'] = xarModURL('lists','admin','view',array('iid'=>$loopitem['iid'], 'mode'=>'modify'));
                    $items[$key]['urlview'] = xarModURL('lists','admin','view',array('iid'=>$loopitem['iid']));
                    $items[$key]['urldelete'] = xarModURL('lists','admin','view',array('iid'=>$loopitem['iid'], 'mode'=>'delete'));
                }
                $data['items'] =& $items;
            }
        } else {
            // The lid is invalid, so reset it.
            $lid = NULL;
        }
    }

    // List item specified.
    if (!empty($iid)) {
        if (isset($items[$iid])) {
            // Mark the current iid-index list as 'current'.
            $items[$iid]['current'] = true;
            $data['item'] = $items[$iid];
        } else {
            // The iid is invalid, so reset it.
            $iid = NULL;
        }
    }

    $data['tid'] = $tid;
    $data['lid'] = $lid;
    $data['iid'] = $iid;


    // Now we can start processing submitted forms *


    if ($mode != 'view') {
        $success = true;
        $redirect = '';

        if ($mode == 'modify') {
            // A modify form will be presented. The main data has already been
            // created for this, above, but the hooks data needs creating.

            if (!empty($tid) && empty($lid)) {
                // Modifying a list type.
                // There are two sets of hooks: for the items and for the lists. Only one
                // can be selected at any time to appear in a single screen.
                $data['hooks_config'] = xarModCallHooks(
                    'module', 'modifyconfig', 'lists',
                    array('module' => 'lists', 'itemtype' => (($typehooks == 'list') ? $data['type']['type_group_id'] : $tid))
                );
                $data['type']['typehooks'] = $typehooks;
            }

            if (!empty($lid) && empty($iid)) {
                // Modifying a list.
                $hooks_modify = xarModCallHooks(
                    'item', 'modify', $lid,
                    array('itemtype' => $data['list']['type_group_id'], 'module' => 'lists')
                );
                if (isset($hooks_modify['dynamicdata'])) {
                    $data['hooks_modify_dd'] = $hooks_modify['dynamicdata'];
                    unset($hooks_modify['dynamicdata']);
                    $data['hooks_modify'] =& $hooks_modify;
                }
            }

            if (!empty($iid)) {
                // Modifying a list item.
                $hooks_modify = xarModCallHooks(
                    'item', 'modify', $iid,
                    array('itemtype' => $tid, 'module' => 'lists')
                );
                if (isset($hooks_modify['dynamicdata'])) {
                    $data['hooks_modify_dd'] = $hooks_modify['dynamicdata'];
                    unset($hooks_modify['dynamicdata']);
                    $data['hooks_modify'] =& $hooks_modify;
                }
            }
        }

        if ($mode == 'update') {
            // We have a submitted edit form.

            if (!empty($tid) && empty($lid)) {
                // An edited list type form was submitted.
                $success = xarVarFetch('type_name', 'str:1', $type_name, 'list_type_' . $tid, XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_name'] = $type_name;

                $success = xarVarFetch('type_desc', 'str:1:1000', $type_desc, '', XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_desc'] = $type_desc;

                $success = xarVarFetch('type_order_columns', 'strlist:,|;:pre:trim:lower:passthru:str:1:30', $type_order_columns, '', XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_order_columns'] = $type_order_columns;

                //$success = xarVarFetch('type_group_id', 'int', $type_group_id, '', XARVAR_NOT_REQUIRED) & $success;
                //$data['type']['type_group_id'] = $type_group_id;

                // If no errors so far, then update the list type.
                if ($success &= xarModAPIfunc('lists', 'admin', 'updatelisttype', $data['type'])) {
                    $redirect = xarModURL('lists', 'admin', 'view', array('tid'=>$tid));
                }

                // TODO: catch errors for passing back to the page.
                // TODO: redirect to relevant view page on success.
            }

            if (!empty($lid) && empty($iid)) {
                // An edited list form was submitted.
                $success = xarVarFetch('list_name', 'str:1', $list_name, 'list_' . $lid, XARVAR_NOT_REQUIRED) & $success;
                $data['list']['list_name'] = $list_name;

                $success = xarVarFetch('list_desc', 'str:1:1000', $list_desc, '', XARVAR_NOT_REQUIRED) & $success;
                $data['list']['list_desc'] = $list_desc;

                $success = xarVarFetch('list_order_columns', 'strlist:,|;:pre:trim:lower:passthru:str:1:30', $list_order_columns, '', XARVAR_NOT_REQUIRED) & $success;
                $data['list']['list_order_columns'] = $list_order_columns;

                // If no errors so far, then update the list type.
                if ($success &= xarModAPIfunc('lists', 'admin', 'updatelist', $data['list'])) {
                    $redirect = xarModURL('lists', 'admin', 'view', array('lid'=>$lid));
                }

                // TODO: catch errors for passing back to the page.
            }

            if (!empty($iid)) {
                // An edited list item form was submitted.
                $data['item']['lid'] = $lid;
                $success = xarVarFetch('item_code', 'str:1', $item_code, 'item_code', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_code'] = $item_code;

                $success = xarVarFetch('item_short_name', 'str:1:100', $item_short_name, $item_code, XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_short_name'] = $item_short_name;

                $success = xarVarFetch('item_long_name', 'str:1:200', $item_long_name, '', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_long_name'] = $item_long_name;

                $success = xarVarFetch('item_desc', 'str:1:1000', $item_desc, '', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_desc'] = $item_desc;

                $success = xarVarFetch('item_order', 'int', $item_order, '', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_order'] = $item_order;

                // If no errors so far, then update the list type.
                if ($success &= xarModAPIfunc('lists', 'admin', 'updatelistitem', array_merge($data['item'], array('gui'=>true)))) {
                    $redirect = xarModURL('lists', 'admin', 'view', array('iid'=>$iid));
                }

                // TODO: catch errors for passing back to the page.
            }
        }

        if ($mode == 'new') {
            // Present a blank form to create a new type/list/item.

            if (empty($tid)) {
                // New list type.
                $data['type'] = array(
                    'type_name' => '',
                    'type_desc' => '',
                    'type_order_columns' => '+item_order,+item_short_name'
                );
            }

            if (!empty($tid) && empty($lid)) {
                // New list.
                $data['list'] = array(
                    'list_name' => '',
                    'list_desc' => '',
                    'list_order_columns' => ''
                );
                // Handle hooks.
                $hooks_new = xarModCallHooks(
                    'item', 'new', '',
                    array('itemtype' => $data['type']['type_group_id'], 'module' => 'lists')
                );
                if (isset($hooks_new['dynamicdata'])) {
                    $data['hooks_new_dd'] = $hooks_new['dynamicdata'];
                    unset($hooks_new['dynamicdata']);
                    $data['hooks_new'] =& $hooks_new;
                }
            }

            if (!empty($lid) && empty($iid)) {
                // New list item.
                $data['item'] = array(
                    'item_code' => '',
                    'item_short_name' => '',
                    'item_long_name' => '',
                    'item_desc' => '',
                    'item_order' => 0 // TODO: make this max order in table plus one.
                );

                // Handle hooks.
                $hooks_new = xarModCallHooks(
                    'item', 'new', '',
                    array('itemtype' => $tid, 'module' => 'lists')
                );
                if (isset($hooks_new['dynamicdata'])) {
                    $data['hooks_new_dd'] = $hooks_new['dynamicdata'];
                    unset($hooks_new['dynamicdata']);
                    $data['hooks_new'] =& $hooks_new;
                }
            }
        }

        if ($mode == 'create') {
            // We have a submitted new item.

            if (!empty($lid) && empty($iid)) {
                // New list item.
                $data['item']['lid'] = $lid;
                $success = xarVarFetch('item_code', 'str:1', $item_code, 'item_code', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_code'] = $item_code;

                $success = xarVarFetch('item_short_name', 'str:1:100', $item_short_name, 'item_short_name', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_short_name'] = $item_short_name;

                $success = xarVarFetch('item_long_name', 'str:1:200', $item_long_name, '', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_long_name'] = $item_long_name;

                $success = xarVarFetch('item_desc', 'str:1:1000', $item_desc, '', XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_desc'] = $item_desc;

                $success = xarVarFetch('item_order', 'int', $item_order, 0, XARVAR_NOT_REQUIRED) & $success;
                $data['item']['item_order'] = $item_order;

                // If no errors so far, then create the list item.
                if ($success && ($iid = xarModAPIfunc('lists', 'admin', 'createlistitem', $data['item']))) {
                    if (!empty($batch)) {
                        // Batch mode - create another.
                        $redirect = xarModURL('lists', 'admin', 'view', array('lid'=>$lid, 'mode'=>'new', 'batch'=>$batch));
                    } else {
                        $redirect = xarModURL('lists', 'admin', 'view', array('iid'=>$iid));
                    }
                }

                // TODO: catch errors for passing back to the page.
                // TODO: redirect to relevant view page on success.
            }

            if (!empty($tid) && empty($lid)) {
                // New list.
                $data['list']['tid'] = $tid;

                $success = xarVarFetch('list_name', 'str:1', $list_name, 'list_name', XARVAR_NOT_REQUIRED) & $success;
                $data['list']['list_name'] = $list_name;

                $success = xarVarFetch('list_desc', 'str:1:1000', $list_desc, '', XARVAR_NOT_REQUIRED) & $success;
                $data['list']['list_desc'] = $list_desc;

                $success = xarVarFetch('list_order_columns', 'strlist:,|;:str:1:30', $list_order_columns, '', XARVAR_NOT_REQUIRED) & $success;
                $data['list']['list_order_columns'] = $list_order_columns;

                // If no errors so far, then create the list type.
                $success && ($lid = xarModAPIfunc('lists', 'admin', 'createlist', $data['list']));
                $redirect = xarModURL('lists', 'admin', 'view', array('lid'=>$lid));

                // TODO: catch errors for passing back to the page.
                // TODO: redirect to relevant view page on success.
            }

            if (empty($tid)) {
                // New list type.
                $success = xarVarFetch('type_name', 'str:1', $type_name, 'list_type_name', XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_name'] = $type_name;

                $success = xarVarFetch('type_desc', 'str:1:1000', $type_desc, '', XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_desc'] = $type_desc;

                $success = xarVarFetch('type_order_columns', 'strlist:,|;:str:1:30', $type_order_columns, '', XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_order_columns'] = $type_order_columns;

                $success = xarVarFetch('type_parenttype', 'int', $type_parenttype, '', XARVAR_NOT_REQUIRED) & $success;
                $data['type']['type_parenttype'] = $type_parenttype;

                // If no errors so far, then create the list type.
                if ($success && ($tid = xarModAPIfunc('lists', 'admin', 'createlisttype', $data['type']))) {
                    $redirect = xarModURL('lists', 'admin', 'view', array('tid'=>$tid));
                }

                // TODO: catch errors for passing back to the page.
                // TODO: redirect to relevant view page on success.
            }
        }

        if ($mode == 'delete') {
            // We are requesting an item be deleted.
            if (!empty($iid)) {
                // Deleting a list item.
                if ($confirm) {
                    // Delete the item.
                    // TODO
                    xarModAPIfunc('lists', 'admin', 'deletelistitem', array('iid'=>$iid));
                    $redirect = xarModURL('lists', 'admin', 'view', array('lid'=>$lid));
                } else {
                    $data['urlconfirm'] = xarModURL('lists', 'admin', 'view', array('iid'=>$iid, 'mode'=>'delete', 'confirm'=>'1'));
                }
            }
        }

        // Redirect required.
        if (!empty($redirect)) {
            xarResponseRedirect($redirect);
            return true;
        }
    }

    $data['mode'] = $mode;

    // Pass the batch indicator into the template.
    $data['batch'] = !empty($batch) ? $batch : NULL;

    return $data;

}

?>