@echo off
setlocal

set "ENV=ha"

rem --- Determine the repository name from this folder ---
cd /d "%~dp0"
for %%I in ("%~dp0.") do set "REPO=%%~nxI"

rem --- Stage files ---
git add .
if errorlevel 1 goto :failed

rem --- Commit only if there are staged changes ---
git diff --cached --quiet
if errorlevel 1 (
    git commit -m "no comment"
    if errorlevel 1 goto :failed
)

rem --- Push and wait for Git to finish ---
git push
if errorlevel 1 goto :failed

rem --- Pull on the server and wait for SSH/git pull to finish ---
ssh %ENV% "cd /srv/www/%REPO% && git pull"
if errorlevel 1 goto :failed

echo.
echo DEPLOY COMPLETE
pause
exit /b 0

:failed
echo.
echo DEPLOY FAILED
pause
exit /b 1