<?php

namespace App\Providers;

use Cache;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use ZhiEq\Contracts\Listener;

class EventServiceProvider extends ServiceProvider
{

    protected static $cacheMapping = 'Event-Mapping-';

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
    ];

    /**
     * @return array
     */

    public function listens()
    {
        return array_merge(parent::listens(), $this->loadUserEventMapping(), $this->loadModelEventMapping());
    }

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * @return array
     */

    protected function loadUserEventMapping()
    {
        return $this->loadPathEventMapping('UserEvents', 'UserListeners');
    }

    /**
     * @return array
     */

    protected function loadModelEventMapping()
    {
        return $this->loadPathEventMapping('ModelEvents', 'ModelListeners');
    }

    /**
     * @param $eventPath
     * @param $listenerPath
     * @return array
     */

    protected function loadPathEventMapping($eventPath, $listenerPath)
    {
        $eventBasePath = app_path($eventPath);
        $pathList = array_merge([$eventBasePath], $this->getPathDirList($eventBasePath));
        $mapping = [];
        $eventNamespace = 'App\\' . $eventPath . '\\';
        $listenerNamespace = 'App\\' . $listenerPath . '\\';
        $listenerBasePath = app_path($listenerPath);
        foreach ($pathList as $basePath) {
            foreach (glob($basePath . '/*.php') as $event) {
                $eventName = substr(str_replace($basePath, '', $event), 1, -4);
                $group = substr($basePath, strrpos($basePath, $eventPath) + strlen($eventPath) + 1);
                $eventSpace = '';
                if (!empty($group)) {
                    $eventSpace = $group . '\\' . $eventName;
                    $eventName = $group . DIRECTORY_SEPARATOR . $eventName;
                }
                $eventListenerPath = $listenerBasePath . DIRECTORY_SEPARATOR . $eventName;
                $listeners = [];
                foreach (glob($eventListenerPath . '/*.php') as $listener) {
                    $listenerName = substr(str_replace($eventListenerPath, '', $listener), 1, -4);
                    /**
                     * @var Listener $listenerModel
                     */
                    $listenerModel = $listenerNamespace . $eventSpace . '\\' . $listenerName;
                    $listeners[$listenerModel::getListenOrder()] = $listenerModel;
                }
                ksort($listeners);
                !empty($listeners) && $mapping[$eventNamespace . $eventSpace] = array_values($listeners);
            }
        }
        return $mapping;
    }

    /**
     * @param $path
     * @return array
     */

    protected function getPathDirList($path)
    {
        $dirList = [];
        $pathFiles = dir($path);
        while ($file = $pathFiles->read()) {
            if ($file === '.') {
                continue;
            }
            if ($file === '..') {
                continue;
            }
            $fullPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                $dirList[] = $fullPath;
            }
        }
        return $dirList;
    }
}
