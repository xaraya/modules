<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * display waiting content as a hook
 */
function publications_admin_waitingcontent()
{
    if (!xarSecurity::check('EditPublications')) return;

    // Get publication types
    unset($publinks);
    $publinks = xarMod::apiFunc('publications', 'user', 'getpublinks',
                          array('state' => array(0),
                                'typemod' => 'admin'));

     $data['loop'] = $publinks;
     return $data;
}
?>