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

function cachesecurity_adminapi_hook($args)
{
    if (!xarModAPIFunc('cachesecurity','admin','turnoff')) return;

    //For now we just synchronize all
    //For add/delete hooks we can usually be a lot more selective
    //Just adding the new relatioships or deleting the old ones...
    //Whoever wants to finish this will be more than welcome.
    //Nice help for that: 
    //http://fungus.teststation.com/~jon/treehandling/TreeHandling.htm
    if (!xarModAPIFunc('cachesecurity','admin','syncall')) return;

    if (!xarModAPIFunc('cachesecurity','admin','turnon')) return;
  
    //I know this doesnt make any sense, whatsoever but what can i do?
    return $args['extrainfo'];
}

?>
