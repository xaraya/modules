<?php
/**
 * Get the key for Google Maps.
 * The keys are stored in the file modules/xarquery/xardata/googlemapkeys.txt
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Base module
 * @link http://xaraya.com/index.php/release/68.html
 */
/**
 * @author Jason Judge
 * @param keyfile string An alternative path to the googlemapkeys.txt file
 * @return string The google map key for the current page, or NULL if none found.
 *
 * @todo Provide a multiple search path for the default maps key.
 */
function jquery_userapi_get_googlemaps_key($args)
{
    extract($args);
    $key = NULL;

    if (empty($keyfile)) $keyfile = dirname(__FILE__) . '/../xardata/googlemapskeys.txt';

    $ini = @parse_ini_file($keyfile, true);

    // Check the patterns. Stop if one matches.
    if (!empty($ini['keypatterns'])) {
        // Get the current URL (without XML encoding).
        $url = xarServerGetCurrentURL(array(), false);

        foreach($ini['keypatterns'] as $pattern => $key) {
            if (fnmatch($pattern, $url)) {
                $key = $key;
                break;
            }
        }
    }

    return $key;
}
?>
