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

    // Get parameters from whatever input we need
    if (!xarVarFetch('tid', 'id', $tid)) {
        $errorcount += 1;
        // 'text' rendering will return an array
        $data['tid_error'] = xarErrorRender('text');
        if (isset($data['tid_error']['short'])) {$data['tid_error'] = $data['tid_error']['short'];}
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
        $data['keyword_error'] = xarErrorRender('text');
        if (isset($data['keyword_error']['short'])) {$data['keyword_error'] = $data['keyword_error']['short'];}
        xarErrorHandled();
        if (trim($keyword) == '' ) {
            $keyword = NULL;
        }
    }

    if (!xarVarFetch('title', 'str', $title)) {
        $errorcount += 1;
        $data['title_error'] = xarErrorRender('text');
        if (isset($data['title_error']['short'])) {$data['title_error'] = $data['title_error']['short'];}
        xarErrorHandled();
    }

    if (!xarVarFetch('url', 'str:1', $url)) {
        $errorcount += 1;
        $data['url_error'] = xarErrorRender('text');
        if (isset($data['url_error']['short'])) {$data['url_error'] = $data['url_error']['short'];}
        xarErrorHandled();
    }

    if (!xarVarFetch('comment', 'isset', $comment, NULL, XARVAR_DONT_SET)) {
        $errorcount += 1;
        $data['comment_error'] = xarErrorRender('text');
        // Hack until exceptions are sorted.
        if (isset($data['comment_error']['short'])) {$data['comment_error'] = $data['comment_error']['short'];}
        xarErrorHandled();
    }

    // Default the name to the same as the keyword.
    if (!xarVarFetch('name', 'str:1', $name, $keyword)) {
        $errorcount += 1;
        $data['name_error'] = xarErrorRender('text');
        // Hack until exceptions are sorted.
        if (isset($data['name_error']['short'])) {$data['name_error'] = $data['name_error']['short'];}
        xarErrorHandled();
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) {return;}

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
    }

    // Error in creating the item.
    if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
        $errorcount += 1;
        $data['global_error'] = xarErrorRender('text');
        if (isset($data['global_error']['short'])) {$data['global_error'] = $data['global_error']['short'];}
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
