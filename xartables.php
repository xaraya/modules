<?php
/**
 * Table definition functions
 * @package modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage xartinymce module
 * @copyright (C) 2009 2skies.com
 * @link http://xarigami.com/projects/xartinymce
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Table definition functions
 *
 * Return tinymce module table names to xaraya
 *
 * @access private
 * @return array
 */
function tinymce_xartables()
{
    /* Initialise table array */
    $xarTables = array();
   
    $tinymceConfigTable = xarDBGetSiteTablePrefix() . '_tinymce';

    /* Set the table name */
    $xarTables['tinymce'] = $tinymceConfigTable;
    /* Return the table information */
    return $xarTables;
}
?>