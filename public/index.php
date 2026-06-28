<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/Views/layouts/config-view.php';
require_once __DIR__ . '/../routes.php';