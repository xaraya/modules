<?php

/*
 * Base class for custom validation and processing classes (for 'multiform').
 */

class xarpages_customapi_multiform_master
{
    // The form object.
    var $formobject = NULL;

    // The raw values from the form object.
    // Can be altered by the validation functions.
    var $values = array();

    // Invalids array (messages for each property that is invalid).
    // Can be altered by the validation functions.
    var $invalids = array();

    // Next page ID
    // This is the page the user should go to next, if the 'next' button
    // has been pressed.
    var $next_page_pid = NULL;

    // New URL, which can be set by a processing function.
    var $redirect_url = NULL;

    // Work data array, used to pass information form one form to another,
    // through the processing functions (only available for writing to the
    // processing functions).
    var $workdata = array();

    // The accumulated form data so far.
    // Read-only for both validation and processing functions.
    // Can be referenced for inter-page validation rules.
    var $formdata = array();

    // Set if this is the last processing step.
    // After this step, the session will be cleared before jumping to the last page.
    var $last_page = false;

    /*
    * Constructor.
    * This sets up the main data that the validation and processing functions
    * require: the form object and the work data.
    * The formobject (expanded into arrays) is read and write for the validation
    * functions, and read-only for the processing functions.
    * The workdata is read-only for the validation functions and read/write for
    * the processing functions.
    */

    function xarpages_customapi_multiform_master($args)
    {
        // Store the object and extract its data into arrays.
        if (isset($args['formobject'])) {
            $this->formobject = $args['formobject'];
            $this->extract_formobject();
        }

        // Store the workdata array.
        if (isset($args['workdata'])) $this->workdata = $args['workdata'];

        // Store the accumlative form data array.
        if (isset($args['formdata'])) $this->formdata = $args['formdata'];
    }

    // Take the values and invalids from the object,
    // and place them into the object arrays
    function extract_formobject()
    {
        $this->values = array();
        $this->invalids = array();

        if (!empty($this->formobject->properties)) {
            foreach($this->formobject->properties as $name => $property) {
                $this->values[$name] = $property->getValue();
                if (!empty($property->invalid)) $this->invalids[$name] = $property->invalid;
            }
        }
    }

    // Take the values and invalids from the object arrays,
    // and place them back into the form object.
    function compact_formobject()
    {
        if (!empty($this->formobject->properties)) {
            foreach($this->formobject->properties as $name => $property) {
                if (isset($this->invalids[$name])) $this->formobject->properties[$name]->invalid = $this->invalids[$name];
                if (isset($this->values[$name])) $this->formobject->properties[$name]->setValue($this->values[$name]);
            }
        }

        return $this->formobject;
    }

    // Convert a page name to a pid
    // Allows processing functions to jump to a page by name.
    function pagename_to_pid($pagename)
    {
        $page = xarModAPIfunc('xarpages', 'user', 'getpage', array('name' => $pagename));

        if (!empty($page)) {
            $pid = $page['pid'];
        } else {
            $pid = 0;
        }

        return $pid;
    }

    // DEPRCATED
    // End the sequence.
    // This clears out the session completely. It is called in the very last
    // processing step, before the user is redirected off to a 'thankyou'
    // page or some other location.
    function finish()
    {
        $this->last_page = true;
        return true;
    }

    // Set the next page by pid
    function set_next_page_pid($pid) {
        $this->next_page_pid = $pid;
    }

    // Set the next page by its name
    function set_next_page_name($name) {
        $pid = $this->pagename_to_pid($name);
        if (!empty($pid)) $this->next_page_pid = $pid;
    }
}

?>