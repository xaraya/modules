<?php
/**
 * Dynamic PayPal Now Button
 *
 * @package dynamicdata
 * @subpackage properties
 */

/**
 * include the base class
 *
 */
include_once "modules/base/xarproperties/Dynamic_TextBox_Property.php";

/**
 * handle MSN property
 *
 * @package dynamicdata
 */
class Dynamic_PayPalCart_Property extends Dynamic_TextBox_Property
{
    function validateValue($value = null)
    {
        if (!isset($value)) {
            $value = $this->value;
        }
        if (!empty($value)) {
            if (is_array($value)) {
                if (isset($value['product'])) {
                    $product = $value['product'];
                }
                if (empty($product)) {
                    $product = '';
                }
                if (isset($value['price'])) {
                    $price = $value['price'];
                }
                if (empty($price)) {
                    $price = '';
                }
                $value = array('product' => $product, 'price' => $price);
                $this->value = serialize($value);
            } else {
            // TODO: do we need to check the serialized content here ?
                $this->value = $value;
            }
        } else {
            $this->value = '';
        }
        return true;
    }

    function showInput($args = array())
    {
        extract($args);
        // empty value is allowed here
        if (!isset($value)) {
            $value = $this->value;
        }
        // empty fields are not allowed here
        if (empty($name)) {
            $name = 'dd_' . $this->id;
        }
        if (empty($id)) {
            $id = $name;
        }
        if (empty($size)) {
            $size = $this->size;
        }
        if (empty($maxlength)) {
            $maxlength = $this->maxlength;
        }
        // extract the link and title information
        if (empty($value)) {
        } elseif (is_array($value)) {
            if (isset($value['product'])) {
                $product = $value['product'];
            }
            if (isset($value['price'])) {
                $price = $value['price'];
            }
        } elseif (is_string($value) && substr($value,0,2) == 'a:') {
            $newval = unserialize($value);
            if (isset($newval['product'])) {
                $product = $newval['product'];
            }
            if (isset($newval['price'])) {
                $price = $newval['price'];
            }
        }

        if (empty($product)) {
            $product = '';
        }
        if (empty($price)) {
            $price = '';
        }

        return 'Product Name: <br /><input type="text" name="' . $name . '[product]" value="'. xarVarPrepForDisplay($product) . '" size="'. $size . '" maxlength="'. $maxlength . '"' .
               ' id="'. $id . '"' .
               (!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '') .
               ' /> <br /><br />' .
               'Product Price: <br /><input type="text" name="' . $name . '[price]" value="'. xarVarPrepForDisplay($price) . '" size="'. $size . '" maxlength="'. $maxlength . '" />' .
               (!empty($this->invalid) ? ' <span class="xar-error">'.xarML('Invalid #(1)', $this->invalid) .'</span>' : '');
    }

    function showOutput($args = array())
    {
         extract($args);
        $img = xarTplGetImage('cart.gif', 'paypalsetup');
        $business = xarModGetVar('paypalsetup', 'business');
        $return = xarModGetVar('paypalsetup', 'return');
        $currency_code = xarModGetVar('paypalsetup', 'currency_code');

        if (!isset($value)) {
            $value = $this->value;
        }
        if (empty($value)) {
            return '';
        }
        if (is_array($value)) {
            if (isset($value['product'])) {
                $product = $value['product'];
            }
            if (isset($value['price'])) {
                $price = $value['price'];
            }
        } elseif (is_string($value) && substr($value,0,2) == 'a:') {
            $newval = unserialize($value);
            if (isset($newval['product'])) {
                $product = $newval['product'];
            }
            if (isset($newval['price'])) {
                $price = $newval['price'];
            }
        }
        if (empty($product) && empty($price)) {
            return '';
        } else {
            $out = '<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">';
            $out .= '<input type="hidden" name="cmd" value="_cart">';
            $out .= '<input type="hidden" name="business" value="'. $business .'">';
            $out .= '<input type="hidden" name="currency_code" value="'. $currency_code .'">';
            $out .= '<input type="hidden" name="return" value="'. $return .'">';
            $out .= '<input type="hidden" name="item_name" value="'. $product .'">';
            $out .= '<input type="hidden" name="amount" value="'. $price .'">';
            $out .= '<input type="hidden" name="add" value="1">';
            $out .= '<input type="image" src="'.$img.'" border="0" name="submit" alt="Make payments with PayPal - it\'s fast, free and secure!">';
            $out .= '</form>';
            return $out;
        }
    }


    /**
     * Get the base information for this property.
     *
     * @returns array
     * @return base information for this property
     **/
     function getBasePropertyInfo()
     {
         $args = array();
         $baseInfo = array(
                                  'id'         => 801,
                                  'name'       => 'paypalcart',
                                  'label'      => 'PayPal Cart Button',
                                  'format'     => '801',
                                  'validation' => '',
                            'source'     => '',
                            'dependancies' => '',
                            'requiresmodule' => 'paypalsetup',
                            'aliases'        => '',
                            'args'           => serialize($args)
                            // ...
                           );
        return $baseInfo;
     }

}

?>
