:: Simple batch file to send bitkeeper patches to xaraya
:: Usage: save this file with name sendbk.bat
::        type sendbk -r[range] recipient@address.com
::        the second argument is optional (defaults to patches@xaraya.com)
:: by Andrea Moro andream@bufera.org

@echo off

:initial
set patchfile=my.patch
if "%1" == "" goto error
set range=%1
if "%2" == "" goto defaultmail
set recipient=%2

goto sendit

:defaultmail
set recipient=patches@xaraya.com
goto sendit

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

