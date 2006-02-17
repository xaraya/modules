<?php
/**
 * Retrieve ItemTypes
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */

/*
 * Utility function to retrieve the list of item types of this module.
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return array
 * @return array containing the item types and their description
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @ TODO Michelv: this function returns error for Dynamic Data when no types are defined.
 */

function surveys_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get list types.
    $listtypes = array();

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $internal_types = array(
        'S' => xarML('Surveys'),
        'G' => xarML('Question Groups'),
        'T' => xarML('Statuses')
    );

    foreach($internal_types as $type_code => $type_name) {
        // Get item type details.
        $itemtype = xarModAPIfunc(
            'surveys', 'user', 'gettype',
            array('type' => $type_code)
        );

        if (isset($itemtype)) {
            $listtypes[] = array(
                'type_name'=>'* ' . $type_name,
                'tid' => $itemtype['tid']
            );
        }
    }

    // Include the user-defined question and response types.
    $user_types = array(
        'Q' => xarML('Question: '),
        'R' => xarML('Response: ')
    );
    foreach($user_types as $type_code => $type_name) {
        $itemtypes = xarModAPIfunc(
            'surveys', 'user', 'gettypes',
            array('type' => $type_code)
        );

        if (!empty($itemtypes)) {
            foreach($itemtypes as $itemtype) {
                $listtypes[] = array(
                    'type_name' => $type_name . $itemtype['name'],
                    'tid' => $itemtype['tid']
                );
            }
        }
    }


    foreach ($listtypes as $listtype) {
        $tid = $listtype['tid'];
        $itemtypes[$tid] = array(
            'label' => xarVarPrepForDisplay($listtype['type_name']),
            'title' => xarVarPrepForDisplay(xarML('View #(1)', $listtype['type_name'])),
            'url'   => xarModURL('lists', 'admin', 'view', array('tid' => $tid))
        );
    }

    return $itemtypes;
}

?>