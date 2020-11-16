<?php

/**
* Get a list of possible page statues.
* This simple list feeds into any screen or function that needs
* to display, process or validate the statuses.
* @todo: would we like to support separate status/system status? Probably too late now.
* @todo: put this in a table so it can be extended and also is.
* accessible to the privilege masks without having to provide
* a complete privilege mask GUI screen.
*/

function xarpages_userapi_getstatuses($args)
{
    return array(
        'ACTIVE' => array(
            'status' => 'ACTIVE', 'name' => xarML('Active'),
            'desc' => xarML('The page is active and displayable (online)')
        ),
        'INACTIVE' => array('status' => 'INACTIVE', 'name' => xarML('Inactive'),
            'desc' => xarML('The page is inactive (offline)')
        ),
        'TEMPLATE' => array('status' => 'TEMPLATE', 'name' => xarML('Template'),
            'desc' => xarML('The page is used as a template for creating new pages')
        ),
        'EMPTY' => array('status' => 'EMPTY', 'name' => xarML('Empty'),
            'desc' => xarML('The page is active but as a placeholder for other pages, and not directly displayable')
        )
    );
}
