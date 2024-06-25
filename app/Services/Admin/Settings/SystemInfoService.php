<?php

namespace App\Services\Admin\Settings;

use Illuminate\Foundation\Application;

class SystemInfoService
{
    public function execute()
    {
        // Application Information
        $app_version = config('app.version');

        // System Information
        $current_php_version = phpversion();
        $minimum_php_version = 8.1;
        $matched_php_requirement = version_compare($current_php_version, $minimum_php_version, '>=');
        $current_laravel_version = Application::VERSION;

        $current_mysql_version = \DB::select('select version()')[0]->{'version()'};
        $minimum_mysql_version = '5.6+';
        $matched_mysql_requirement = version_compare($current_mysql_version, $minimum_mysql_version, '>=');

        $memory_limit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $memory_limit, $matches)) {
            if ($matches[2] == 'G') {
                $memory_limit = $matches[1] * 1024 * 1024 * 1024; // nnnM -> nnn GB
            } elseif ($matches[2] == 'M') {
                $memory_limit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } elseif ($matches[2] == 'K') {
                $memory_limit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        $matched_memory_limit = $memory_limit == -1 || $memory_limit >= (256 * 1024 * 1024);

        $php_ini = [
            'file_uploads' => ini_get('file_uploads'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'matched_upload_max_filesize' => str_replace(['M', 'G'], '', ini_get('upload_max_filesize')) >= 128,
            'post_max_size' => ini_get('post_max_size'),
            'matched_post_max_size' => str_replace(['M', 'G'], '', ini_get('post_max_size')) >= 128,
            'allow_url_fopen' => ini_get('allow_url_fopen'),
            'max_execution_time' => ini_get('max_execution_time'),
            'max_input_time' => ini_get('max_input_time'),
            'max_input_vars' => ini_get('max_input_vars'),
            'memory_limit' => $memory_limit,
            'matched_memory_limit' => $matched_memory_limit,
        ];

        // Extension Information
        $required_extensions = ['json', 'mbstring', 'zip', 'openssl', 'tokenizer', 'curl', 'fileinfo', 'gd', 'pdo_mysql'];
        $extension_permissions = [];

        foreach ($required_extensions as $extension) {
            $extension_permissions[$extension] = extension_loaded($extension);
        }

        // Filesystem Permissions
        $fileSystemPaths = ['storage', 'bootstrap/cache/', 'public', 'resources/lang', 'resources/views'];
        $fileSystemPermissions = [];

        foreach ($fileSystemPaths as $path) {
            $fileSystemPermissions[$path] = is_writable(base_path($path));
        }

        return [
            'app_version' => $app_version,
            'current_php_version' => $current_php_version,
            'minimum_php_version' => $minimum_php_version,
            'matched_php_requirement' => $matched_php_requirement,
            'current_laravel_version' => $current_laravel_version,
            'current_mysql_version' => $current_mysql_version,
            'minimum_mysql_version' => $minimum_mysql_version,
            'matched_mysql_requirement' => $matched_mysql_requirement,
            'php_ini' => $php_ini,
            'extension_permissions' => $extension_permissions,
            'fileSystemPermissions' => $fileSystemPermissions,
        ];
    }
}
