<?php
/**
 * Search Handler for Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * This is the item:search:api hook function for the newsletter
 * module. Each kind of this function is supposed to do its
 * search thing specific for it's containing module and return
 * an array of search-results with keys: result, link, itemtype and optionally
 * description. The search module then renders that array in it's search
 * results template.
 *
 * Each search api function gets two parameters from the search module: terms
 * and itemtypes. The latter is an array which contains the itemtypes the
 * user wants to search in. The module itself should know what to do with these
 *
 * So:
 * $object_id -> when not 0 a specific repository was specified
 * $terms -> string with entered search terms
 * $itemtypes = array('itemtypename' => itemtypeid, ... ,)
 * <code>
 * $results = array(
 *                  array('result' => string describing the result,
 *                        'link'   => link to the result,
 *                        'itemtype' => in which itemtype was this found (text, not id)
 *                        'description' => longer result text
 *                        'description' => longer result text
 *                       )
 *                  ...
 *                 )
 * </code>
*/


?>
