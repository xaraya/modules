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

set patchfile=amoro.patch

if "%1" == "-help" goto help
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
goto sendit

:help
echo ===============================================================
echo Usage: save this file with name sendbk.bat
echo sendbk
echo     sends patch using range -r+ to patches@xaraya.com
echo sendbk -r1.1000..
echo     sends patch using range -r1.1000 to patches@xaraya.com
echo sendbk -r1.1000 mail@mymail.com
echo     sends patch using range -r1.1000 to mail@mymail.com    
echo ===============================================================
goto done

:sendit
echo Using bk makepatch to create patch file %patchfile% using range %range% 
bk makepatch %range%>%patchfile%

echo Invoking blat.exe to send file to %recipient% ...
blat %patchfile% -to %recipient% -subject "[xar patch] range %range%"

del %patchfile%
echo File %patchfile% sent and removed

:done

