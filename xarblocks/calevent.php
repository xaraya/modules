<?php
/**
 * Show a block with a listing of events
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * initialise block
 * Metrostat Calendar of Events
 *
 * This module:
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @link http://www.metrostat.net
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xarayahosting.nl>
 *
 * @access public
 * @param none
 * @return nothing
 * @throws no exceptions
 * @todo nothing
 */
function julian_caleventblock_init()
{
    return array('catfilter' => '');
}

/**
 * get information on block
 *
 * @author David St.Clair
 * @access public
 * @param none $
 * @return data array
 * @throws no exceptions
 * @todo nothing
 */
function julian_caleventblock_info()
{
    // Values
    return array('text_type'        => 'Calendar',
        'module'                    => 'julian',
        'text_type_long'            => 'Metrostat Calendar',
        'allow_multiple'            => false,
        'form_content'              => false,
        'form_refresh'              => false,
        'show_preview'              => true);
}

/**
 * display calevent block
 *
 * @author David St.Clair, MichelV
 * @access public
 * @param none $
 * @return data array on success or void on failure
 * @throws no exceptions
 * @todo implement centre menu position
 */
function julian_caleventblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ReadJulianBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Break out options from our content field
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    //set the selected date parts, timestamp, and cal_date in the data array
    $args = xarModAPIFunc('julian','user','getUserDateTimeInfo');
    //load calendar class
    $c = xarModAPIFunc('julian','user','factory','calendar');
    //determine the current user
    $args['name'] = xarUserGetVar('name');
    $args['blockid'] = $blockinfo['bid'];
    //set dates for determining which events to show for the upcoming events
    if (isset($vars['EventBlockDays']) && is_numeric($vars['EventBlockDays'])) {
        $EventBlockDays = $vars['EventBlockDays'];
    } else {
        $EventBlockDays = 7;
    }
    $args['EventBlockDays'] = $EventBlockDays;
    $today=date('Y-m-d');
    $tomorrow=date("Y-m-d",strtotime("tomorrow"));
    $endweek=date("Y-m-d",strtotime("+$EventBlockDays days"));

    if (isset ($vars['CatAware'])) {
        $CatAware = $vars['CatAware'];
    } else {
        $CatAware = 0;
    }
    // Get today's events: start and enddate are the same
    $args['todaysevents']= xarModApiFunc('julian','user','getall',
                                                          array('startdate'=>$today,
                                                                'enddate'=>$today,
                                                                'catid' =>(((!$CatAware==0) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                                                                )
                                        );
    // Get the events for the next $EventBlockDays days
    $args['upcomingevents'] = xarModApiFunc('julian','user','getall',
                                                            array('startdate'=>$tomorrow,
                                                                  'enddate'=>$endweek,
                                                                  'catid' =>(((!$CatAware==0) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                                                                  )
                                           );

    //set the required block data
    if (empty($blockinfo['title'])) {
       $blockinfo['title'] = xarML('Events');
    }
    if (empty($blockinfo['template'])) {
        $template = 'calevent';
    } else {
        $template = $blockinfo['template'];
    }
    $args['Bullet'] = '&'.xarModGetVar('julian', 'BulletForm').';';
    $blockinfo['content'] = xarTplBlock('julian', $template, $args);
    // return the array with info
    return $blockinfo;
}
?>