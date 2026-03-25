<?php

namespace App\Utils;

class DemoResetHelper
{
    /**
     * Check if demo data is currently being reset
     *
     * @return boolean
     */
    public static function isResetting()
    {
        $flagFile = storage_path('framework/demo_resetting.flag');
        return file_exists($flagFile);
    }

    /**
     * Update the progress message for the demo reset
     *
     * @param string $message
     * @return void
     */
    public static function updateProgress($message)
    {
        $progressFile = storage_path('framework/demo_progress.txt');
        file_put_contents($progressFile, $message);
    }

    /**
     * Get the current progress message
     *
     * @return string
     */
    public static function getProgress()
    {
        $progressFile = storage_path('framework/demo_progress.txt');
        if (file_exists($progressFile)) {
            return file_get_contents($progressFile);
        }
        return 'Initializing demo data reset...';
    }

    /**
     * Clear the progress file
     *
     * @return void
     */
    public static function clearProgress()
    {
        $progressFile = storage_path('framework/demo_progress.txt');
        if (file_exists($progressFile)) {
            @unlink($progressFile);
        }
    }
}
