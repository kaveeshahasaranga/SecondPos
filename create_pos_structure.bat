@echo off
REM ====================================================================
REM  Batch script to create the folder and file structure for the
REM  Simple Watch POS System.
REM  Save this file as "create_pos_structure.bat" and run it.
REM ====================================================================

echo Creating the main project directory: watch-pos...
mkdir watch-pos
cd watch-pos

echo Creating sub-directories...
mkdir api
mkdir data
mkdir assets
mkdir assets\js
mkdir assets\css
mkdir templates

echo.
echo Creating PHP files in the root directory...
type nul > index.php
type nul > products.php
type nul > repairs.php

echo Creating API endpoint files...
type nul > api\products_api.php
type nul > api\sales_api.php
type nul > api\repairs_api.php

echo Creating JSON data files...
echo [] > data\products.json
echo [] > data\sales.json
echo [] > data\repairs.json

echo Creating asset files...
type nul > assets\js\app.js
type nul > assets\css\custom.css

echo Creating template files...
type nul > templates\header.php
type nul > templates\footer.php

echo.
echo =================================================
echo  Project structure for 'watch-pos' created successfully!
echo =================================================
echo.

REM Go back to the original directory
cd ..

REM Pause to see the output before the window closes
pause
