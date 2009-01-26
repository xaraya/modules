<?php
/**
 * Google Rel=NoFollow Transform
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 - 2009 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage NoFollow
 * @author John Cox
*/
function nofollow_userapi_transform($args)
{
    extract($args);
    if ((!isset($objectid)) ||
        (!isset($extrainfo))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $args;
    }
    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = nofollow_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] = nofollow_transform($text);
        }
    } else {
        $result = nofollow_transform($text);
    }
    return $result;
}
function nofollow_transform($text)
{
    $text = preg_replace('/<a([^>]*)(?=href="http)([^>]*)>/i', 
                         '<a\\1\\2rel="nofollow">', 
                         $text);
    return $text;
}
?>