<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 */

/**
 * Overview displays standard Overview page
 */
function translations_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('translations', 'admin', 'main', $data, 'main');
}
