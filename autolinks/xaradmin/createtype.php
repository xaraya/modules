<?php

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('autolinks','admin','newtype') to create a new type
 * TODO: document parameters
 * @param 'keyword' the keyword of the link to be created
 * @param 'title' the title of the link to be created
 * @param 'url' the url of the link to be created
 * @param 'comment' the comment of the link to be created
 */
function autolinks_admin_createtype()
{
    $errorcount = 0;
    $data = array();

    // Security check
    if(!xarSecurityCheck('AddAutolinks')) {return;}

    // Get parameters from whatever input we need
    if (!xarVarFetch('type_name', 'str:1:', $type_name)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['type_name_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    // TODO: better validation on template name
    if (!xarVarFetch('template_name', 'str:1:', $template_name)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['template_name_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    if (!xarVarFetch('dynamic_replace', 'int:0:1', $dynamic_replace, '0')) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['dynamic_replace_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    if (!xarVarFetch('type_desc', 'str:0:400', $type_desc)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['type_desc_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {return;}

    if ($errorcount == 0) {
        // Call the API function if we have not encountered errors.
        $tid = xarModAPIFunc(
            'autolinks', 'admin', 'createtype',
            array(
                'type_name' => $type_name,
                'template_name' => $template_name,
                'dynamic_replace' => $dynamic_replace,
                'type_desc' => $type_desc
            )
        );
    }

    // Error in creating the item.
    if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['global_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    if ($errorcount > 0) {
        $data['type_name'] = $type_name;
        $data['template_name'] = $template_name;
        $data['dynamic_replace'] = $dynamic_replace;
        $data['type_desc'] = $type_desc;

        // Represent the form, with error messages passed in.
        return xarModFunc(
            'autolinks', 'admin', 'newtype', $data
        );
    }

    // Redirect to the modify screen.
    xarResponseRedirect(
        xarModURL(
            'autolinks', 'admin', 'modifytype',
            array('tid' => $tid)
        )
    );

    // Return
    return true;
}

?>