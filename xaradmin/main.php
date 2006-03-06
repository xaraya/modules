<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
/**
 * Main administration function
 * @return array
 */
function googlesearch_admin_main()
{
    // Security Check
  if(!xarSecurityCheck('Admingooglesearch')) return;
  return array();
  //xarResponseRedirect(xarModURL('googlesearch', 'admin', 'modifyconfig'));
}
?>