/**
 *
 * Initialisation of metaweblog module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage metaweblogapi
 * @author Marcel van der Boom <marcel@xarara.com>
 */

/**
* initialise the metaweblogapi module
 *
 * The intialisation of metaweblogapi is very simple as
 * it uses no database tables yet.
 *
 */
function metaweblogapi_init()
{
    return metaweblogapi_upgrade('0.1.0'); // initial version was 0.1.0
}

/**
* upgrade the metaweblogapi module from an old version
 * This function can be called multiple times
 */
function metaweblogapi_upgrade($oldversion)
{
    return true;
}

/**
* delete the metaweblogapi module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function metaweblogapi_delete()
{
    return true;
}

?>