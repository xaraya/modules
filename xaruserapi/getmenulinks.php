<?php
function translations_userapi_getmenulinks ($args) 
{
    if (xarSecurityCheck('ReadTranslations', 0) == true) {
        $menulinks[] = array(
            'url'   => xarModURL('translations', 'user', 'show_status', array('action' => 'post')),
            'title' => xarML('Show the progress status of the locale currently being translated'),
            'label' => xarML('Progress report'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}

?>