function xar_helpdesk_validateQuickHistory(commentForm)
{
    var message = '';

    if( commentForm.comment.value.length < 5 )
    {
        message += "\n- a valid comment";
    }
    if( message.length < 1 )
    {
        return true;
    }

    alert("Please provide the following details before committing:" + message);
    // cancel submit
    return false;
}
