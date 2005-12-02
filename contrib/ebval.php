<?php
/**
* Redirect for validating unregistered users
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/

//initialize the Xaraya core
include 'includes/xarCore.php';
xarCoreInit(XARCORE_SYSTEM_ALL);

// get HTTP vars
if (!xarVarFetch('c', 'str:1', $c)) return;
if (!xarVarFetch('phase', 'str:1', $phase)) return;

// call validation function
xarResponseRedirect(xarModURL('ebulletin', 'user', 'validatesubscriber',
    array('code' => $c, 'phase' => $phase))
);

// success
exit;
?>