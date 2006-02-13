<?php
/**
 * Modify block settings
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * legis Block - Modify block settings
 *
 * @author jojodee
 */
function legis_latestblock_modify($blockinfo)
{ 
    if (!xarSecurityCheck('AdminLegis')) return;    
    /* Get current content */
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
  //get some default information - if it doesn't exist return now
   $hallsparent=xarModGetVar('legis','mastercids');
   if (!isset($hallsparent)) {
      return;
   }
   $defaulthall=xarModGetVar('legis','defaulthall');
   if (!isset($defaulthall)) {
      return;
   }
    /* Defaults */
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    } 

    /* Send content to template */
    return array(
        'numitems' => $vars['numitems'],
        'blockid' => $blockinfo['bid']
    );
}

/**
 * Update block settings
 */
function legis_latestblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('numitems', 'int:0', $vars['numitems'], 5, XARVAR_DONT_SET)) {return;}
    $blockinfo['content'] = $vars;
    return $blockinfo;
} 
?>
