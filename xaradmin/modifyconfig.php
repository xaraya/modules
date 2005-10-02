<?php
function dailydelicious_admin_modifyconfig()
{
    if(!xarSecurityCheck('DailyDelicious')) return;
    $hooks = xarModCallHooks('module', 'modifyconfig', 'dailydelicious', array('module' => 'dailydelicious'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    $data['authid'] = xarSecGenAuthKey();
    $data['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $data['importpubtype'] = xarModGetVar('dailydelicious','importpubtype');
    if (empty($data['importpubtype'])) {
        $data['importpubtype'] = xarModGetVar('articles','defaultpubtype');
        if (empty($data['importpubtype'])) {
            $data['importpubtype'] = 1;
        }
    }
    return $data;
}
?>