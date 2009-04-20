<?php
//sys::import('xaraya.structures.descriptor');
//sys::import('modules.calendar.class.calnav');

function calendar_calnavblock_init()
{
    return array(
        'nocache' => 1, // don't cache by default
        'pageshared' => 1, // share across pages
        'usershared' => 0, // don't share across users
        'cacheexpire' => null);
        $descriptor = new ObjectDescriptor(array(
                                        'nocache' => 1,
                                        'usershared' => 0,
                                        'text_type' => 'Calnav',
                                        'text_type_long' => 'Calnav selection',
                                        'module' => 'calendar',
                                        ));
        $block = new CalnavBlock($descriptor);
        return $block->getArgs();
}

/**
 * get information on block
 */
function calendar_calnavblock_info()
{
    return array(
        'text_type' => 'Calnav',
        'module' => 'calendar',
        'text_type_long' => 'Calnav selection'
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function calendar_calnavblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadCalendar', 0, 'Block', "All:" . $blockinfo['title'] . ":" . $blockinfo['bid'])) {return;}

    if (!defined('CALENDAR_ROOT')) {
        define('CALENDAR_ROOT', xarModVars::get('calendar','pearcalendar_root'));
    }
    include_once(CALENDAR_ROOT.'Calendar.php');


    $tplData['form_action'] = xarModURL('calendar', 'user', 'changecalnav');
    $tplData['blockid'] = $blockinfo['bid'];

    if (xarServerGetVar('REQUEST_METHOD') == 'GET') {
        // URL of this page
        $tplData['return_url'] = xarServer::getCurrentURL();
    } else {
        // Base URL of the site
        $tplData['return_url'] = xarServer::getBaseURL();
    }

    $blockinfo['content'] = $tplData;

    return $blockinfo;
}

?>
