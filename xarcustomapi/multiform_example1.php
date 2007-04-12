<?php

/**
 * Validation and processing class for the 'example1' multiform sequence.
 * This example is very much work in progress, and will be replaced soon.
 */

function xarpages_customapi_multiform_example1($args)
{
    return new xarpages_customapi_multiform_example1($args);
}

class xarpages_customapi_multiform_example1 extends xarpages_customapi_multiform_master
{
    // Constructor for form set 'example1'
    // Leaving this constructor out completely would have the same effect as just
    // calling the parent constructor.
    function xarpages_customapi_multiform_example1($args)
    {
        // Call the parent constructor.
        parent::xarpages_customapi_multiform_master($args);
    }

    //
    // The following three methods show what can be done for page 'ex1page1' in the sequence.
    // We have the following child pages of page 'example1':
    // ex1page1 -> ex1page2 -> ex1page3 -> ex1page4
    // Pages 1 to 3 are of type 'multiform' and page 4 is of type 'html'.
    //
    
    // The initialise method is called before the form is displayed.
    function initialise_ex1page1($args)
    {
        // If the name field is blank, then put either the current user's name into it
        // (if logged in) or a short message.
        if (empty($this->values['name'])) {
            if (xarUserIsLoggedIn()) {
                $this->values['name'] = xarUserGetVar('name');
            } else {
                $this->values['name'] = xarML('<Enter your name here>');
            }
        }

        // Return true to indicate we have changed anything.
        // Returning false means we have not changed anything.
        return true;
    }

    // Validate page 'ex1page1'.
    // This is invoked when the user submits the form on this page.
    function validate_ex1page1($args)
    {
        // If the user has entered an age too high, then raise an error against that property.
        if ($this->values['age'] > 65) $this->invalids['age'] = xarML('Too old to skateboard!');

        // If any 'invalids' errors are returned, then the form will be considered to
        // be in error. You can force this error condition without returning any
        // error messages by returning false here.
        // Returning false from here will force the same form to be re-presented to the user.
        return true;
    }

    // Processing for page 'ex1page1'.
    // This is invoked only if both the following ocnditions are true:
    // - The form is completely valid (no errors); and
    // - The user has requested to go to the 'next' page.
    function process_ex1page1($args)
    {
        // If the user enters 'hello' into the 'name' property, then do two things:
        // - Set the name to 'world', overwriting the 'hello'
        // - Go direct to page 'ex1page3', effectively skipping over page 'ex1page2'.
        // This demonstrates how branches can be implemented.
        if ($this->values['name'] == 'hello') {
            $this->values['name'] = 'world';
            $this->set_next_page_name('ex1page3');
        }

        // Save the name in the workdata.
        // We are going to pass this out right at the end of the form sequence, so it
        // can be displayed on a html page after the sequence is complete.
        $this->workdata['name'] = $this->values['name'];

        // Return true to indicate that all is well.
        // Returning false from here will clear the session and send the user to an error page.
        // TODO: provide a method to return a global error message, that can be displayed on
        // the error page. It could be something like "payment gateway returned an error".
        return true;
    }

    // Page 3 is the final page.
    // We don't do any custom validation for it.
    function process_ex1page3($args)
    {
        // This is the point where all the collected data (in $this->formdata)
        // and work data (in $this->workdata) can be processed for the final time.
        // It may involved pushing a transaction to a payment gateway, or sending an
        // e-mail or storing the data in a table.

        // ...final processing here...

        // The final thing that page 3 does is to close the session. This will
        // clear out all data that has been collected in this sequence of forms.
        // The next page in the sequence must not be a 'multiform' type page,
        // because there will not longer be a session for it to validate against.
        // Instead, the last page could be a static 'html' page in the same page group,
        // or $this->set_next_page_name('pagename') can be used to send the user to some
        // other page altogether (e.g. a 'thankyou' page).
        //
        // We have passed in the workdata array, which means that data will be available
        // to the next page visited. It can be retrieved using:
        // $workdata = xarModAPIfunc('xarpages', 'multiform', 'passdata');
        // This connects the form sequence to the outside functionality, in a similar
        // way to a function returning a value.
        return $this->finish($this->workdata);
    }
}

?>