<?php

namespace HttpStack\Config;

class Config
{
    protected array $settings = [];

    public function __construct(protected string $configDir)
    {
        $this->configDir = $configDir;
        $this->loadConfigs();
    }
    public function getSettings()
    {
        return $this->settings;
    }
    public function setConfigDir(string $dir)
    {
        $this->configDir = $dir;
    }
    public function loadConfigs(): void
    {
        $configDir = $this->configDir;
        $configs = [];
        foreach (glob($configDir . '/*.php') as $file) {
            $key = basename($file, '.php');
            $configs[$key] = include $file;
        }
        $this->settings = array_merge($this->settings, $configs);
    }
}