<?php
/**
 * Site Tools DB Optimization
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * @Optimize tables in your database
 * @Only for mySQL datbase at this time
 * @TODO: database abstraction classs
 */
function sitetools_admin_optimize()
{
   if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    /* Security check */
    if (!xarSecurityCheck('AdminSiteTools')) return;
    /* Check for confirmation. */
    if (empty($confirm)) {
        /* No confirmation yet - display a suitable form to obtain confirmation
         * of this action from the user
         */
        $data['optimized']=false;
        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();
        /* Return the template variables defined in this function */
        return $data;
    }
    /* If we get here it means that the user has confirmed the action */
    $data=array();
    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;

    /* Start optimization api */
        $data=array();
        $tabledata=array();
        $total_gain=0;
        $total_kbs=0;
        //optimize and get data for each table's result
        $tabledata= xarModAPIFunc('sitetools','admin','optimizedb');

        if ($tabledata == false) {
            /* Throw back any system exceptions (e.g. database failure) */
            if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
                return; // throw back
            }
            /* Handle the user exceptions yourself */
            $status = xarML('Optimizing database failed');
            $reason = xarCurrentError();
            if (!empty($reason)) {
                $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
            }
            /* Free the exception to tell Xaraya that you handled it */
            xarErrorFree();
            return $status;
        }

       $data['tabledat']=$tabledata['rowdata'];

       $total_gain=$tabledata['total_gain'];
       $total_kbs=$tabledata['total_kbs'];
       $data['totalgain'] = round ($total_gain,3);
       $data['totalkbs']  = round ($total_kbs,3);
       $data['dbname']    = $tabledata['dbname'];
       //Add this new optimization record to the database
       $optid = xarModAPIFunc('sitetools',
                              'admin',
                              'create',
                              array('totalgain' => $data['totalgain']));

      if (!isset($optid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

       /*get total number of times this script has run and total kbs */
      $items = xarModAPIFunc('sitetools', 'admin', 'getall');
       $gaintd=0;
       $runtimes=0;
       foreach ($items as $item) {
            $gaintd += $item['stgain'];
            $runtimes += 1;
       }

       $data['totalruns']=$runtimes;
       $data['gaintd']=$gaintd;
       $data['optimized']=true;

    /* return */
return $data;
}
?>