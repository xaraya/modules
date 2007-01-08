<?php
/**
 * Status of all Block
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
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
    if (!xarVarFetch('catid', 'int:1:', $catid, 0, XARVAR_DONT_SET)) return;
    // Get all persons
    $items = xarModApiFunc('sigmapersonnel', 'user', 'getall', array('catid' => $catid));

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
    // Get the standard status indicators
    // TODO: what is the on call/not on call status?
    $statusses = xarModAPIFunc('sigmapersonnel', 'user', 'gets', array('itemtype' => 6));

    // returns array statusid-> statustype

    $data['nrstatus'] = count($statusses);
    //echo $nrstatus;

    foreach ($statusses as $status) {
        // Count the number of persons having this status
        $countstatus = xarModApiFunc('sigmapersonnel', 'user', 'countitems',
                                      array('persstatus' => $status['statusid']));
        if(!empty($countstatus)) {
            $status['count'] = $countstatus;

            $data['amountstatus'][$status['statustype']] = $countstatus;
        } else {
            $status['count'] = 0;
            $data['amountstatus'][$status['statustype']] = 0;
        }
        $data['statusses'][]=$status;
    }

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
            $presencenow = '';
            $presencenow = xarModApiFunc('sigmapersonnel','user','presencenow',array('personid' => $item['personid']));
            $item['presencenow'] = $presencenow;
            // Add this item to the list of items to be displayed
            $data['items'][] = $item;
        }
    }

    $totalnumber = xarModApiFunc('sigmapersonnel', 'user', 'countitems');
    if (!empty($totalnumber)) {
        $data['totalnumber'] = $totalnumber;
    }


    $data['blockid'] = $blockinfo['bid'];


    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
}

?>
