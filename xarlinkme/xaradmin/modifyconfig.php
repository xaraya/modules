<?php

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function xarlinkme_admin_modifyconfig()
{
     // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminxarLinkMe')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 
    // Specify some labels and values for display
    $data['updatebutton']      = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['itemsperpagelabel'] = xarVarPrepForDisplay(xarML('Banner Items Per Page?'));
    $data['imagedirlabel']     = xarVarPrepForDisplay(xarML('Directory where banner images are stored (no trailing slash):'));
    $data['pagetitlelabel']    = xarVarPrepForDisplay(xarML('Title for your Link Me Page:'));
    $data['instructionslabel'] = xarVarPrepForDisplay(xarML('Instructions for using the links:'));
    $data['instructions2label']= xarVarPrepForDisplay(xarML('Additional instructions:'));
    $data['txtintrolabel']     = xarVarPrepForDisplay(xarML('Instruction for text ad:'));
    $data['txtadleadlabel']    = xarVarPrepForDisplay(xarML('Lead in text for text ad (sitename is added automatically):'));

    $data['itemsperpage']   = xarModGetVar('xarlinkme','itemsperpage');
    $data['imagedir']       = xarModGetVar('xarlinkme','imagedir');
    $data['pagetitle']      = xarModGetVar('xarlinkme','pagetitle');
    $data['instructions']   = xarModGetVar('xarlinkme','instructions');
    $data['instructions2']  = xarModGetVar('xarlinkme','instructions2');
    $data['txtintro']       = xarModGetVar('xarlinkme','txtintro');
    $data['txtadlead']      = xarModGetVar('xarlinkme','txtadlead');
    $data['sitename']       = xarModGetVar('themes','sitename');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'xarlinkme',
        array('module' => 'xarlinkme'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    } 
    // Return the template variables defined in this function
    return $data;
} 

?>
