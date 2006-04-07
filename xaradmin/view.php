<?php
/**
* Display GUI for config modification
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * View publications
 */
function ebulletin_admin_view()
{
    // security check
    if (!xarSecurityCheck('EditeBulletin')) return;

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('order' => 'id'));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get additional vars
    $yes = xarML('Yes');
    $no = xarML('No');

    // generate list of options for each publication
    foreach ($pubs as $index => $pub) {

        // add some vars
        $pubs[$index]['hr_public'] = $pub['public'] ? $yes : $no;

        if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$pub[name]:$pub[id]")) {

            // edit
            $pubs[$index]['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'modify',
                     array('id' => $pub['id'])
                ),
                'title' => xarML('Modify this publication'),
                'label' => xarML('Edit')
            );
        }

        if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$pub[name]:All:$pub[id]")) {

            // delete
            $pubs[$index]['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'delete',
                    array('id' => $pub['id'])
                ),
                'title' => xarML('Delete this publication'),
                'label' => xarML('Delete')
            );
        }

        if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$pub[name]:All:$pub[id]")) {

            // announce
            $pubs[$index]['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'announce',
                    array('pid' => $pub['id'])
                ),
                'title' => xarML('Send one-time message to all subscribers'),
                'label' => xarML('Announce')
            );
        }


    }

    // initialize template array
    $data = array();

    // add template vars
    $data['pubs'] = $pubs;
    $data['showaddlink'] = xarSecurityCheck('AddeBulletin', 0);

    return $data;

}

?>
