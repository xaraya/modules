var isCancel = 0

function validate2(form)
{
    var error_msg = ''
    if(isCancel)
        return true

    //Check Required Fields
    if (form.title.value == '')
        error_msg += "Event Title field must be filled in\n"
    if (form.event_desc.value == '')
        error_msg += "Event Description field must be filled in\n"
    if (form.month.value == '')
        error_msg += "Event Day field must be filled in\n"
    if (form.day.value == '')
        error_msg += "Event Month field must be filled in\n"
    if (form.event_year.value == '')
        error_msg += "Event Year field must be filled in\n"

    //If the top radio button is checked, check these fields
    if (document.cal_submit.event_endtype[0].checked)
    {
        if (form.event_endmonth.value == '')
            error_msg += "The End Date Month field must be filled in\n"
        if (form.event_endday.value == '')
            error_msg += "The End Date Day field must be filled in\n"
        if (form.event_endyear.value == '')
            error_msg += "The End Date Year field must be filled in\n"
    }
    //If phone fields are filled out (length = 10) or not filled out (length = 0)
    //then don't add error_msg. Any partally filled out phone number is not ok.
    //TODO Need to rewrite this check for more and other phone fields
    //var string = form.phone1.value + form.phone2.value + form.phone3.value
    //if (!(string.length == 10 || string.length == 0))
    //    error_msg += "Invalid Phone Number\n"
    var selectedRadioVal=getSelectedRadioValue(form.event_repeat);
    if (selectedRadioVal == 1 && (form.event_repeat_freq.value == '' || form.event_repeat_freq_type.selectedIndex == 0))
        error_msg += "All fields for 'Event Recurs Every' must be completely filled in\n"
    if (selectedRadioVal == 2 && (form.event_repeat_on_num.selectedIndex == 0 || form.event_repeat_on_day.selectedIndex == 0 || form.event_repeat_on_freq.value == ''))
        error_msg += "All fields for 'Event Recurs On' must be completely filled in\n"
    if (selectedRadioVal == 1 && form.event_repeat_freq.value == '0')
        error_msg += "'Every' can not be 0\n"
    if (selectedRadioVal == 2 && form.event_repeat_on_freq.value == '0')
        error_msg += "'On every month' can not be 0\n"

    if (!isEmailAddr(form.email.value) && (form.email.value != ""))
        error_msg += "Please enter a complete email address in the form: yourname@yourdomain.com"
    if (error_msg != '')
    {
        error_msg = "These Errors must be fixed first:\n" + error_msg
        alert(error_msg)
        return false;
    }
    return true;
}
//this function sets a select box back to the default for that object and returns a boolean indicating whether
//a default value existed
function restoreDefault(obj) {
 var isDefaultSelected = 0;
     for (var i = 0; i != obj.length; i++) {
         if (obj.options[i].defaultSelected == true) {
             obj.options[i].selected=true;
             isDefaultSelected=1;
         }
     }
     return isDefaultSelected;
}
//this function resets the repeating events values for each associated repeating type and
//determines if the enddate should be disabled. The repeating event data fields are only
//reset to their default values if a radio button (event repeat) calls this function.
function processEventRepeat(f,obj,callingobj)
{
        //first clear all repeating events fields if the event repeat radio has called this function
        if(callingobj.type=="radio")
        {
             f.event_repeat_freq.value=''
             f.event_repeat_freq_type.selectedIndex = '0'
             f.event_repeat_on_freq.value=''
             f.event_repeat_on_day.selectedIndex='0'
             f.event_repeat_on_num.selectedIndex='0'
        }
        //initialize the disabled fields to false
        f.event_endtype[0].disabled = false
        f.event_endtype[1].disabled = false
        f.event_endmonth.disabled = false
        f.event_endday.disabled = false
        f.event_endyear.disabled = false;

        var buttonArray=f.elements[obj.name];
        //determine which repeating event was selected and set the associated repeating event
        //data to either the object's default value or, if not set, to our default value
        if (buttonArray[0].checked)
        {
             //clear the repeating event end date data fields
             f.event_endtype[1].checked = true
             f.event_endmonth.selectedIndex = 0
             f.event_endday.selectedIndex = 0
             f.event_endyear.selectedIndex = 0
             //disable the end date section of the repeating events area
             f.event_endtype[0].disabled = true
             f.event_endtype[1].disabled = true
             f.event_endmonth.disabled = true
             f.event_endday.disabled = true
             f.event_endyear.disabled = true;
        }
        else if (buttonArray[1].checked) //repeats every
        {
             //if the repeating event radio called this function, reset the repeating event data values to their default
             if(callingobj.type=="radio")
             {
                    f.event_repeat_freq.value=1;
                    if(f.event_repeat_freq.defaultValue!='')
                         f.event_repeat_freq.value=f.event_repeat_freq.defaultValue;
                    if(!restoreDefault(f.event_repeat_freq_type))
                         f.event_repeat_freq_type.selectedIndex=1;
             }
             f.event_repeat_on_num.selectedIndex=0;
             f.event_repeat_on_day.selectedIndex=0;
             f.event_repeat_on_freq.value='';
        }
        else if (buttonArray[2].checked) //repeats on
        {
             //if the repeating event radio called this function, reset the repeating event data values to their default
             if(callingobj.type=="radio")
             {
                    if(!restoreDefault(f.event_repeat_on_num))
                         f.event_repeat_on_num.selectedIndex=1;
                    if(!restoreDefault(f.event_repeat_on_day))
                         f.event_repeat_on_day.selectedIndex=1;
                    f.event_repeat_on_freq.value=1;
                    if(f.event_repeat_on_freq.defaultValue!='')
                         f.event_repeat_on_freq.value=f.event_repeat_on_freq.defaultValue;
             }
             f.event_repeat_freq.value='';
             f.event_repeat_freq_type.selectedIndex=0;
        }
}
//this function determines if the duration times should be disabled.
function processEventTimed(f,obj,callingobj)
{
        //initialize the disabled fields to false
		// JJ 11-MAR-07: even all-day events can have a start time. They just don't have a duration.
        //f.event_starttimeh.disabled = false
        //f.event_starttimem.disabled = false
        //f.event_startampm.disabled = false
        f.event_dur_hours.disabled = false
        f.event_dur_minutes.disabled = false;

        var buttonArray=f.elements[obj.name];
        //determine which repeating event was selected and set the associated repeating event
        //data to either the object's default value or, if not set, to our default value
        if (buttonArray[0].checked)
        {
             //disable the end date section of the repeating events area
             //f.event_starttimeh.disabled = true
             //f.event_starttimem.disabled = true
             //f.event_startampm.disabled = true
             f.event_dur_hours.disabled = true
             f.event_dur_minutes.disabled = true;
        }
}

