<?php
/**
 * Photoshare by Jorn Lind-Nielsen (C) 2002.
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Jorn Lind-Nielsen / Chris van de Steeg
 */
 
 function photoshare_user_main($args)
 {
     xarResponseRedirect(xarModURL('photoshare', 'user', 'viewallfolders'));
     return;
 }
?>