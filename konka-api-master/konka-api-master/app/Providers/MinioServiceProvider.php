<?php

namespace App\Providers;


use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;

class MinioServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('minio', function () {
            return new S3Client([
                'version' => 'latest',
                'region' => 'us-east-1',
                'endpoint' => config('filesystems.disks.minio.endpoint'),
                'use_path_style_endpoint' => true,
                'credentials' => [
                    'key' => config('filesystems.disks.minio.key'),
                    'secret' => config('filesystems.disks.minio.secret'),
                ],
            ]);
        });
    }

}