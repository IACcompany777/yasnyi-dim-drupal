@echo off
cd /d "%~dp0web"
start "" http://127.0.0.1:8888/
php -S 127.0.0.1:8888 .ht.router.php
