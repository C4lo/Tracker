@echo off
set HOST=ht7m.your-database.de
set USER=jerome_tracker_w
set PASSWORD=KTMf57rhQ17d9zjd
set LOCAL_PATH="C:\Users\Jérôme\Documents\GitHub\Tracker"
set REMOTE_PATH="/public_html/initiativeTracker"

:: FTP-Skript erstellen
(
echo open %HOST%
echo user %USER% %PASSWORD%
echo lcd %LOCAL_PATH%
echo cd %REMOTE_PATH%
echo binary
echo put css/output.css
echo mput *.html
echo lcd users
echo cd users
echo mput *.html
echo lcd ..\DM
echo cd ../DM
echo mput *.html
echo lcd ..\view
echo cd ../view
echo mput *.html
echo bye
) > ftp_commands.txt

:: FTP-Befehl ausführen
ftp -s:ftp_commands.txt

:: Temporäre Datei löschen
del ftp_commands.txt

echo Upload abgeschlossen!
pause
