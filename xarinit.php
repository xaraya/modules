<?php
/**
 * Initialisation of moveabletype module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @link http://xaraya.com/index.php/release/329.html
 * @author Marcel van der Boom <marcel@xarara.com>
 */
/**
* initialise the moveabletype module
 *
 * The intialisation of moveabletype is very simple as
 * it uses no database tables yet.
 *
 */
function moveabletype_init()
{
    return moveabletype_upgrade('0.1.0'); // initial version was 0.1.0
}

/**
 * upgrade the moveabletype module from an old version
 * This function can be called multiple times
 * @return bool true
 */
function moveabletype_upgrade($oldversion)
{
    return true;
}

/**
 * delete the moveabletype module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function moveabletype_delete()
{
    return true;
}

?>