:: Simple batch file to send bitkeeper patches to xaraya
:: by Andrea Moro andream@bufera.org
:: Released in the public domain
::
:: Usage: save this file with name sendbk.bat
::	  sendbk
::		sends patch using range -r+ to patches@xaraya.com
::        sendbk -r1.1000..
::		sends patch using range -r1.1000 to patches@xaraya.com
::	  sendbk -r1.1000 mail@mymail.com
::	  	sends patch using range -r1.1000 to mail@mymail.com    
::

@echo off

:initial

set patchfile=my.patch
if "%1" == "" goto defaultrange
set range=%1
if "%2" == "" goto defaultmail
set recipient=%2
goto sendit

:defaultmail

set recipient=patches@xaraya.com
goto sendit

:defaultrange
set range=-r+


:error

echo ===============================================================
echo Usage: mysend -r1.1028.. address@xaraya.com
echo The email argument is optional (defaults to patches.xaraya.com)
echo ============================================================== 
goto done

:sendit
echo range = %range%
echo recipient = %recipient%
echo patchfile = %patchfile%
bk makepatch %range%>%patchfile%
blat %patchfile% -to %recipient%
del %patchfile%
echo %patchfile% removed

:done

