<?php

/**
 * Example custom functions template for pageform
 *
 * Assumes pageaction page named "formaction_template"
 * and you have specified "validate" for validation_func and "process" for processing_func names
 *
 * TODO: all these validation functions could be rolled up into a single class,
 * and that would solve many of the name-space issues (i.e. it would be very easy to
 * create new action files from a simpler template)
 *
 * Notes for using this template:
 * - Name the copy of this template the same as your page name.
 * - Replace all occurances of 'formaction_template' in this template with your page name.
 */

/* this function is a placeholder that lets us easily load this file
*/
function xarpages_customapi_formaction_template($args)
{
    return true;
}

/* pageform validation function
    note, we can assume that the objects checkInput method has already been called 
    to fetch the posted vars and property validation
    This can do function does further validation and processing.
    note: return value 0 will go back to form for user to change input, 
          return value 1 will continue to next page, we do if all is ok
*/

function pageform_formaction_template_validate(&$inobj)
{
    // extract object fields into local arrays
    pageform_obj2arrays($inobj, $values, $invalids);

    // CHECK NEW USER ARGS
    $isvalid = true;
    
    // Check foobar property (if not already flagged)
    if (!empty($invalids['foobar'])) {
        // Better message than the default property one
        $invalids['foobar'] = 'Please enter a valid foobar';
    } else {
        // Do some additional checks that the property does not do,
        // e.g. check that the property has enough foo.
        if ($values['foobar'] != 'foofoo') {
            $invalids['foobar'] = 'Not enough foo: please enter more foo in foobar';
        } else {
            // We can change the property value, e.g. add some extra 'bar'.
            $values['foobar'] .= 'bar';
        }
    }
    
    // Put local values back into object for return
    $isvalid = pageform_arrays2obj($values, $invalids, $inobj);
    
    return $isvalid;
}

/* pageform processing function
    note, we can assume all input values are valid

    note: return value 0 will go back to form for user to change input, 
          return value 1 will continue to next page, we do if all is ok, and also on fatal errors
*/
function pageform_formaction_template_process(&$inobj, &$outobj)
{
    // Two return codes, to either take the user to the next page,
    // or to present the current form for resubmission.
    $return_next = 1;
    $return_invalid = 0;

    // Assume we will move to the next page
    $return = $return_next;

    // extract object fields into local arrays
    pageform_obj2arrays($inobj, $values, $invalids);
    pageform_obj2arrays($outobj, $outvalues, $outinvalids);

    // $values are the values submitted into this page
    // $invalids is the array of error messages
    // $outvalues are the values to be pushed back to the page
    // $outinvalids is the array of error messages to push back to the form

    //
    // Do processing here:
    // read $values and $invalids, write to $outvalues and $outinvalids.
    //

    // put local values back into objects for return
    $isvalid1 = pageform_arrays2obj($values, $invalids, $inobj);
    $isvalid2 = pageform_arrays2obj($outvalues, $outinvalids, $outobj);

    // If invalid fields are detected then make sure we return to the current form.
    // You not want to do this if processing is a one-way street.
    if (!$isvalid1 || !$isvalid2) $return = $return_invalid;
    
    return $return;
}

?>