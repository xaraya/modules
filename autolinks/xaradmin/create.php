<?php

/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('autolinks','admin','new') to create a new item
 * @param 'keyword' the keyword of the link to be created
 * @param 'title' the title of the link to be created
 * @param 'url' the url of the link to be created
 * @param 'comment' the comment of the link to be created
 */
function autolinks_admin_create()
{
    $errorcount = 0;
    $data = array();

    // Security check
    if(!xarSecurityCheck('AddAutolinks')) {return;}

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {return;}

    // Get parameters from whatever input we need
    if (!xarVarFetch('tid', 'id', $tid)) {
        $errorcount += 1;
        // 'text' rendering will return an array
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['tid_error'] = $errorstack['short'];
        xarErrorHandled();
    } else {
        // Get the autolink type details.
        $type = xarModAPIfunc('autolinks', 'user', 'gettype', array('tid' => $tid));

        if ($type) {
            // A valid autolink type is selected.
            // Pass the details to the template.
            $data['tid'] = $tid;
            $data['type'] = $type;
        } else {
            $errorcount += 1;
            $data['tid_error'] = xarML('Autolink Type does not exist') . ' (' . $tid . ')';
        }
    }

    // TODO: handle these in one go using the new array validation.
    if (!xarVarFetch('keyword', 'str:1', $keyword)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['keyword_error'] = $errorstack['short'];
        xarErrorHandled();
        if (trim($keyword) == '' ) {
            $keyword = NULL;
        }
    }

    if (!xarVarFetch('title', 'str', $title)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['title_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    if (!xarVarFetch('url', 'str:1', $url)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['url_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    if (!xarVarFetch('comment', 'isset', $comment, NULL, XARVAR_DONT_SET)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['comment_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    // Default the name to the same as the (modified) keyword.
    $prekeyword = $keyword;
    xarVarValidate('pre:ftoken:lower', $prekeyword, true);
    if (!xarVarFetch('name', 'pre:ftoken:lower:passthru:str:1', $name, $prekeyword)) {
        $errorcount += 1;
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $data['name_error'] = $errorstack['short'];
        xarErrorHandled();
    }

    if ($errorcount == 0) {
        // The API function is called
        $lid = xarModAPIFunc(
            'autolinks', 'admin', 'create',
            array(
                'keyword' => $keyword,
                'title' => $title,
                'url' => $url,
                'comment' => $comment,
                'name' => $name,
                'enabled' => false,
                'tid' => $tid
            )
        );

        // Fetch it back, to get the item type.
        $link = xarModAPIFunc('autolinks', 'user', 'get', array('lid'=>$lid));
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
        $data['tid'] = $tid;
        $data['keyword'] = $keyword;
        $data['name'] = $name;
        $data['title'] = $title;
        $data['url'] = $url;
        $data['comment'] = $comment;

        // Represent the form, with error messages passed in.
        return xarModFunc(
            'autolinks', 'admin', 'new', $data
        );
    }

    xarResponseRedirect(xarModURL('autolinks', 'admin', 'modify', array('lid' => $lid)));

    // Return
    return true;
}

?>
