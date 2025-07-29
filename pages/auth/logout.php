<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/smr/models/UsersModel.php');
require_once($_SERVER['DOCUMENT_ROOT'] . "/smr/enum/URLEnum.php");

session_start();
$base_url = URLEnum::BASE_URL;

$usersModel = new UsersModel();

if ($usersModel->logout()) {
    header('Location: ' .  URLEnum::getLoginURL());
}
