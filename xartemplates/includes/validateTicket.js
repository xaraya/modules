function xar_helpdesk_validateTicket()
{
    var f = document.newticket;
    var msg = '';

    if( f.name.value != null )
    {
        if( f.name.value.length < 3 && f.userid.value <= 2  )
            msg += "\n- a valid name";
    }
    if( f.email != null )
    {
        if( f.email.value.length < 3 )
        {
            msg += "\n- a valid email";
        }
    }
    if( f.domain != null )
    {
        if( f.domain.value.length == '' )
        {
            msg += "\n- a valid domain";
        }
    }
    if( f.subject.value.length < 3 )
    {
        msg += "\n- a valid subject";
    }
    if( f.issue.value.length < 3 )
    {
        msg += "\n- a valid issue";
    }
    if( msg.length < 1 )
    {
        document.newticket.submit();
    }
    else
    {
        alert( "Please provide the following details before submitting:" + msg );
        // cancel submit
        return false;
    }
}
