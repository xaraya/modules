<?php 
/**
 * File: $Id$
 * 
 * Xaraya's CacheSecurity Module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage CacheSecurity Module
 * @author Flavio Botelho <nuncanada@xaraya.com>
*/

function cachesecurity_adminapi_syncall()
{
//    if (!xarModAPIFunc('cachesecurity','admin','syncmasks')) return;
//    if (!xarModAPIFunc('cachesecurity','admin','syncprivileges')) return;
    if (!xarModAPIFunc('cachesecurity','admin','syncprivsgraph')) return;
    if (!xarModAPIFunc('cachesecurity','admin','syncrolesgraph')) return;
    if (!xarModAPIFunc('cachesecurity','admin','syncprivsmasks')) return;

    return true;
}

?>
