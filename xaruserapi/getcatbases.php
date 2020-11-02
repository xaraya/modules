<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * Get the base categories of this module
 *
 * Normally, this should be exactly one category
 *
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 * @return array of base categories
 */
function crispbb_userapi_getcatbases($args)
{
    $basecats = unserialize(xarModVars::get('crispbb', 'base_categories'));
    return $basecats;
}
