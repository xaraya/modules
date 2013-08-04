<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Sorting
 *
 * @author potion <ryan@webcommunicate.net>
 * @return a string like 'itemid ASC';
 */
function comments_adminapi_sort($args)
{
    // Default URL strings to look for
    $url_sortfield = 'sortfield';
    $url_ascdesc = 'ascdesc';

    extract($args);

    if(!xarVarFetch($url_sortfield,     'isset', $sortfield,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch($url_ascdesc, 'isset', $ascdesc, NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!isset($sort)) {
        if (!isset($sortfield)) {
            $sortfield = $sortfield_fallback;
        }

        if (!isset($ascdesc)) {
            $ascdesc = $ascdesc_fallback;
        }

        $sort = $sortfield . ' ' . $ascdesc;
    }

    return $sort;
}

?>