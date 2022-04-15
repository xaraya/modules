
## Overview

### What is it?

crispBB is a feature-rich forum module for Xaraya

### How to use it?

If this is your first visit to this part of your Xaraya site, please
take a minute to read through the following introduction, in order to
explore the different possibilities offered by the crispBB module.

  - Create categories for your forums.

  - #### Categories
    
    A couple of categories will have been created for you when you
    installed crispBB. Before creating forums, you might want to create
    categories for your forums, or rename the ones already there. Unless
    you have particular reason to, it's not recommended that you change
    the base category for crispBB once you have created forums, so if
    you don't want to take the default, now's the time to change it. You
    can add, edit, re-order and remove categories from the categories
    administration pages at any time. You can also re-order categories
    used by crispBB from the crispBB module itself.

  - Create forums for your users to post in.

  - Assign privileges and permissions to your users.

  - Build your community...

### Included Blocks

  - **Topitems Block** - The topitems block allows you to list topics
    from the forums. You can choose to list topics from any or all of
    the forums available, optionally specifiying the field to sort
    topics on, the direction they should be sorted and the number of
    topics to display.
  - **Userpanel Block** - The userpanel block displays any combination
    of the data contained in the user info panel. *NOTE: if you choose
    to display any of the time options in the block it's recommended
    that you don't cache the block.*

### Included Hooks

*crispBB has the following hooks*

  - **Search Hook** - If you have the search module activated, this hook
    allows crispBB's search options and results to be displayed in the
    search module. To use it, hook crispBB to the Search module.
  - **Waiting Content Hook** - If you have the waitingcontent block of
    the Base module activated, this hook will display any forum topics
    and topic replies awaiting approval in the block. To use it, hook
    crispBB to the Base module.
  - **Modifyconfig Hook** - Shows hook configuration options in modify
    config screen of hooked modules. Allows you to set the following
    options...
      - Set the forum to create related topics in for current
        module/itemtype.
      - Optionally choose to display latest X replies if a topic has
        already been created. (see display hook)
      - Optionally display the quick reply if a topic has already been
        created. (see display hook)
  - **Display Hook** - Displays the following
      - Displays a link to create a topic to discuss the current item.
        When clicked a new topic will be created, linking to the module
        item, and the user will be redirected to make a reply to the
        newly created topic. or...
      - If a topic has already been created, displays a link to the
        topic.
      - Optionally display the last X replies if a topic has already
        been created. (see modifyconfig hook)
      - Optionally display the quick reply form if a topic has already
        been created. (see modifyconfig hook)
  - **New / Create Hook** - Allows a new related topic to be created
    when creating a new hooked module item, topic will include link to,
    and optional excerpt from the created item. Optionally, an existing
    topic can be associated with the newly created item
  - **Modify / Update Hook** - Allows the associated topic to be
    specified or changed when editing a hooked module item
  - **Delete Hook** - Deletes the associated hook entry when a hooked
    module item is deleted. This does not remove the related topic.
  - **Remove Hook** - Removes associated hook entries from the database
    when a hooked module is removed from the system. This does not
    remove related topics.

### Included Dynamic Data Properties

*crispBB has no included properties at present*

### Further Information

  - Official crispBB home page at
    [http://www.crispbb.com](http://www.crispbb.com "crispBB website")
  - Latest release can always be found at [crispBB
    Releases](http://www.crispbb.com/forums/crispbb-releases/t3?action=lastreply "crispBB Releases")
  - crispBB Extension page at [Xaraya Extensions and
    Releases](http://www.xaraya.com/index.php/release/970.html "crispBB Module - Xaraya Extension 970").
  - Related tutorials and documentation on crispBB found at [Xaraya
    Documentation.](http://www.xaraya.com/index.php/keywords/crispbb/ "Related documentation on crispBB")

### Additional credits

  - Most icons in modules/crispbb/xarimages/tango/ courtesy of the
    [Tango Desktop
    Project](http://tango.freedesktop.org/Tango_Desktop_Project) and are
    in the public domain. The remaining icons created by Xaraya
    developers, licensed under the GPL.
  - Icons in modules/crispbb/xarimages/icons/ are courtesy of [FamFamFam
    Silk Icons](http://www.famfamfam.com/lab/icons/silk/) by Mark James
    and are licensed under [Creative Commons Attribution 3.0
    License](http://creativecommons.org/licenses/by/3.0/).

** crispBB - Overview**  Version - 

