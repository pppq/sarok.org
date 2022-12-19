<?php

///////////////////////
// Database settings
///////////////////////

$db_host = "mysql";
$db_name = "sarok";
$db_user = "sarok";
$db_password = "such_sec0re";
$db_port = 3306;

//////////////////
// Log settings
//////////////////

$logfile = "../logs/sarok_" . date("Y_m_d_G_i_s") . ".txt";
$general_logfile = "../logs/log.txt";
$log_level = 3;
$system_email = "nobody@localhost";

///////////////////
// Mail settings
///////////////////

$mail_secretWord = "SECRET";

///////////////////
// Blog settings
///////////////////

$day_range = 1;
$refreshTime = 200;
$skinName = "default";
$tagGrades = 5;   //[1-5]
$tidy_check = false;

///////////////////
// General settings
///////////////////

$appRoot = "/srv/app";
$gen_hostname = "www.sarok.org";
$img_hostname = "img.sarok.org";
$cookiedomain = "sarok.org";

$gmap_key = "SECRET";

///////////////////
// I18N settings
///////////////////

$honapok = array(
    "Január", // index 0
    "Január",
    "Február",
    "Március",
    "Április",
    "Májús",
    "Június",
    "Július",
    "Augusztus",
    "Szeptember",
    "Október",
    "November",
    "December",
);

$dayofweek = array(
    "H", 
    "K", 
    "Sz", 
    "Cs", 
    "P", 
    "Sz", 
    "V",
);

////////////////////
// Image settings
////////////////////

$imageMaxWidth = 640;
$imageMaxHeight = 640;
$imageQuality = 80;
$thumbWidth = 80;
