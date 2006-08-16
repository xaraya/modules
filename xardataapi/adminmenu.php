<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

// TODO: turn this into an xml file
	function maps_dataapi_adminmenu() {
		return array(
            array('includes' => array('main','overview'), 'target' => 'main', 'label' => xarML('Maps Overview')),
            array('mask' => 'AdminAuthsystem', 'includes' => 'modifyconfig', 'target' => 'modifyconfig', 'title' => xarML('Modify the Maps configuration'), 'label' => xarML('Modify Configuration')),
		);
	}
?>