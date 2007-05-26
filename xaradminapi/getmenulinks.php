<?php

/**
    utility function pass individual menu items to the main menu
 
    @returns array
    @return array containing the menulinks for the main menu items.
*/
function sitesearch_adminapi_getmenulinks()
{
    // Security Check
    if (xarSecurityCheck('AdminSiteSearch')) 
    {
        $menulinks[] = Array('url'   => xarModURL('sitesearch','admin','main'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));

        $menulinks[] = Array('url'   => xarModURL('sitesearch','admin','databases'),
                              'title' => xarML('Databases'),
                              'label' => xarML('Databases'));
                              
        $menulinks[] = Array('url'   => xarModURL('sitesearch','admin','qtrack'),
                              'title' => xarML('Query Tracking'),
                              'label' => xarML('Query Tracking'));
        
        $menulinks[] = Array('url'   => xarModURL('sitesearch','admin','indexer'),
                              'title' => xarML('Index Database'),
                              'label' => xarML('Index Database'));
                              
        $menulinks[] = Array('url'   => xarModURL('sitesearch','admin','modifyconfig'),
                              'title' => xarML('Modify the SiteSearch module configuration'),
                              'label' => xarML('Modify Config'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>
