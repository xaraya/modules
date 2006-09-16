<?php
/**
 * Utility function to count the number of items held by this module
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Utility function to split the rules up
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int pitemid The plan item ID or
 * @param string rules. The rule line
 * @since 24 Feb 2006
 * @return array item with the split up rule parts
 * @throws BAD_PARAM
 */
function itsp_userapi_splitrules($args)
{
    extract ($args);
    if ((!isset($pitemid) || !is_numeric($pitemid)) && (!isset($rules) || !is_string($rules))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'splitrules', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    if (empty($rules) || !is_string($rules)) {
        // get planitem
        $pitem = xarModApiFunc('itsp','user','get_planitem',array('pitemid'=>$pitemid));
        if (!isset($pitem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
        $rules = $pitem['pitemrules'];
    }
    // Splice the rule
    list($Rtype, $Rlevel, $Rcat, $Rsource) = explode(";", $rules);
    $rule_parts = explode(':',$Rtype);
    $rule_type = $rule_parts[1];
    $rule_parts = explode(':',$Rlevel);
    $rule_level = $rule_parts[1];
    $rule_parts = explode(':',$Rcat);
    $rule_cat = $rule_parts[1];
    $rule_parts = explode(':',$Rsource);
    $rule_source = $rule_parts[1];
    // See if we have a mixed source

    $mixnr = stripos($rule_source, 'mix_');
   // echo $mixnr;
    if ($mixnr === false) {
        $mix = false;
        $source = $rule_source;
    } else {
        $mix = true;
        $source = substr($rule_source, 4);
    }
//echo $mix;
    $item['rule_type']   = $rule_type;
    $item['rule_level']  = $rule_level;
    $item['rule_cat']    = $rule_cat;
    $item['rule_source'] = $source;
    $item['mix'] = $mix;
//echo $rule_source;
    return $item;
}
?>