<?php
function sitecloud_adminapi_getmenulinks()
{
    // Security Check
	if(xarSecurityCheck('AddSitecloud')) {
        $menulinks[] = Array('url'   => xarModURL('sitecloud',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new Headline into the system'),
                              'label' => xarML('Add'));
    }
    if(xarSecurityCheck('EditSitecloud')) {
        $menulinks[] = Array('url'   => xarModURL('sitecloud',
                                                  'admin',
                                                  'view'),
                              'title' => xarML('View and Edit Sitecloud'),
                              'label' => xarML('View'));
    }
    if(xarSecurityCheck('AdminSitecloud')) {
        $menulinks[] = Array('url'   => xarModURL('sitecloud',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Edit the Sitecloud Configuration'),
                              'label' => xarML('Modify Config'));
    }
    if(xarSecurityCheck('AdminSitecloud')) {
        $menulinks[] = Array('url'   => xarModURL('sitecloud',
                                                  'admin',
                                                  'compare'),
                              'title' => xarML('Check the sites manually for updates'),
                              'label' => xarML('Update Times'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>
