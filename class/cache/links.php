<?php
class LinkCache Extends xarServer
{
    private static $hasShortUrls;
    private static $cachedLinks = array();
    private static $cachedParams = array();

    public static function getCachedURL($modName = 'crispbb', $modType = 'user', $funcName = 'forum_index', $args = array(), $generateXMLURL = NULL, $fragment = NULL, $entrypoint = array())
    {
        // build the key
        $key = $modName.$modType.$funcName;
        // add any new params found for this function
        if (count($args) > 0) {
            foreach ($args as $k => $v) {
                if (is_array($v)) {
                    // @TODO if (is_array($v))
                    /*
                    foreach ($v as $l => $w) {
                        self::$cachedParams[$k] = $k . '[<arraykey>]=<arrayval>';
                        break;
                    }
                    */
                } else {
                    // cache any new params
                    if (!isset(self::$cachedParams[$k]))
                        self::$cachedParams[$k] = $k . '=<'. $k . '>';
                }
            }
        }
        // check for cached link
        if (!isset(self::$cachedLinks[$key])) {
            // No cached link found
            // get link via xarServer::getModuleURL()
            $link = self::getModuleURL($modName, $modType, $funcName, $args, $generateXMLURL, $fragment, $entrypoint);
            // bail if shorturls are enabled and this is a user function (for now)
            if (self::_hasShortUrls() && $modType == 'user') return $link;
            // bail on functions that have arrays in arguments (for now)
            if ($funcName == 'search' || $funcName == 'moderate') return $link;
            // loop through cached params
            if (count(self::$cachedParams) > 0) {
                foreach (self::$cachedParams as $k => $v) {
                    if (isset($args[$k])) {
                        if (is_array($args[$k])) {
                            /* @TODO
                            foreach ($args[$k] as $l => $w) {

                            }
                            */
                        } else {
                            // replace values with param targets for caching
                            $link = str_replace($k .'=' . $args[$k], $v, $link);
                        }
                    }
                }
            }
            // cache the link
            self::$cachedLinks[$key] = $link;
        } else {
            // Found a cached link
            $link = self::$cachedLinks[$key];
        }
        if (!isset($generateXMLURL)) $generateXMLURL = self::$generateXMLURLs;
        // replace &amp; in xmlurls so we can target joins
        if ($generateXMLURL) $link = str_replace('&amp;', '&', $link);
        if (count(self::$cachedParams) > 0) {
            foreach (self::$cachedParams as $k => $v) {
                $pos = strpos($link, $v);
                if (!isset($args[$k])) {
                    if ($pos !== false) {
                        $join = $link[$pos-1];
                        if ($join == '?') {
                            $link = substr_replace($link, '', $pos, strlen($v));
                            if (isset($link[$pos]) && $link[$pos] == '&') {
                                $link = substr_replace($link, '', $pos, 1);
                            }
                        } else {
                            $link = substr_replace($link, '', $pos-1, strlen($v)+1);
                        }
                    }
                } else {
                    if (is_array($args[$k])) {
                        //@ TODO
                    } else {
                        if ($pos !== false) {
                            $link = str_replace($v, $k .'=' . $args[$k], $link);
                        } else {
                            if (strpos($link, $k .'=' . $args[$k]) === false) {
                                $join = (strpos($link, '?') === false) ? '?' : '&';
                                $link .= $join . $k .'=' . $args[$k];
                            }
                        }
                    }
                }
            }
        }
        // put the &amp;s back :)
        if ($generateXMLURL) $link = str_replace('&', '&amp;', $link);
        // handle fragments
        if (!empty($fragment)) {
            // look for existing fragment
            $pos = strrpos($link, '#');
            if ($pos !== false) {
                // and replace it
                $link = substr_replace($link, $fragment, $pos+1);
            } else {
                // or just append it to the link
                $link .= '#' . $fragment;
            }
        } else {
            // no fragment, remove from link if exists
            $pos = strrpos($link, '#');
            if ($pos !== false) {
                $link = substr_replace($link, '', $pos);
            }
        }
        // return the link
        return $link;
    }

    private static function _hasShortUrls()
    {
        if (!isset(self::$hasShortUrls))
            self::$hasShortUrls = (bool)xarModVars::get('crispbb', 'enable_short_urls');
        if (parent::$allowShortURLs && self::$hasShortUrls) return true;
        return false;
    }
}
?>