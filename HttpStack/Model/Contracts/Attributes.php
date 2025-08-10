<?php
namespace HttpStack\Model\Contracts;
interface Attributes
{
    /**
     * Get the value of a key.
     *
     * @param string $key The key to retrieve.
     * @return mixed The value associated with the key, or null if not found.
     */
    public function get(string $key);

    /**
     * Set the value of a key.
     *
     * @param string $key The key to set.
     * @param mixed $value The value to associate with the key.
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Check if a key exists.
     *
     * @param string $key The key to check.
     * @return bool True if the key exists, false otherwise.
     */
    public function has(string $key): bool;

    public function getAll(): array;
    /**
     * Remove a key from the store.
     *
     * @param string $key The key to remove.
     * @return void
     */
    public function remove(string $key): void;

    /**
     * Clear all keys from the store.
     *
     * @return void
     */
    public function clear(): void;

    /**
     * Get the number of keys in the store.
     *
     * @return int The number of keys.
     */
    public function count(): int;

    /**
     * Set multiple keys at once.
     *
     * @param array $data An associative array of key-value pairs to set.
     * @return void
     */
    public function setAll(array $data): void;
}
?>