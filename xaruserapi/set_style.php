<?php

function navigator_userapi_set_style( $args )
{
    extract($args);

    if (!isset($data) || empty($data)) {
        return FALSE;
    }

    if (!isset($name) || empty($name)) {
        return FALSE;
    }

    $cssTemplateFileName = 'modules/navigator/xartemplates/includes/navigator-';
    $cssTemplateFileName .= $name . '-css.xd';

    $styleList = @unserialize(xarModGetVar('navigator', 'style.list.inline'));

    if (!isset($styleList) || !is_array($styleList) || !count($styleList)) {
        $styleList = array();
    }

    if (!file_exists($cssTemplateFileName)) {
        if (isset($styleList[$name]) && !empty($styleList[$name])) {
            unset($styleList[$name]);
            xarModSetVar('navigator', 'style.list.inline', serialize($styleList));
        }
        return FALSE;
    }

    $styleList[$name] = xarTplFile($cssTemplateFileName, $data);
    xarModSetVar('navigator', 'style.list.inline', serialize($styleList));

    return TRUE;

}

?>