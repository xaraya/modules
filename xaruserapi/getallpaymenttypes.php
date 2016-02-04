<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
    sys::import('xaraya.structures.query');

    function payments_userapi_getallpaymenttypes($args)
    {
        extract($args);

        $xartable =& xarDB::getTables();
        $q = new Query('SELECT',$xartable['payments_paymentmethods']);
        $q->eq('category',$category);
        $q->setorder('name');
        if (!$q->run()) return;
    //  $q->qecho();
        return $q->output();
    }
?>