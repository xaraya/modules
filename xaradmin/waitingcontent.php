<?php
/**
 * Display waiting content for selected sitecontent items
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Display waiting content in sitecontact module
 *
 * @param
 * @author jojodee
 */
/**
 * display waiting content as a hook
 */
function sitecontact_admin_waitingcontent($args)
{
    extract($args);

    unset($responses);

    $responselist = array();
    $responses = array();
    $typelist = array();

    $sctypes = xarModAPIFunc('sitecontact','user','getcontacttypes');

    if (xarModIsHooked('sitecontact','base')) {
        foreach ($sctypes as $scform) {
            if (xarModIsHooked('sitecontact','sitecontact', $scform['scid'])) {
                  $typelist[] =$scform['scid'];
            }
        }
    }

    $responses = xarModAPIFunc('sitecontact', 'user', 'getitemlinks', array('status' => array(0), 'formids'=>$typelist));


    $data['loop'] = $responses;

    return $data;
}
?>