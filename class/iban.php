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
/**
 * Wrapper class for IBAN manipulation
 *
 */

// Make sure we have the required library
$filepath = sys::lib() . 'php-iban/trunk/php-iban.php';
if (!file_exists($filepath)) {
    throw new Exception(xarML('Could not load the php-iban library'));
}
    
sys::import('php-iban.trunk.php-iban');

class IBAN
{
    public function __construct($iban = '')
    {
        $this->iban = $iban;
    }

    public function check($iban='')
    {
        if ($iban!='') {
            return verify_iban($iban);
        }
        return verify_iban($this->iban);
    }

    public function MistranscriptionSuggestions()
    {
        return iban_mistranscription_suggestions($this->iban);
    }

    public function MachineFormat()
    {
        return iban_to_machine_format($this->iban);
    }

    public function humanFormat()
    {
        return iban_to_human_format($this->iban);
    }

    public function country($iban='')
    {
        return iban_get_country_part($this->iban);
    }

    public function checksum($iban='')
    {
        return iban_get_checksum_part($this->iban);
    }

    public function BBAN()
    {
        return iban_get_bban_part($this->iban);
    }

    public function verifyChecksum()
    {
        return iban_verify_checksum($this->iban);
    }

    public function findChecksum()
    {
        return iban_find_checksum($this->iban);
    }

    public function setChecksum()
    {
        $this->iban = iban_set_checksum($this->iban);
    }

    public function checksumStringReplace()
    {
        return iban_checksum_string_replace($this->iban);
    }

    public function parts()
    {
        return iban_get_parts($this->iban);
    }

    public function bank()
    {
        return iban_get_bank_part($this->iban);
    }

    public function Branch()
    {
        return iban_get_branch_part($this->iban);
    }

    public function Account()
    {
        return iban_get_account_part($this->iban);
    }

    public function Countries()
    {
        return iban_countries();
    }
}

# IBANCountry
class IBANCountry
{

    # constructor with code
    public function __construct($code = '')
    {
        $this->code = $code;
    }

    public function Name()
    {
        return iban_country_get_country_name($this->code);
    }

    public function DomesticExample()
    {
        return iban_country_get_domestic_example($this->code);
    }

    public function BBANExample()
    {
        return iban_country_get_bban_example($this->code);
    }

    public function BBANFormatSWIFT()
    {
        return iban_country_get_bban_format_swift($this->code);
    }

    public function BBANFormatRegex()
    {
        return iban_country_get_bban_format_regex($this->code);
    }

    public function BBANLength()
    {
        return iban_country_get_bban_length($this->code);
    }

    public function IBANExample()
    {
        return iban_country_get_iban_example($this->code);
    }

    public function IBANFormatSWIFT()
    {
        return iban_country_get_iban_format_swift($this->code);
    }

    public function IBANFormatRegex()
    {
        return iban_country_get_iban_format_regex($this->code);
    }

    public function IBANLength()
    {
        return iban_country_get_iban_length($this->code);
    }

    public function BankIDStartOffset()
    {
        return iban_country_get_bankid_start_offset($this->code);
    }

    public function BankIDStopOffset()
    {
        return iban_country_get_bankid_stop_offset($this->code);
    }

    public function BranchIDStartOffset()
    {
        return iban_country_get_branchid_start_offset($this->code);
    }

    public function BranchIDStopOffset()
    {
        return iban_country_get_branchid_stop_offset($this->code);
    }

    public function RegistryEdition()
    {
        return iban_country_get_registry_edition($this->code);
    }

    public function IsSEPA()
    {
        return iban_country_is_sepa($this->code);
    }
}
