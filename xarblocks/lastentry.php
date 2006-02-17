<?php
/**
 * SIGMAPersonnel Block shows latest presence entries
 * at this moment for the current person
 *
 * @package modules
 * @copyright (C) 2005-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */

/**
 * initialise block
 */
function sigmapersonnel_lastentryblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 */
function sigmapersonnel_lastentryblock_info()
{
    // Values
    return array(
        'text_type' => 'lastentry',
        'module' => 'sigmapersonnel',
        'text_type_long' => xarML('Show other sigmapersonnel items when 1 is displayed'),
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function sigmapersonnel_lastentryblock_display($blockinfo)
{
    // See if we are currently displaying a sigmapersonnel item
    // (this variable is set in the user display function)
    if (!xarVarIsCached('Blocks.sigmapersonnel', 'personid')) {
        // if not, we don't show this
        return;
    }

    $current_personid = xarVarGetCached('Blocks.sigmapersonnel', 'personid');
    if (empty($current_personid) || !is_numeric($current_personid)) {
        return;
    }

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
        $vars['numitems'] = 3;
    }
    // TODO: get all presence items for the current person
    // These orders are extra, for later ;)
    $sortby = 'start';
    $sortorder = 'DESC';
    $uid = xarUserGetVar('uid');
    $personid = xarModAPIFunc('sigmapersonnel',
                              'user',
                              'getpersonid',
                              array('uid'=>$uid));

    // Check if person is a SIGMA member
    if (empty($personid)) {
        $blockinfo['content'] = array('items' => '', 'presencenow' => '');
    } else {

        // Create output object
        $items = array();
        $items = xarModAPIFunc('sigmapersonnel',
                               'user',
                               'getallpresence',
                                array('numitems'  => $vars['numitems'],
                                      'personid'  => $personid,
                                      'sortby'    => $sortby,
                                      'sortorder' => $sortorder));

        // TODO: get the current presence for a person
        $presencenow = xarModAPIFunc('sigmapersonnel',
                                     'user',
                                     'presencenow',
                                      array('personid' => $personid));

        // TODO: cleanup
        /*
        // Display each item, permissions permitting
        for (; !$result->EOF; $result->MoveNext()) {
            list($exid, $name) = $result->fields;

            if (xarSecurityCheck('ViewExample', 0, 'Item', "$name:All:$exid")) {
                if (xarSecurityCheck('ReadExample', 0, 'Item', "$name:All:$exid")) {
                    $item = array();
                    $item['link'] = xarModURL(
                        'sigmapersonnel', 'user', 'display',
                        array('exid' => $exid)
                    );

                }
                $item['name'] = $name;
            }
            $items[] = $item;
        }
        */

        $blockinfo['content'] = array('items' => $items, 'presencenow' => $presencenow);
    }
    return $blockinfo;
}

?>