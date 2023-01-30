<?php
/**
 * Classes to utility methods for the cache system of Xaraya
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager;

use BadParameterException;

class CacheUtility
{
    /**
     * Update the configuration parameters of the module based on data from the modification form
     *
     * @author Jon Haworth
     * @author jsb <jsb@xaraya.com>
     * @access public
     * @param int|string $args['starttime'] (seconds or hh:mm:ss)
     * @param string $args['direction'] (from or to)
     * @return string|int $convertedtime (hh:mm:ss or seconds)
     * @throws BadParameterException wrong direction
     * @todo maybe add support for days?
     */
    public static function convertseconds(int|string $args): string|int
    {
        extract($args);

        // if the value is set to zero, we can leave it that way
        if ($starttime === 0) {
            return $starttime;
        }

        switch ($direction) {
            case 'from':
                return static::convertFromSeconds((int) $starttime);
            case 'to':
                return static::convertToSeconds((string) $starttime);
            default:
                throw new BadParameterException($direction, "Unknown direction #(1)");
                return 0;
        }
    }

    public static function convertFromSeconds(int $starttime = 0): string
    {
        // if the value is set to zero, we can leave it that way
        if ($starttime === 0) {
            return (string) $starttime;
        }
        $convertedtime = '';
        // convert to hours
        $hours = intval(intval($starttime) / 3600);
        // add leading 0
        $convertedtime .= str_pad($hours, 2, '0', STR_PAD_LEFT). ':';
        // get the minutes
        $minutes = intval(intval($starttime / 60) % 60);
        // then add to $hms (with a leading 0 if needed)
        $convertedtime .= str_pad($minutes, 2, '0', STR_PAD_LEFT). ':';
        // get the seconds
        $seconds = intval($starttime % 60);
        // add to $hms, again with a leading 0 if needed
        $convertedtime .= str_pad($seconds, 2, '0', STR_PAD_LEFT);

        return $convertedtime;
    }

    public static function convertToSeconds(string $starttime = ''): int
    {
        // break apart the time elements
        $elements = explode(':', $starttime);
        // make sure it's all there
        $allelements = array_pad($elements, -3, 0);
        // calculate the total seconds
        $convertedtime = (($allelements[0] * 3600) + ($allelements[1] * 60) + $allelements[2]);
        // make sure we're sending back an integer
        settype($convertedtime, 'integer');

        return $convertedtime;
    }
}
