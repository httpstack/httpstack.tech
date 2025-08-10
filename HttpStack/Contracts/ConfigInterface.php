<?php
namespace App\Contracts;
/**
 * Interface ConfigInterface
 * @package App\Contracts
 *
 * This interface defines the methods for handling configuration settings.
 */
interface ConfigInterface
{
    public function __construct(array $settings = []);
    /**
     * Get a configuration value by key.
     *
     * @param string $key
     * @return mixed
     */
    public function load(string $file): void;
    /**
     * Set a configuration value by key.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, string $value): void;
    /**
     * Check if a configuration key exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;
    /**
     * Get all configuration settings.
     *
     * @return array
     */
    public function all(): array;
    /**
     * Get a configuration value by key.
     *
     * @param string $key
     * @return mixed
     */
   // public function get(string $key): mixed;
    /**
     * Remove a configuration value by key.
     *
     * @param string $key
     * @return void
     */
    //public function remove(string $key): void;
    /**
     * Clear all configuration settings.
     *
     * @return void
     */
    //public function clear(): void;
    /**
     * Save the configuration settings to a file.
     *
     * @param string $file
     * @return void
     */
   // public function save(string $file): void;
}