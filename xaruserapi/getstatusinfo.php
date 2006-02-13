<?php
/**
 * Get status information
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * Get status information
 *
 * @author jojodee
 */
function legis_userapi_getstatusinfo($args)
{
    extract($args);

        $stateoptions=array();
        //$stateoptions[0] = xarML('Draft'); //not used yet
        $stateoptions[1] = xarML('Pending');
        $stateoptions[2] = xarML('Valid');
        $stateoptions[3] = xarML('Invalid');        
        $data['stateoptions']=$stateoptions;

        $voteoptions=array();
        $voteoptions[0] = xarML('Pending');
        $voteoptions[1] = xarML('Passed');
        $voteoptions[2] = xarML('Not Passed');

        $vetooptions=array();
        $vetooptions[0] = xarML('Pending');
        $vetooptions[1] = xarML('Not Vetoed');
        $vetooptions[2] = xarML('Vetoed');

        $authortypes=array();
        $authortypes[0] = xarML('Sponsor');
        $authortypes[1] = xarML('Author');
        $authortypes[2] = xarML('Cosponsor');
        $authortypes[3] = xarML('Coauthor');    
        
        $data['stateoptions']=$stateoptions;
        $data['voteoptions']=$voteoptions;
        $data['vetooptions']=$vetooptions;
        $data['authortypes']=$authortypes;

        return $data;
}
?>