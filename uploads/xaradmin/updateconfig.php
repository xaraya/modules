<?php

function uploads_admin_updateconfig()
{
    // Get parameters
    if (!xarVarFetch('file', 'list:str:1:', $file, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('path', 'list:str:1:', $path, '', XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.  
    if (!xarSecConfirmAuthKey()) return;

    if (isset($file) && is_array($file)) {
        foreach ($file as $varname => $value) {
            // if working on maxsize, remove all commas
            if ($varname == 'maxsize') {
                $value = str_replace(',', '', $value);
            }
            // check to make sure that the value passed in is 
            // a real uploads module variable
            if (NULL !== xarModGetVar('uploads', 'file.'.$varname)) {
                xarModSetVar('uploads', 'file.' . $varname, $value);    
            }
        }
    }
    if (isset($path) && is_array($path)) {
        foreach ($path as $varname => $value) {
            // check to make sure that the value passed in is 
            // a real uploads module variable
            if (NULL !== xarModGetVar('uploads', 'path.' . $varname)) {
                xarModSetVar('uploads', 'path.' . $varname, $value);
            }
        }
    }

	// FIXME: change only if the imports-directory was changed? <rabbitt>
	// Now update the 'current working imports directory' in case the 
    // imports directory was changed. We do this by first deleting the modvar
    // and then recreating it to ensure that the user's version is cleared
    xarModDelVar('uploads', 'path.imports-cwd');
    xarModSetVar('uploads', 'path.imports-cwd', xarModGetVar('uploads', 'path.imports-directory'));

    xarModCallHooks('module',
                    'updateconfig',
                    'uploads',
                    array('module' => 'uploads'));

    xarResponseRedirect(xarModURL('uploads', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>
