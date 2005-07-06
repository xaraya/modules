<?php
/**
   utility function pass individual menu items to the main menu
 
   @return array containing the menulinks for the main menu items.
*/
function security_adminapi_getmenulinks()
{

    $menulinks = array();

    // Security Check
	if (xarSecurityCheck('AdminSecurity',0))
    {

        $menulinks[] = Array('url'   => xarModURL('security',
                                                  'admin',
                                                  'main'),
                              'title' => xarML('Overview'),
                              'label' => xarML('Overview'));
                              
                              
        $menulinks[] = Array('url'   => xarModURL('security',
                                                  'admin',
                                                  'enablemodulesecurity'),
                              'title' => xarML('Enable Module Security'),
                              'label' => xarML('Enable Module Security'));
    }

    return $menulinks;
}
?>
