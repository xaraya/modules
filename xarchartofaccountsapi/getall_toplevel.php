<?php

/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage labaccounting module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function labaccounting_chartofaccountsapi_getall_general($args)
{
    extract($args);

    $items = array();
    
    $items[] = array('minacctnum'   => 10000,
                    'maxacctnum'    => 19999,
                    'title'         => "Assets");
                    
    $items[] = array('minacctnum'   => 20000,
                    'maxacctnum'    => 29999,
                    'title'         => "Liabilities");
                    
    $items[] = array('minacctnum'   => 30000,
                    'maxacctnum'    => 39999,
                    'title'         => "Equity");
                    
    $items[] = array('minacctnum'   => 40000,
                    'maxacctnum'    => 49999,
                    'title'         => "Income or Revenue");
                    
    $items[] = array('minacctnum'   => 50000,
                    'maxacctnum'    => 59999,
                    'title'         => "Job Costs/Cost of Goods Sold");
                    
    $items[] = array('minacctnum'   => 60000,
                    'maxacctnum'    => 69999,
                    'title'         => "Overhead Costs or Expenses");
                    
    $items[] = array('minacctnum'   => 70000,
                    'maxacctnum'    => 79999,
                    'title'         => "Other Income");
                    
    $items[] = array('minacctnum'   => 80000,
                    'maxacctnum'    => 89999,
                    'title'         => "Other Expense");

    return $items;
}

?>