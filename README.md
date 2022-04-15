
## Messages Overview

A private messaging module.

Before using this module you must specify who should be permitted to
send to whom. This is done in the **Role Groups** fieldset in the
[Modify Config
tab]().

### Enable User Settings

To enable User Settings, check the box for "Enable User Settings" in the
[Modify Config
tab]() and
choose the specific settings to enable in the **User Settings**
fieldset. Depending on which settings you enable, the following may be
configurable by users in their Roles account:

  - *Tab to open after sending message* -- Redirect to this tab after
    sending a message.
  - *Email me when I receive a new message* -- Enable email delivery to
    the user's email address.
  - *Enable my autoreply* -- Turn on/off the autoreply.
  - *Autoreply text* -- The text of the autoreply.

### Admin Settings

These settings are available in the **Admin Settings** fieldset:

  - *Allow anonymous messages* -- Allow users to send messages
    anonymously.
  - *Strip tags from autoreplies* -- If you do not enable this, and you
    turn on the "Show template filenames" settings in the themes module,
    HTML comments will appear in autoreplies. It is recommended that you
    leave this checked.
  - *Default tab to open after send* -- Redirect to this tab after
    sending a message. The "Tab to open" user setting will override this
    admin setting.

### How to Create a Link to Message a Specific User

In these examples, 11 is the user ID of the recipient:

    ##xarController::URL('messages','user','new',array('to' => 11))##

Optionally, set the 'opt' argument to true if you would like the form to
display a userlist dropdown with the value set to the specified user:

    ##xarController::URL('messages','user','new',array('to' => 11, 'opt' => true))##

If you don't set 'opt' to true, the form will display the recipient's
username and pass the user ID in a hidden input.

Depending on context, a link might look something like...

    <a href="##xarController::URL('messages','user','new',array('to' => $uid))##">
            Send a PM to ##xarUser::getVar('name',$uid)##
        </a>

### Message Templates

In the Messages module's xartemplates directory, some default message
templates are provided:

  - user-email-subject.xt
  - user-email-body.xt
  - user-autoreply-subject.xt
  - user-autoreply-body.xt

