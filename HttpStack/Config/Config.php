<?php

namespace HttpStack\Config;

class Config
{
    protected string $configDir = "/config";
    protected array $settings = [];

    public function __construct()
    {
        $this->configDir = "/config";
    }
    public function getSettings()
    {
        return $this->settings;
    }
}
