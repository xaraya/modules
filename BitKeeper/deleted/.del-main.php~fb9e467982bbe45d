<?php
/**
 * the main user function (nothing interesting here - might be removed)
 *
 * @return array data to the template
 */
function trackback_user_main()
{
    // Security check
    if (!xarSecurityCheck('ViewTrackBack')) return;


    $data['title'] = "Modules we're currently counting display trackbacks for : (test)";
    $data['moditems'] = array();

    $modList = xarModAPIFunc('trackback','user','getmodules');
    foreach ($modList as $modId => $numItems) {
        $modInfo = xarModGetInfo($modId);
        $modItem = array();
        $modItem['name'] = $modInfo['name'];
        $modItem['numitems'] = $numItems;
        $modItem['link'] = xarModURL($modinfo['name'],'user','main');

        $data['moditems'][] = $modItem;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Track Back Items')));

    // Return output
    return $data;
}
?>