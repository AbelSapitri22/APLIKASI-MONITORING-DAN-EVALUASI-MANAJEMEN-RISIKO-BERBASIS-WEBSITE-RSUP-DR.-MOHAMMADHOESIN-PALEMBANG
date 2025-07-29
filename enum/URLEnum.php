<?php

class URLEnum
{
    const BASE_URL = 'http://localhost/smr';

    public static function getDashboardURL()
    {
        return self::BASE_URL . '/';
    }

    public static function getAccountURL()
    {
        return self::BASE_URL . '/pages/account/';
    }

    public static function getEditAccountURL()
    {
        return self::BASE_URL . '/pages/account/edit.php';
    }

    public static function getRiskAssasmentURL()
    {
        return self::BASE_URL . '/pages/penilaian-risiko/';
    }

    public static function getRiskAssasmentRejectedURL()
    {
        return self::BASE_URL . '/pages/penilaian-risiko-ditolak/';
    }

    public static function getMonitoringReviewURL()
    {
        return self::BASE_URL . '/pages/pemantauan-reviu/';
    }

    public static function getMonitoringReviewRejectedURL()
    {
        return self::BASE_URL . '/pages/pemantauan-reviu-ditolak/';
    }

    public static function getUnitURL()
    {
        return self::BASE_URL . '/pages/unit/';
    }

    public static function getUsersURL()
    {
        return self::BASE_URL . '/pages/users/';
    }

    public static function getChangePasswordURL()
    {
        return self::BASE_URL . '/pages/account/change-password.php';
    }

    public static function getLoginURL()
    {
        return self::BASE_URL . '/pages/auth/login.php';
    }

    public static function getRegisterURL()
    {
        return self::BASE_URL . '/pages/auth/register.php';
    }

    public static function getForgotPasswordURL()
    {
        return self::BASE_URL . '/pages/auth/forgot-password.php';
    }

    public static function getLogoutURL()
    {
        return self::BASE_URL . '/pages/auth/logout.php';
    }
}
