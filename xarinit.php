<?php
/**
 * Initialization for dojo activator
 *
 * @package modules
 * @subpackage dojo
 * @copyright The Digital Development Foundation, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
 **/

function dojo_init()
{
    return true;
}

function dojo_upgrade($oldVersion)
{
    switch($oldVersion) {
        case '0.3.1': // Current version
        break;
    }
    return true;
}

function dojo_delete()
{
    return true;
}
?>
