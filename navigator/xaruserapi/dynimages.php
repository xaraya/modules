<?php
function chsfnav_userapi_dynimages($args) {
    extract ($args);
    
    if (!isset($side) || empty($side) || ($side != 'left' && $side != $side)) {
        $msg = xarML('You must provide a side (left/right) for dynimages to display on!');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new DefaultUserException($msg));
        return;
    }
    
    if (!isset($style)) {
        $style = 'tag';
    } 
    
    switch(strtolower($style)) {
        case 'url':
            $format = '%s';
            break;
        default:
        case 'tag':
            $format = '<img src="%s" id="'.$side.'_img" alt="'.$side.'_img" />';
            break;
    }

    
    $cids = xarVarGetCached('Blocks.articles', 'cids');
    
    if (empty($cids) || (count($cids) == 0 || count($cids) > 2)) {
        $pid = 0;
        $typeId = 0;
    } else {
        $pids = unserialize(xarModGetVar('chsfnav', 'category.default-parents'));
        if (count($cids) == 1) {
            $cids[1] = 0;
        }
        
        if (in_array($cids[0], array_keys($pids))) {
            $pid = $cids[0];
            $typeId = $cids[1];
        } elseif (in_array($cids[1], array_keys($pids))) {
            $pid = $cids[1];
            $typeId = $cids[0];
        } else {
            $pid = 0;
            $typeId = 0;
        }
    } 
    
    $images = xarModGetVar('chsfnav', "category.image-list.$pid");
    if (isset($images) && !empty($images)) {
        $images = unserialize($images);
    } else {
        $images = array();
    }
    
    $siteDefaults = xarmodGetVar('chsfnav', 'category.image-list.0');
    if (isset($siteDefaults) && !empty($siteDefaults)) {
        $siteDefaults = unserialize($siteDefaults);
    } else {
        $siteDefaults = array();
    }

    if (isset($images[$typeId][$side]) && !empty($images[$typeId][$side])) {
        $fileId = $images[$typeId][$side]; 
    } elseif (isset($images[0][$side]) && !empty($images[0][$side])) {
        $fileId = $images[0][$side];
    } elseif (isset($siteDefaults[0][$side]) && !empty($siteDefaults[0][$side])) {
        $fileId = $siteDefaults[0][$side]; 
    } else {
        $fileId = 0;
    }
    if ($fileId) {
        $url =  xarModURL('images', 'user', 'display', array('fileId' => $fileId));
    } else {
        $url = 'themes/CHSF/images/missing.png';
    }
    
    return sprintf($format, $url);
}                     
?>