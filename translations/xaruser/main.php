<?php
/**
 * Translations User GUI functions
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage translations
 * @author Volodymyr Metenchuk
 */
function translations_user_main($args)
{
    // Security Check
    if(!xarSecurityCheck('ReadTranslations')) return;

    $data = array();
    return $data;
}
?>
