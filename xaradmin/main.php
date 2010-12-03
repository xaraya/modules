<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage filters
 * @link http://www.xaraya.com/index.php/release/1039.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function filters_admin_main()
{
    
    return xarTplModule('filters','admin','overview');

}

?>