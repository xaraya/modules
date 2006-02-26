<?php
function dailydelicious_adminapi_getmenulinks()
{
    if(xarSecurityCheck('DailyDelicious')) {
        $menulinks[] = Array('url'   => xarModURL('dailydelicious',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the Daily Delicious Configuration'),
                              'label' => xarML('Modify Config'));
        $menulinks[] = Array('url'   => xarModURL('dailydelicious',
                                                  'admin',
                                                  'get'),
                              'title' => xarML('Import Daily Delicious'),
                              'label' => xarML('Import'));
    }
    if (empty($menulinks)){
        $menulinks = array();
    }
    return $menulinks;
}
?>