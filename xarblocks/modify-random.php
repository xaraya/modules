<?php
/**
 * File: $Id: modify-random.php,v 1.1.1.1 2005/11/28 18:55:21 curtis Exp $
 * 
 * Random passage block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */

/**
 * modify block settings
 */
function bible_randomblock_modify($blockinfo)
{ 
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['rotation'])) {
        $vars['rotation'] = 'pageload';
    }
    if (empty($vars['lastchange'])) {
        $vars['lastchange'] = strtotime('1 years ago');
    }
    if (empty($vars['queries']) ||
        (!is_array($vars['queries']) && !is_string($vars['queries']))) {
        $vars['queries'] = '|Proverbs';
    }
    if (empty($vars['lastquery'])) {
        $vars['lastquery'] = '|Proverbs 1:1';
    }

    $data = $vars;
    $data['blockid'] = $blockinfo['bid'];
    $data['bid'] = $blockinfo['bid'];

    $rotations = array();
    $rotations['pageload'] = array(xarML('Every Page Load'));
    $rotations['hourly']   = array(xarML('Hourly'));
    $rotations['daily']    = array(xarML('Daily'));
    $rotations['weekly']   = array(xarML('Weekly'));
    $rotations['biweekly'] = array(xarML('Biweekly'));
    $rotations['monthly']  = array(xarML('Monthly'));
    $rotations[$vars['rotation']][] = 1;
    $data['rotations'] = $rotations;

    // Send content to template
    return $data;
} 

/**
 * update block settings
 */
function bible_randomblock_update($blockinfo)
{
    if (!xarVarFetch('rotation', 'enum:monthly:biweekly:weekly:daily:hourly:pageload', $vars['rotation'], 'pageload', XARVAR_DONT_SET)) return;
    if (!xarVarFetch('queries', 'str:1', $vars['queries'], '|Proverbs', XARVAR_DONT_SET)) return;

	// reset cached values to avoid weird results
	$vars['lastchange'] = '';
	$vars['lastquery'] = '';

    $blockinfo['content'] = $vars;
    return $blockinfo;
} 

?>