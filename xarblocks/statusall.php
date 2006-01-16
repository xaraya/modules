<?php
/**
 * Status of all Block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */

/**
 * initialise block
 */
function sigmapersonnel_statusallblock_init()
{
    return array(
        'numitems' => 5,
        'nocache' => 0, // cache by default (if block caching is enabled)
        'pageshared' => 1, // share across pages
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
}

/**
 * get information on block
 */
function sigmapersonnel_statusallblock_info()
{
    // Values
    return array(
        'text_type'      => 'statusall',
        'module'         => 'sigmapersonnel',
        'text_type_long' => xarML('Show status of total group)'),
        'allow_multiple' => true,
        'form_content'   => false,
        'form_refresh'   => false,
        'show_preview'   => true
    );
}

/**
 * display block
 * @author MichelV
 * @todo Add more intelligent counter for status and presence
 */
function sigmapersonnel_statusallblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadSIGMAPersonnelBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block.
    // Content is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    // Get all persons
    $items = xarModApiFunc('sigmapersonnel', 'user', 'getall');

    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
        return $blockinfo; // throw back
    }
    /*
    $items = xarModAPIFunc(
        'sigmapersonnel', 'user', 'getallpresence', // Get a function here to get all presences...
        array('numitems' => $vars['numitems'])
    );
*/
    // TODO: define all presencetypes
    // TODO: write the function(s) to calculate the amount of available people.
    // TODO: put that in an API function

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item
    $data['items'] = array();

    $tstatus=0;
    $tstatus1=0;
    $tstatus2=0;
    $tstatus3=0;

    $presence = 0;
    $presence1 = 0;
    $presence2 = 0;

    if (is_array($items)) {
        foreach ($items as $item) {
            // TODO: Add if to see if person is currently an active member

            // Add link to show person
            if (xarSecurityCheck('ReadSIGMAPersonnel', 0, 'PersonnelItem', "All:All:All")) {
                $item['link'] = xarModURL(
                    'sigmapersonnel', 'user', 'display',
                    array('personid' => $item['personid'])
                );
                // Security check 2 - else only display the item name (or whatever is
                // appropriate for your module)
            } else {
                $item['link'] = '';
            }

            // Add one to the appropriate statustype
            if($item['persstatus'] == 1) {
                $tstatus1++;
            } elseif ($item['persstatus'] == 2) {
                $tstatus2++;
            } elseif ($item['persstatus'] == 3) {
                $tstatus3++;
            } else {
                $tstatus++;
            }
            $data['persstatusses'] = xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                              array('itemtype' => 6));

            // See what his presence is
            $personpresence = xarModApiFunc('sigmapersonnel', 'user', 'presencenow', array('personid' => $item['personid']));
            // Add one to the presence type
            // TODO: Make this independent and relate to presencetypes
            if ($personpresence == 1) {
                $presence1++;
            } elseif ($personpresence == 2) {
                $presence2++;
            } else {
                $presence++;
            }

            // Get the presencetypes
            // TODO: what if there are no types defined?
            $data['types'] = xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                              array('itemtype' => 5));

/*
$array = array (1, "hello", 1, "world", "hello");

array_count_values ($array); // returns array (1=>2, "hello"=>2, "world"=>1)
*/

            // Add this item to the list of items to be displayed
            $data['items'][] = $item;
        }
    }
    $data['blockid'] = $blockinfo['bid'];
    $data['presence'] = $presence;
    $data['presence1'] = $presence1;
    $data['presence2'] = $presence2;
    $data['tstatus'] = $tstatus;
    $data['tstatus1'] = $tstatus1;
    $data['tstatus2'] = $tstatus2;
    $data['tstatus3'] = $tstatus3;

    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
}

?>
