<?php

/**
 * File: $Id$
 *
 * Runner for the unit testing framework
 *
 * It is assumed that this file runs with the 
 * current directory set to the directory which
 * contains the 'xartests' subdirectory
 *
 * @package quality
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage testing
 * @author Marcel van der Boom <marcel@xaraya.com>
 * @author Jan Schrage <jan@xaraya.com>
 * @author Frank Besler <besfred@xaraya.com>
*/
    sys::import('modules.dynamicdata.class.properties.master');

    function xarayatesting_user_othertests()
    {
        $picker = DataPropertyMaster::getProperty(array('name' => 'filepicker'));
        $picker->initialization_basedirectory = sys::code() . 'modules/xarayatesting/xarscans';
        $picker->validation_file_extensions = 'php';
        $data['tests'] = $picker->getOptions();
        return $data;
    }

?>