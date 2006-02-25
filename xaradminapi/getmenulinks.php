<?php
/**
 * Get menu links
 *
 */
function tasks_adminapi_getmenulinks()
{

    $menulinks[] = Array('url'   => xarModURL('tasks',
                                              'admin',
                                              'new'),
                         'label' => xarML('Add Task'),
                         'title' => xarML('Add a new task'));
    $menulinks[] = Array('url'   => xarModURL('tasks',
                                              'user',
                                              'view'),
                         'label' => xarML('View tasks'),
                         'title' => xarML('View registered tasks'));
    $menulinks[] = Array('url'   => xarModURL('tasks',
                                              'admin',
                                              'modifyconfig'),
                         'label' => xarML('Modify config'),
                         'title' => xarML('Modify tasks configuration'));

    return $menulinks;
}
?>