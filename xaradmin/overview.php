<?php
/**
 * Overview for Translations
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Translations
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

?>