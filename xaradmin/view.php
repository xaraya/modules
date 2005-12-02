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
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall');
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // generate list of options for each publication
    foreach ($pubs as $index => $pub) {

        $pubs[$index]['editurl'] = '';
        $pubs[$index]['deleteurl'] = '';
        $pubs[$index]['issueurl'] = '';

        // Modify
        if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$pub[name]:$pub[id]")) {
            $pubs[$index]['editurl'] = xarModURL('ebulletin', 'admin', 'modify',
                                                 array('id' => $pub['id']));
        }

        // New Issue
        if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$pub[name]:All:$pub[id]")) {
            $pubs[$index]['issueurl'] = xarModURL('ebulletin', 'admin', 'newissue',
                                                  array('pid' => $pub['id']));
        }

        // Delete
        if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$pub[name]:All:$pub[id]")) {
            $pubs[$index]['deleteurl'] = xarModURL('ebulletin', 'admin', 'delete',
                                                   array('id' => $pub['id']));
        }

    }

    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // add template vars
    $data['pubs'] = $pubs;
    $data['showaddlink'] = xarSecurityCheck('AddeBulletin', 0);

    return $data;

}

?>
