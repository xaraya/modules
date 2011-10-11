<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * Sorting
 *
 * @author Ryan Walker
 * @return string $sort (ex. 'subject ASC');
 */
function messages_adminapi_sort($args)
{
    // Default URL strings to look for
    $url_sortfield = 'sortfield';
    $url_ascdesc = 'ascdesc';

    extract($args);

    if(!xarVarFetch($url_sortfield,     'isset', $sortfield,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch($url_ascdesc, 'isset', $ascdesc, NULL, XARVAR_NOT_REQUIRED)) {return;}

    /*if (isset($object) && !isset($sortfield) && !isset($ascdesc)) {
        $config = $object->configuration;
        if (!empty($config['sort'])) {
            $sort = $config['sort'];
        }
    }*/

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