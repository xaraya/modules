<?php
/**
 * Display waiting content for selected sitecontent items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact Module
 * @link http://xaraya.com/index.php/release/890.html
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
    if (!isset($scid)) $scid = 2;//specific case

    // Get
    unset($responses);
    $responses = xarModAPIFunc('sitecontact', 'user', 'getitemlinks', array('status' => array(0), 'scid'=>2));

     $data['loop'] = $responses;

     return $data;
}
?>