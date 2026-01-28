<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    /**
     * The AuthService class
     *
     * @return \App\Services\AuthService
     */
    public static function authService(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authService');
        }

        return new \App\Services\AuthService();
    }

    /**
     * The EmailService class
     *
     * @return \App\Services\EmailService
     */
    public static function emailService(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('emailService');
        }

        return new \App\Services\EmailService();
    }

    /**
     * The RecaptchaService class
     *
     * @return \App\Services\RecaptchaService
     */
    public static function recaptchaService(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('recaptchaService');
        }

        return new \App\Services\RecaptchaService();
    }

    /**
     * The AuditLogService class
     *
     * @return \App\Services\AuditLogService
     */
    public static function auditLog(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('auditLog');
        }

        return new \App\Services\AuditLogService();
    }

    /**
     * The ModerationService class
     *
     * @return \App\Services\ModerationService
     */
    public static function moderation(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('moderation');
        }

        return new \App\Services\ModerationService();
    }

    /**
     * The AuthorizationService class
     *
     * @return \App\Services\AuthorizationService
     */
    public static function authorization(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('authorization');
        }

        return new \App\Services\AuthorizationService();
    }
}
