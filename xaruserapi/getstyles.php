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
    $ratingstyles = [
        ['id' => 'percentage', 'name' => xarML('Percentage')],
        ['id' => 'outoffive', 'name' => xarML('Number out of five')],
        ['id' => 'outoffivestars', 'name' => xarML('Stars out of five')],
        ['id' => 'outoften', 'name' => xarML('Number out of ten')],
        ['id' => 'outoftenstars', 'name' => xarML('Stars out of ten')],
        ['id' => 'customised', 'name' => xarML('Customized: see the user-display template')],
    ];
    return $ratingstyles;
}
