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

    if (!empty($submit) || !empty($cache)) {
        // Values have been submitted by the form.
        $type = array('tid' => $tid);

        if (!xarVarFetch('type_name', 'str:1:', $type['type_name'])) {
            $errorcount += 1;
            $type['type_name_error'] = xarExceptionRender('text');
            xarExceptionFree();
        }

        // TODO: better validation on template name
        if (!xarVarFetch('template_name', 'str:1:', $type['template_name'])) {
            $errorcount += 1;
            $type['template_name_error'] = xarExceptionRender('text');
            xarExceptionFree();
        }

        if (!xarVarFetch('dynamic_replace', 'int:0:1', $type['dynamic_replace'], '0')) {
            $errorcount += 1;
            $type['dynamic_replace_error'] = xarExceptionRender('text');
            xarExceptionFree();
        }

        if (!xarVarFetch('type_desc', 'str:0:400', $type['type_desc'])) {
            $errorcount += 1;
            $type['type_desc_error'] = xarExceptionRender('text');
            xarExceptionFree();
        }

        // Confirm authorisation code.
        if (!xarSecConfirmAuthKey()) {
            $errorcount += 1;
            $type['global_error'] = xarExceptionRender('text');
            xarExceptionFree();
        }

        if ($errorcount == 0 && empty($cache)) {
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
                xarExceptionFree();
            }

            return true;
        }
    } else {
        // First time - fetch the type.
        $type = xarModAPIFunc(
            'autolinks', 'user', 'gettype',
            array('tid' => $tid)
        );
        if (!$type) {return;}
    }

    $type['authid'] = xarSecGenAuthKey();

    return $type;
}

?>
