<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * display waiting content as a hook
 */
function publications_admin_waitingcontent()
{

    // Get publication types
    unset($publinks);
    $publinks = xarModAPIFunc('publications', 'user', 'getpublinks',
                          array('state' => array(0),
                                'typemod' => 'admin'));

     $data['loop'] = $publinks;
     return $data;
}
?>