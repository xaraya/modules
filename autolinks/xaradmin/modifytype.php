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

        if (!xarVarFetch('type_name', 'pre:lower:passthru:str:1', $type['type_name'])) {
            $errorcount += 1;
            $errorstack = xarErrorGet();
            $errorstack = array_shift($errorstack);
            $type['type_name_error'] = $errorstack['short'];
            xarErrorHandled();
        }

        if (!xarVarFetch('template_name', 'pre:ftoken:lower:passthru:str:1', $type['template_name'])) {
            $errorcount += 1;
            $errorstack = xarErrorGet();
            $errorstack = array_shift($errorstack);
            $type['template_name_error'] = $errorstack['short'];
            xarErrorHandled();
        }

        if (!xarVarFetch('dynamic_replace', 'int:0:1', $type['dynamic_replace'], '0')) {
            $errorcount += 1;
            $errorstack = xarErrorGet();
            $errorstack = array_shift($errorstack);
            $type['dynamic_replace_error'] = $errorstack['short'];
            xarErrorHandled();
        }

        if (!xarVarFetch('type_desc', 'str:0:400', $type['type_desc'])) {
            $errorcount += 1;
            $errorstack = xarErrorGet();
            $errorstack = array_shift($errorstack);
            $type['type_desc_error'] = $errorstack['short'];
            xarErrorHandled();
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
                // Return true to redirect.
                $type = true;
            } else {
                // Error in API.

                $errorcount += 1;
                $errorstack = xarErrorGet();
                $errorstack = array_shift($errorstack);
                $type['global_error'] = $errorstack['short'];
                xarErrorHandled();
            }
        }
    } else {
        // First time - return the current type details.
        $type = $currenttype;
    }

    if (empty($errorcount)) {
        // Do config hooks for the autolink type as an item type.
        $type['itemhooks'] = xarModCallHooks(
            'module', 'modifyconfig', 'autolinks',
            array('module' => 'autolinks', 'itemtype' => $type['itemtype'])
        );

        // Do modify hooks for the autolink item type as an item.
        $type['typehooks'] = xarModCallHooks(
            'item', 'modify', $tid, 
            array('itemtype' => xarModGetVar('autolinks', 'typeitemtype'), 'module' => 'autolinks')
        );
    }

    $type['authid'] = xarSecGenAuthKey();

    return $type;
}

?>