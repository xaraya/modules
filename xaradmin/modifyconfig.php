<?php
/**
 * modify configuration
 */
function headlines_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminHeadlines')) return;

    $hooks = xarModCallHooks('module', 'modifyconfig', 'headlines', array('module' => 'headlines'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('headlines', 'SupportShortURLs') ?   true : false;
    // Include 'formcheck' JavaScript.
    xarModAPIfunc('base', 'javascript', 'modulefile', array('module'=>'base', 'filename'=>'formcheck.js'));
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();

    $data['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $data['importpubtype'] = xarModGetVar('headlines','importpubtype');
    return $data;
}
?>
