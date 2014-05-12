<?php
/**
 * Translations module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
