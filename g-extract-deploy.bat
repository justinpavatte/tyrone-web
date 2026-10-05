@echo off
setlocal

set "ENV=ha"
set "DEST=%~dp0"
set "DOWNLOADS=%USERPROFILE%\Downloads"

rem --- Find and extract the newest ZIP from Downloads into this project folder ---
for /f "delims=" %%F in ('dir /b /a-d /o-d "%DOWNLOADS%\*.zip" 2^>nul') do (
    set "ZIP=%DOWNLOADS%\%%F"
    goto :found
)

echo No ZIP files found in Downloads.
pause
exit /b 1

:found
echo Using: %ZIP%
echo Destination: %DEST%

set "TEMP_DIR=%TEMP%\latest-zip-%RANDOM%%RANDOM%"
mkdir "%TEMP_DIR%" >nul 2>&1

powershell -NoProfile -Command "Expand-Archive -LiteralPath '%ZIP%' -DestinationPath '%TEMP_DIR%' -Force"

if errorlevel 1 (
    echo Failed to extract ZIP.
    rmdir /s /q "%TEMP_DIR%" >nul 2>&1
    pause
    exit /b 1
)

xcopy "%TEMP_DIR%\*" "%DEST%" /E /H /Y /Q >nul
rmdir /s /q "%TEMP_DIR%"

rem --- Determine the repository name from this folder ---
cd /d "%~dp0"
for %%I in ("%~dp0.") do set "REPO=%%~nxI"

rem --- Commit and push the updated project to GitHub ---
git add .
if errorlevel 1 goto :deploy_failed

git diff --cached --quiet
if errorlevel 1 (
    git commit -m "no comment"
    if errorlevel 1 goto :deploy_failed
)

git push
if errorlevel 1 goto :deploy_failed

rem --- Pull the latest GitHub version onto the selected server environment ---
ssh %ENV% "cd /srv/www/%REPO% && git pull"
if errorlevel 1 goto :deploy_failed

echo.
echo DEPLOY COMPLETE
pause
exit /b 0

:deploy_failed
echo.
echo DEPLOY FAILED
pause
exit /b 1