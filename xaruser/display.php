<?php
 /**
 * Display an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */

/**
 * display a person from the table.
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['personid'] the item id used for this sigmapersonnel module
 */
function sigmapersonnel_user_display($args)
{
    extract($args);
    if (!xarVarFetch('personid', 'int:1:', $personid)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $personid = $objectid;
    }
    // Initialise the $data variable
    $data = xarModAPIFunc('sigmapersonnel', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // The API function is called.
    $item = xarModAPIFunc('sigmapersonnel',
                          'user',
                          'get',
                          array('personid' => $personid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $item['transform'] = array('name');
    $item = xarModCallHooks('item',
                            'transform',
                            $personid,
                            $item);
    // Fill in the details of the item.

    // Privacy options
    $data['phonehome'] = ($item['privphonehome'] == 1) ?  false : $item['phonehome'];
    // Labels
    $data['useridlabel'] = xarVarPrepForDisplay(xarML('Xaraya User ID '));
    $data['pnumberlabel'] = xarVarPrepForDisplay(xarML('SIGMA number '));
    $data['persstatuslabel'] = xarVarPrepForDisplay(xarML('Status '));
    $data['firstnamelabel'] = xarVarPrepForDisplay(xarML('First name '));
    $data['lastnamelabel'] = xarVarPrepForDisplay(xarML('Last name '));
    $data['tussenvgsllabel'] = xarVarPrepForDisplay(xarML('Tussenvoegsel(s)'));
    $data['initialslabel'] = xarVarPrepForDisplay(xarML('Initials'));
    $data['sexlabel'] = xarVarPrepForDisplay(xarML('Sexe'));
    $data['titlelabel'] = xarVarPrepForDisplay(xarML('Title'));
    $data['streetlabel'] = xarVarPrepForDisplay(xarML('Street'));
    $data['ziplabel'] = xarVarPrepForDisplay(xarML('ZIP Code'));
    $data['cityidlabel'] = xarVarPrepForDisplay(xarML('Town or City'));

    $data['mobilelabel'] = xarVarPrepForDisplay(xarML('Mobile phone number'));
    $data['phoneworklabel'] = xarVarPrepForDisplay(xarML('Work phone number'));
    $data['emaillabel'] = xarVarPrepForDisplay(xarML('Email address'));
    $data['privphonehomelabel'] = xarVarPrepForDisplay(xarML('Home phone number private?'));
    $data['privworklabel'] = xarVarPrepForDisplay(xarML('Work address private?'));
    $data['privemaillabel'] = xarVarPrepForDisplay(xarML('Email address private?'));
    $data['privbirthdatelabel'] = xarVarPrepForDisplay(xarML('Birtdate private?'));
    $data['privaddresslabel'] = xarVarPrepForDisplay(xarML('Address private?'));
    $data['privphoneworklabel'] = xarVarPrepForDisplay(xarML('Work phone number private?'));
    $data['contactnamelabel'] = xarVarPrepForDisplay(xarML('Name of contact person '));
    $data['contactphonelabel'] = xarVarPrepForDisplay(xarML('Phone number of contact person'));
    $data['contactstreetlabel'] = xarVarPrepForDisplay(xarML('Street of contact person'));
    $data['contactcityidlabel'] = xarVarPrepForDisplay(xarML('Town or city of contact person'));
    $data['contactrelationlabel'] = xarVarPrepForDisplay(xarML('Relation to contact person'));
    $data['contactmobilelabel'] = xarVarPrepForDisplay(xarML('Mobile phone number of contact'));
    $data['birthdatelabel'] = xarVarPrepForDisplay(xarML('Birthdate'));
    $data['birthplacelabel'] = xarVarPrepForDisplay(xarML('Place of birth'));
    $data['nrkdistrictlabel'] = xarVarPrepForDisplay(xarML('NRK district'));
    $data['nrknumberlabel'] = xarVarPrepForDisplay(xarML('NRK registration number'));
    $data['ehbonrlabel'] = xarVarPrepForDisplay(xarML('First aid certificate number'));
    $data['ehbopluslabel'] = xarVarPrepForDisplay(xarML('First aid including bandages?'));
    $data['ehbodatelabel'] = xarVarPrepForDisplay(xarML('Date of renewal for first aid'));
    $data['ehboplacelabel'] = xarVarPrepForDisplay(xarML('Organisation to renew first aid'));
    $data['dateintakelabel'] = xarVarPrepForDisplay(xarML('Date of intake talk'));
    $data['intakebylabel'] = xarVarPrepForDisplay(xarML('Intake taken by'));
    $data['dateemploylabel'] = xarVarPrepForDisplay(xarML('Datum in dienst'));
    $data['dateoutlabel'] = xarVarPrepForDisplay(xarML('Datum uit dienst'));
    $data['dateouttalklabel'] = xarVarPrepForDisplay(xarML('Date of talk about departure'));
    $data['outreasonlabel'] = xarVarPrepForDisplay(xarML('Reason for departure'));
    $data['outtalkwithlabel'] = xarVarPrepForDisplay(xarML('Talk taken with'));
    $data['dateshoeslabel'] = xarVarPrepForDisplay(xarML('Date of shoes release'));
    $data['sizeshoeslabel'] = xarVarPrepForDisplay(xarML('Size of shoes'));
    $data['banknrlabel'] = xarVarPrepForDisplay(xarML('Bank account number'));
    $data['bankplaceidlabel'] = xarVarPrepForDisplay(xarML('Bank account place'));
    $data['otherslabel'] = xarVarPrepForDisplay(xarML('Other remarks'));
    $data['educationremarkslabel'] = xarVarPrepForDisplay(xarML('Education remarks'));

    $data['item']     = $item;
    $data['personid'] = $personid;

    xarVarSetCached('Blocks.sigmapersonnel', 'personid', $personid);
    $item['returnurl'] = xarModURL('sigmapersonnel',
                                   'user',
                                   'display',
                                    array('personid' => $personid));
    // Call hooks
    $hooks = xarModCallHooks('item',
                             'display',
                             $personid,
                             $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        $data['hookoutput'] = $hooks;
    }
    if (!empty($item['tussenvgsl'])) {
        xarTplSetPageTitle(xarVarPrepForDisplay($item['firstname'].' '.$item['tussenvgsl'].' '.$item['lastname']));
    } else {
        xarTplSetPageTitle(xarVarPrepForDisplay($item['firstname'].' '.$item['lastname']));
    }
    // Return the template variables defined in this function
    return $data;
}

?>
