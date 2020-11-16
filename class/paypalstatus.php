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
//Psspl:Implemented class for displaying the return status of paypal transaction.
class paypalstatus
{
    public function displayStatus()
    {
        $status = "<table border = '1'>";
        
        foreach ($_POST as $key => $value) {
            $status .=  "<tr><td>".$key."</td><td>".$value."</td></tr>";
        }
        
        $status .= "</table>";
        
        return $status;
    }
}
