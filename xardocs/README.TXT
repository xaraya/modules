The following are instructions on how to make the htmlarea module run on your Xaraya site
=========================================================================================

For the Site Administrator:

1. Install this module like you would any other Xaraya module. That is:
   a. put the htmlarea folder under html/modules
   b. go to the admin menu modules -> View All
   c. Click on the "install" link of the htmlarea module

2. The htmlarea editor is not turned on by default. Once the htmlarea module is installed it needs to be turned on. To do this:
   a. go to the admin menu base -> Modify Config
   b. choose the tab "Other"
   c. on the wysiwyg editor dropdown choose "htmlarea"

3. Once you have done this the htmlarea editor will replace the textarea fields in any Xaraya module that has been enabled. To see an example, go to the admin menu roles -> Email messaging and click on the "Templates" tab.


For the Module Developer:

In order to make your module compliant with the htmlarea module you need to add the following snippet at the top of any template that contains textfields you want to enable for the editor:

    <xar:if condition="xarModGetVar('base','editor') eq 'htmlarea'">
        <xar:template file="htmlarea_insert" module="htmlarea" type="module" />
    </xar:if>