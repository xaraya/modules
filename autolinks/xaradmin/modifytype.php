<?php

/**
 * $Id$
 * modify an item
 * @param 'lid' the id of the link to be modified
 */
function autolinks_admin_modifytype($args)
{
    extract($args);

    $errorcount = 0;
    $type = array();

    // Get parameters from whatever input we need
    if (!xarVarFetch('tid',  'id', $tid)) {return;}
    if (!xarVarFetch('obid', 'id', $obid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('startnumitem', 'id', $startnumitem, NULL, XARVAR_DONT_SET)) {return;}

    // Indicates if form is submitted.
    if (!xarVarFetch('submit', 'str', $submit, NULL, XARVAR_DONT_SET)) {return;}

    // Security Check
    if(!xarSecurityCheck('EditAutolinks')) {return;}

    if (!empty($obid)) {
        $tid = $obid;
    }

    $currenttype = xarModAPIFunc(
        'autolinks', 'user', 'gettype',
        array('tid' => $tid)
    );
    if (!$currenttype) {return;}

    // TODO: some restructuring on fetched values.
    // Ideal would be to fetch current values, apply changes to
    // the array, then submit the same array for doing the updates.
    if (!empty($submit)) {
        // Values have been submitted by the form.
        $type = array('tid' => $tid);

        if (!xarVarFetch('type_name', 'str:1', $type['type_name'])) {
            $errorcount += 1;
            $type['type_name_error'] = xarExceptionRender('text');
            xarExceptionHandled();
        }

        // TODO: better validation on template name
        if (!xarVarFetch('template_name', 'str:1', $type['template_name'])) {
            $errorcount += 1;
            $type['template_name_error'] = xarExceptionRender('text');
            xarExceptionHandled();
        }

        if (!xarVarFetch('dynamic_replace', 'int:0:1', $type['dynamic_replace'], '0')) {
            $errorcount += 1;
            $type['dynamic_replace_error'] = xarExceptionRender('text');
            xarExceptionHandled();
        }

        if (!xarVarFetch('type_desc', 'str:0:400', $type['type_desc'])) {
            $errorcount += 1;
            $type['type_desc_error'] = xarExceptionRender('text');
            xarExceptionHandled();
        }

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) {return;}

        if ($errorcount == 0) {
            // Call the API function if we have not encountered errors.
            $result = xarModAPIFunc(
                'autolinks', 'admin', 'updatetype', $type
            );

            if ($result) {
                // Go back to the type view, selecting the correct page if necessary.
                if (!empty($startnumitem)) {
                    xarResponseRedirect(xarModURL('autolinks', 'admin', 'viewtype', array('startnumitem' => $startnumitem)));
                } else {
                    xarResponseRedirect(xarModURL('autolinks', 'admin', 'viewtype'));
                }
            } else {
                // Error in API.

                $errorcount += 1;
                $type['global_error'] = xarExceptionRender('text');
                xarExceptionHandled();
            }

            //return true;
        }
    } else {
        // First time - return the current type details.
        $type = $currenttype;
    }

    // Do config hooks for the items.
    $hooks = xarModCallHooks(
        'module', 'modifyconfig', 'autolinks',
        array('itemtype' => $currenttype['itemtype'], 'module' => 'autolinks'));
    $type['itemhooks'] = $hooks;

    // Do modify hooks for the item type itself.
    $hooks = xarModCallHooks(
        'item', 'modify', $tid, 
        array('itemtype' => xarModGetVar('autolinks', 'typeitemtype'), 'module' => 'autolinks')
    );
    $type['typehooks'] = $hooks;

    $type['authid'] = xarSecGenAuthKey();

    return $type;
}

?>
