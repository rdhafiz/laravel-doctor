<?php

declare(strict_types=1);

use Codevioso\LaravelDoctor\Checks\AppDebugInProductionCheck;
use Codevioso\LaravelDoctor\Checks\AppEnvMismatchCheck;
use Codevioso\LaravelDoctor\Checks\CacheFileInProductionCheck;
use Codevioso\LaravelDoctor\Checks\ComposerAutoloadOptimizedCheck;
use Codevioso\LaravelDoctor\Checks\ConfigCacheCheck;
use Codevioso\LaravelDoctor\Checks\FilePermissionsCheck;
use Codevioso\LaravelDoctor\Checks\LogChannelSingleHighTrafficCheck;
use Codevioso\LaravelDoctor\Checks\QueueSyncInProductionCheck;
use Codevioso\LaravelDoctor\Checks\QueueWorkerRunningCheck;
use Codevioso\LaravelDoctor\Checks\RequiredPhpExtensionsCheck;
use Codevioso\LaravelDoctor\Checks\RouteCacheCheck;
use Codevioso\LaravelDoctor\Checks\SessionFileInProductionCheck;
use Codevioso\LaravelDoctor\Checks\StorageSymlinkCheck;
use Codevioso\LaravelDoctor\Checks\ViewCacheCheck;

return [
    'enabled' => true,

    // Per-check enable/disable. Format: [ CheckClass::class => true|false ]
    'checks' => [
        AppDebugInProductionCheck::class => true,
        AppEnvMismatchCheck::class => true,
        ConfigCacheCheck::class => true,
        RouteCacheCheck::class => true,
        ViewCacheCheck::class => true,
        QueueSyncInProductionCheck::class => true,
        CacheFileInProductionCheck::class => true,
        SessionFileInProductionCheck::class => true,
        StorageSymlinkCheck::class => true,
        QueueWorkerRunningCheck::class => true,
        LogChannelSingleHighTrafficCheck::class => true,
        ComposerAutoloadOptimizedCheck::class => true,
        RequiredPhpExtensionsCheck::class => true,
        FilePermissionsCheck::class => true,
    ],

    // Custom checks (all enabled by default). Add your own CheckInterface implementations.
    // Example: 'custom_checks' => [ \App\Doctor\MyCustomCheck::class ],
    'custom_checks' => [],

    'environment' => [
        'high_traffic' => false,
    ],
];
