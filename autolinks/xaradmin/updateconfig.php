<?php

/**
 * update configuration
 */
function autolinks_admin_updateconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminAutolinks')) {return;}

    if (!xarSecConfirmAuthKey()) {return;}

    $old_newwindow = xarModGetVar('autolinks', 'newwindow');
    $old_showerrors = xarModGetVar('autolinks', 'showerrors');

    // The flags that are accepted (values: 0 or 1; 'name'=>default-value)
    $flags = array('newwindow' => 0, 'nbspiswhite' => 0, 'showerrors' => 0);

    // Deal with flags.
    foreach ($flags as $flag => $default)
    {
        xarVarFetch($flag, 'int:0:1', $flagvalue, $default, XARVAR_NOT_REQUIRED+XARVAR_DONT_REUSE);
        xarModSetVar('autolinks', $flag, $flagvalue);
    }

    $newwindow = xarModGetVar('autolinks', 'newwindow');
    $showerrors = xarModGetVar('autolinks', 'showerrors');

    // TODO: do these returns make any sense here? Do we not just fallback to the default value?
    // TODO: represent values and errors to user for correcting.
    if (!xarVarFetch('maxlinkcount', 'int:1:', $maxlinkcount, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemsperpage', 'int:1:', $itemsperpage, 20, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showsamples', 'int:0:3', $showsamples, 0, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('decoration', 'str::30', $decoration, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('punctuation', 'str::30', $punctuation, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('excludeelements', 'str:1:200', $excludeelements, 'a', XARVAR_NOT_REQUIRED)) {return;}
    // TODO: further validation to ensure the template base is a valid partial-filename.
    // TODO: if the template base has been changed, then recompile all the autolinks.
    if (!xarVarFetch('templatebase', 'str:1:30', $templatebase, 'link', XARVAR_NOT_REQUIRED)) {return;}

    $old_decoration = xarModGetVar('autolinks', 'decoration');
    $old_templatebase = xarModGetVar('autolinks', 'templatebase');

    xarModSetVar('autolinks', 'maxlinkcount', $maxlinkcount);
    xarModSetVar('autolinks', 'itemsperpage', $itemsperpage);
    xarModSetVar('autolinks', 'showsamples', $showsamples);
    xarModSetVar('autolinks', 'decoration', $decoration);
    xarModSetVar('autolinks', 'punctuation', $punctuation);
    xarModSetVar('autolinks', 'templatebase', $templatebase);

    // Clean up the excluded elements list.

    // Remove non-alpha chars and normalize letter-case:
    $excludeelements = strtolower(
        trim(preg_replace('/[^\w:]+/', ' ', $excludeelements))
    );

    // Compress runs of white space:
    $excludeelements = preg_replace('/\s+/', ' ', $excludeelements);
    
    xarModSetVar('autolinks', 'excludeelements', $excludeelements);

    // If certain values have changed, then rebuild the replace caches (these values
    // will affect the cached template outputs).
    if ($old_decoration !== $decoration || $old_templatebase !== $templatebase
        || $old_newwindow !== $newwindow || $old_showerrors !== $showerrors) {
        // Get the static autolink types.
        $types = xarModAPIfunc('autolinks', 'user', 'getalltypes', array());

        if (is_array($types)) {
            foreach ($types as $tid => $type) {
                // Rebuild replace cache.
                $result = xarModAPIfunc('autolinks', 'admin', 'updatecache', array('tid' => $tid));
                if (!$result) {return;}
            }
        }
    }

    // Check for errors.
    if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
        return;
    }

    $typeitemtype = xarModGetVar('autolinks', 'typeitemtype');
    if (empty($typeitemtype)) {
        xarModSetVar('autolinks', 'typeitemtype', 1);
    }
    
    // Config update hooks for the autolink type.
    xarModCallHooks(
        'module', 'updateconfig', 'autolinks',
        array('itemtype' => $typeitemtype, 'module' => 'autolinks')
    );
    
    xarResponseRedirect(xarModURL('autolinks', 'admin', 'modifyconfig'));
    return true;
}

?>