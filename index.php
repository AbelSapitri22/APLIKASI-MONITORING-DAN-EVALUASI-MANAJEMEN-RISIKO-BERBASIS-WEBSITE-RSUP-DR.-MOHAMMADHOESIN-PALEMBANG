<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/PageNameEnum.php");
$pageName = PageNameEnum::DASHBOARD;

include_once('pages/templates/header.php');
include_once('pages/templates/sidebar.php');
include_once('pages/home.php');
include_once('pages/templates/footer.php');
