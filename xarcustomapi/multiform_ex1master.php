<?php

/**
 * Validation and processing class for the 'ex1master' multiform sequence.
 * This example is very much work in progress, and will be replaced soon.
 */

function xarpages_customapi_multiform_ex1master($args)
{
    return new xarpages_customapi_multiform_ex1master($args);
}

// CHECKME: Should classes go in a folder of their own?
// There are no simple methods for loading classes at the moment.

class xarpages_customapi_multiform_ex1master extends xarpages_customapi_multiform_master
{
    // Constructor for form set 'ex1master'
    function xarpages_customapi_multiform_ex1master($args)
    {
        // Call the parent constructor.
        parent::xarpages_customapi_multiform_master($args);
    }

    // Validate page 'ex1page1'
    function validate_ex1page1($args)
    {
        if ($this->values['age'] > 40) $this->invalids['age'] = 'Too old!';
        return true;
    }

    // Processing for page 'ex1page1'
    // Support functions that may be needed:
    // - clear history: set this page as a one-way trip, so the user cannot go back to it.
    //   (It may be better to flag those pages we are not allowed to go back to, rather than
    //   actually removing them from the history. Also this could be done as a flag on the
    //   page itself. I guess the flag would work, allowing the process function to set the
    //   flag. We could call these 'milestone' pages, which can only be 'processed' once.)
    function process_ex1page1($args)
    {
        if ($this->values['name'] == 'hello') $this->values['name'] = 'world';
        $this->set_next_page_name('ex1page3');
        return true;
    }

    function validate_ex1page3($args)
    {
        echo "<pre>PAGE3";
        var_dump($this->formdata);
        echo "</pre>";
        return true;
    }

    // This is only called on a redirect to a page (i.e. on first going into
    // a page, and not when saving a form).
    function initialise_ex1page1($args)
    {
        if (empty($this->values['age'])) $this->values['age'] = 39;
        // Return true to indicate we have changed anything.
        // Returning false saves a little time at the other end.
        return true;
    }

    function process_ex1page3($args)
    {
        return $this->finish();
    }
}

?>