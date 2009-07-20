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
 *//**
 * Standard function to selectively call transform hooks on individual fields
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param array $args
 * @param int $args['itemtype'] crispbb itemtype to call transform hooks for (required)
 * @param array $args['transforms'] array of fields and hooks to transform (required)
 * @param string $args[$fields] one for each field to transform, matching fields in transforms array
 * @param array $args['ignore'] array of hooks to ignore (optional)
 * @return array
 */
function crispbb_userapi_dotransforms($args)
{
    extract($args);
    $invalid = array();
    if (empty($itemtype) || !is_numeric($itemtype)) $invalid[] = 'itemtype';
    if (!isset($transforms) || empty($transforms) || !is_array($transforms)) $invalid[] = 'transforms';

    if (!empty($invalid)) return array();

    if (!isset($ignore) || empty($ignore) || !is_array($ignore)) $ignore = array();

    $transhooks = xarModGetHookList('crispbb', 'item', 'transform', $itemtype);

    $transformed = array();

    foreach ($transforms as $field => $hooks) {
        if (isset($args[$field])) {
            $text = $args[$field];
            $text = empty($ignore['html']) ? xarVarPrepHTMLDisplay($text) : xarVarPrepForDisplay($text);
            if (!empty($transhooks)) {
                foreach ($transhooks as $transform) {
                    // skip ignored hook module
                    if (!empty($ignore[$transform['module']])) continue;
                    // skip transforms for this field, this hook module
                    if (empty($transforms[$field][$transform['module']])) continue;
                    // do transform
                    if (!xarModAPILoad($transform['module'], $transform['type']))  return; //return;
                    $extrainfo = array(
                        'module' => 'crispbb',
                        'itemtype' => $itemtype,
                        'itemid' => 0,
                        'transform' => array($field),
                        $field => $text
                    );
                    $res = xarModAPIFunc($transform['module'],
                                         $transform['type'],
                                         $transform['func'],
                                         array('objectid' => 0,
                                               'extrainfo' => $extrainfo));
                    if (!isset($res))  return; //return;
                    $text = $res[$field];
                }
            }
            $transformed[$field] = $text;
        }
    }

    $cleanup = array('ttitle', 'tdesc', 'pdesc', 'fname');
    foreach ($transformed as $field => $text) {
        if (!in_array($field, $cleanup)) continue;
        $text = str_replace("<p>", "", $text);
        $text = str_replace("</p>", "", $text);
        $transformed[$field] = $text;
    }

    return $transformed;

}
?>