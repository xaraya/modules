<?php
class LinkCache Extends Object
{
    private static $cache = array();
    private static $hasShortUrls;

    public static function getCached($key, $param, $id, $join = '=')
    {
        // @TODO figure out a way to cache shortURLs
        if (self::_hasShortUrls() && strpos($key, 'user_') === 0) return false;
        // replaces param=id with param=<param>
        if (isset(self::$cache[$key])) {
            return str_replace($param.$join.'<'.$param.'>',$param.$join.$id,self::$cache[$key]);
        }
        return false;
    }
    public static function setCached($key, $param, $id, $link, $join = '=')
    {
        // @TODO figure out a way to cache shortURLs
        if (self::_hasShortUrls() && strpos($key, 'user_') === 0) return false;
        // replaces param=<param> with param=id
        self::$cache[$key] = str_replace($param.$join.$id,$param.$join.'<'.$param.'>', $link);
    }
    public static function getCachedLinks()
    {
        return self::$cache;
    }
    public static function setCachedLinks($cachedlinks)
    {
        if (empty($cachedlinks) || !is_array($cachedlinks)) return;
        self::$cache = $cachedlinks;
    }
    private static function _hasShortUrls()
    {
        if (!isset(self::$hasShortUrls))
            self::$hasShortUrls = (bool)xarModVars::get('crispbb', 'enable_short_urls');
        if (xarMod::$genShortUrls && self::$hasShortUrls) return true;
        return false;
    }
}
?>