<?php

/**
 * Update configuration
 */
function xlink_admin_updateconfig()
{ 
    // Get parameters
    xarVarFetch('xlink','isset',$xlink,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminXLink')) return; 

    $oldbasenames = array();
    $newbasenames = array();
    if (isset($xlink) && is_array($xlink)) {
        foreach ($xlink as $modname => $value) {
            $oldvalue = xarModGetVar('xlink',$modname);
            if (!empty($oldvalue)) {
                $list = explode(',',$oldvalue);
                foreach ($list as $base) {
                    if (empty($base)) continue;
                    $oldbasenames[$base] = 1;
                }
            }
            if (!empty($value)) {
                $list = explode(',',$value);
                foreach ($list as $base) {
                    if (empty($base)) continue;
                    $newbasenames[$base] = 1;
                }
            }
            if ($modname == 'default') {
                xarModSetVar('xlink', 'default', $value);
            } else {
                xarModSetVar('xlink', $modname, $value);
            } 
        } 
    } 
    if (empty($isalias)) {
        $isalias = array();
    }
    foreach (array_keys($oldbasenames) as $base) {
        if (empty($isalias[$base]) && xarModGetAlias($base) === 'xlink') {
            xarModDelAlias($base,'xlink');
        }
    }
    foreach (array_keys($newbasenames) as $base) {
        if (empty($isalias[$base]) && xarModGetAlias($base) === 'xlink') {
            xarModDelAlias($base,'xlink');
        } elseif (!empty($isalias[$base]) && xarModGetAlias($base) !== 'xlink') {
            xarModSetAlias($base,'xlink');
        }
    }

    // update the list of valid basenames
    $objectid = xarModGetVar('xlink','objectid');
    if (!empty($objectid)) {
        $object =& xarModAPIFunc('dynamicdata','user','getobject',
                                 array('objectid' => $objectid));
        if (isset($object->properties['basename'])) {
            $property = $object->properties['basename'];
            $basenames = array_keys($newbasenames);
            $validation = ';' . join(';',$basenames);
            xarModAPIFunc('dynamicdata','admin','updateprop',
                          array('prop_id' => $property->id,
                                'label' => $property->label,
                                'type' => $property->type,
                                'validation' => $validation));
        }
    }

    xarResponseRedirect(xarModURL('xlink', 'admin', 'modifyconfig'));

    return true;
}

?>
