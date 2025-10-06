<?php
require_once 'lib/auth.php';
logout();
header('Location: login.php');
exit();
