<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * get the rating styles
 */
function ratings_userapi_getstyles($args)
{
    $ratingstyles = array(
        array('id' => 'percentage', 'name' => xarML('Percentage')),
        array('id' => 'outoffive', 'name' => xarML('Number out of five')),
        array('id' => 'outoffivestars', 'name' => xarML('Stars out of five')),
        array('id' => 'outoften', 'name' => xarML('Number out of ten')),
        array('id' => 'outoftenstars', 'name' => xarML('Stars out of ten')),
        array('id' => 'customised', 'name' => xarML('Customized: see the user-display template')),
    );
    return $ratingstyles;
}
