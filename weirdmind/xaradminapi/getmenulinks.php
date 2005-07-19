<?php

function weirdmind_adminapi_getmenulinks()
{
    if(xarSecurityCheck('AdminWeirdMind')) {
        $menulinks[] = Array('url'   => xarModURL('weirdmind',
                                                  'admin',
                                                  'sshreadme'),
                             'title' => xarML('Read the Mindterm README file'),
                             'label' => xarML('ViewREADME'));
        $menulinks[] = Array('url'   => xarModURL('weirdmind',
                                                  'admin',
                                                  'sshfaq'),
                             'title' => xarML('Read the Mindterm Frequently 
			     Asked Questions'),
                             'label' => xarML('View FAQ'));

        $menulinks[] = Array('url'   => xarModURL('weirdmind',
                                                  'admin',
                                                  'weirdmind'),
                             'title' => xarML('Run Weirdmind X-Server'),
                             'label' => xarML('Start Weirdmind'));
    } else {
        $menulinks = '';
    }
    return $menulinks;
}
?>