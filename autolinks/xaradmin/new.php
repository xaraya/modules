<?php

/**
 * add new item
 */
function autolinks_admin_new($args)
{
    extract($args);

    if (!xarVarFetch('tid', 'id', $tid, NULL,  XARVAR_NOT_REQUIRED)) {return;}

    // Prepare each parameter for re-presentation in the form.
    foreach(array('tid', 'tid_error',
        'name', 'name_error',
        'keyword', 'keyword_error',
        'url', 'url_error',
        'title', 'title_error',
        'comment', 'comment_error',
        'global_error') as $param) {
        if (isset($$param)) {
            $data[$param] = xarVarPrepForDisplay($$param);
        } else {
            $data[$param] = '';
        }
    }

    // Security Check
    if (!xarSecurityCheck('AddAutolinks')) {return;}

    $types = xarModAPIfunc('autolinks', 'user', 'getalltypes');

    if (!$types) {
        // There are no autolink types.
        $data['global_error'] = xarML('Autolink Types must be created before Autolinks');
    } else {
        // Sort by type name, preserving the key association.
        $sortfunc = create_function(
            '$a,$b',
            'if ($a["type_name"] == $b["type_name"]) return 0;'
            .'return ($a["type_name"] < $b["type_name"]) ? -1 : 1;'
        );
        uasort($types, $sortfunc);
        $data['types'] = $types;
    }

    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}

?>