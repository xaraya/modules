<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

function gallery_userapi_get_states($args)
{
    extract($args);

    $states = array(
        'SUBMITTED' => xarML('Submitted'), // Default
        'REJECTED'  => xarML('Rejected'),
        'APPROVED'  => xarML('Approved'),
        'UNKNOWN'   => xarML('Unknown')
    );

    return $states;
}
?>