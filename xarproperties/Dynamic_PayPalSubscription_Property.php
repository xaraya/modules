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
include_once "includes/properties/Dynamic_TextBox_Property.php";

/**
 * handle MSN property
 *
 * @package dynamicdata
 */
class Dynamic_PayPalSubscription_Property extends Dynamic_TextBox_Property
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
                if (isset($value['time'])) {
                    $time = $value['time'];
                }
                if (empty($time)) {
                    $time = '';
                }
                if (isset($value['number'])) {
                    $number = $value['number'];
                }
                if (empty($number)) {
                    $number = '';
                }
                $value = array('product' => $product, 'price' => $price, 'time' => $time, 'number' => $number);
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
            if (isset($value['time'])) {
                $time = $value['time'];
            }
            if (isset($value['number'])) {
                $number = $value['number'];
            }
        } elseif (is_string($value) && substr($value,0,2) == 'a:') {
            $newval = unserialize($value);
            if (isset($newval['product'])) {
                $product = $newval['product'];
            }
            if (isset($newval['price'])) {
                $price = $newval['price'];
            }
            if (isset($newval['time'])) {
                $time = $newval['time'];
            }
            if (isset($newval['number'])) {
                $number = $newval['number'];
            }
        }

        if (empty($product)) {
            $product = '';
        }
        if (empty($price)) {
            $price = '';
        }
        if (empty($time)) {
            $time = '';
        }
        if (empty($number)) {
            $number = '';
        }

        return 'Product Name: <br /><input type="text" name="' . $name . '[product]" value="'. xarVarPrepForDisplay($product) . '" size="'. $size . '" maxlength="'. $maxlength . '"' .
               ' id="'. $id . '"' .
               (!empty($tabindex) ? ' tabindex="'.$tabindex.'"' : '') .
               ' /> <br /><br />' .
               'Product Price: <br /><input type="text" name="' . $name . '[price]" value="'. xarVarPrepForDisplay($price) . '" size="'. $size . '" maxlength="'. $maxlength . '" /> <br /><br />' .
               'Number of time periods between each recurrence: <br /><input type="text" name="' . $name . '[number]" value="'. xarVarPrepForDisplay($number) . '" size="'. $size . '" maxlength="'. $maxlength . '" /> <br /><br />' .
               'Time Period: <br />Day <input type="radio" name="' . $name . '[time]" value="D"> Week <input type="radio" name="' . $name . '[time]" value="W"> Month <input type="radio" name="' . $name . '[time]" value="M"> Year <input type="radio" name="' . $name . '[time]" value="Y">' .
               (!empty($this->invalid) ? ' <span class="xar-error">'.xarML('Invalid #(1)', $this->invalid) .'</span>' : '');
    }

    function showOutput($args = array())
    {
         extract($args);
        $img = xarTplGetImage('subscribe.gif', 'paypalsetup');
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
            if (isset($value['time'])) {
                $time = $value['time'];
            }
            if (isset($value['number'])) {
                $number = $value['number'];
            }
        } elseif (is_string($value) && substr($value,0,2) == 'a:') {
            $newval = unserialize($value);
            if (isset($newval['product'])) {
                $product = $newval['product'];
            }
            if (isset($newval['price'])) {
                $price = $newval['price'];
            }
            if (isset($newval['time'])) {
                $time = $newval['time'];
            }
            if (isset($newval['number'])) {
                $number = $newval['number'];
            }
        }
        if (empty($product) && empty($price)) {
            return '';
        } else {
            $out = '<form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">';
            $out .= '<input type="hidden" name="cmd" value="_xclick-subscriptions">';
            $out .= '<input type="hidden" name="business" value="'. $business .'">';
            $out .= '<input type="hidden" name="currency_code" value="'. $currency_code .'">';
            $out .= '<input type="hidden" name="return" value="'. $return .'">';
            $out .= '<input type="hidden" name="item_name" value="'. $product .'">';
            $out .= '<input type="hidden" name="a3" value="'. $price .'">';
            $out .= '<input type="hidden" name="no_shipping" value="1">';
            $out .= '<input type="hidden" name="p3" value="'. $number .'">';
            $out .= '<input type="hidden" name="t3" value="'. $time .'">';
            $out .= '<input type="hidden" name="src" value="1">';
            $out .= '<input type="hidden" name="sra" value="1">';
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
                            'id'         => 803,
                            'name'       => 'paypalsubscription',
                            'label'      => 'PayPal Subscription Button',
                            'format'     => '803',
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