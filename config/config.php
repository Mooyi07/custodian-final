<?php

if (!session_status()) {
    session_start();
} else {
    session_start();
}

define('APPNAME', 'Property Custodian System');
define('URLROOT', 'http://' . $_SERVER['SERVER_ADDR'] . '/custodian');
