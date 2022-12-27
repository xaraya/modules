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

class BasicPayment implements iPayment
{
    public $code;
    public $title;
    public $description;
    public $status;

    public function __construct()
    {
    }

    public function update_status(array $args=[])
    {
        return false;
    }

    public function javascript_validation()
    {
        return false;
    }

    public function selection()
    {
        return false;
    }

    public function pre_confirmation_check()
    {
        return false;
    }

    public function confirmation()
    {
        return false;
    }

    public function process_button()
    {
        return false;
    }

    public function before_process()
    {
        return false;
    }

    public function after_process()
    {
        return false;
    }

    public function output_error()
    {
        return false;
    }

    public function get_error()
    {
        return false;
    }

    public function check()
    {
        return false;
    }

    public function install()
    {
        return false;
    }

    public function remove()
    {
        return false;
    }

    public function keys()
    {
        return false;
    }
}

interface iPayment
{
    public function __construct();
    public function update_status(array $args=[]);
    public function javascript_validation();
    public function selection();
    public function pre_confirmation_check();
    public function confirmation();
    public function process_button();
    public function before_process();
    public function after_process();
    public function get_error();
    public function check();
    public function install();
    public function remove();
    public function keys();
}
