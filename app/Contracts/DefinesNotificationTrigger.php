<?php

namespace App\Contracts;

interface DefinesNotificationTrigger
{
    /**
     * Return a trigger definition.
     *
     * Expected keys:
     * - trigger (string, required)
     * - name (string, optional)
     * - description (string, optional)
     * - module (string, optional)
     * - is_enabled (bool, optional)
     * - receivers (array, optional)
     */
    public static function notificationTriggerDefinition(): array;
}
