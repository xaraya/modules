<?php

/**
 * view statistics
 */
function articles_admin_stats($args = array())
{
    if (!xarSecurityCheck('AdminArticles')) return;
    if (!xarVarFetch('group','isset',$group,array(),XARVAR_NOT_REQUIRED)) return;
    extract($args);

    if (empty($group)) {
        $group = array('pubtypeid', 'status', 'authorid');
    }

    $data = array();
    $data['group'] = $group;
    $data['stats'] = xarModAPIFunc('articles','admin','getstats',
                                   array('group' => $group));
    $data['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $data['statuslist'] = xarModAPIFunc('articles','user','getstates');
    $data['fields'] = array('pubtypeid' => xarML('Publication Type'),
                            'status'    => xarML('Status'),
                            'authorid'  => xarML('Author'),
                            'language'  => xarML('Language'));
    return $data;
}

?>
