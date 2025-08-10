<?php
namespace HttpStack\Model\Concrete;
use HttpStack\Model\Contracts\Attributes;

class KeyStore implements Attributes
{
    /**
     * @var array The current attributes data.
     */
    private array $data = [];

    /**
     * @var array A snapshot of the attributes when they were last marked as clean.
     */
    private array $originalData = [];

    /**
     * Constructor.
     * Initializes attributes with provided data and marks them as clean.
     *
     * @param array $initialData Initial data to populate the attributes.
     */
    public function __construct(array $initialData = [])
    {
        $this->fill($initialData); // Use fill to set initial data
        $this->markAsClean();      // Mark as clean after initial population
    }

    /**
     * Retrieves the value associated with the specified key.
     *
     * @param string $key The key to retrieve the value for.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The value associated with the key, or the default value if not found.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Sets the value for the specified key.
     * This method updates the internal data array. The dirty state is determined
     * by comparing `$data` with `$originalData` in `isDirty()`.
     *
     * @param string $key The key to set the value for.
     * @param mixed $value The value to set.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        // No need for explicit dirty flags here, as isDirty() compares the full arrays.
        // This simple assignment is sufficient.
        $this->data[$key] = $value;
    }

    /**
     * Checks if a key exists.
     *
     * @param string $key The key to check for existence.
     * @return bool True if the key exists, false otherwise.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Retrieves all key-value pairs.
     *
     * @return array An associative array of all key-value pairs.
     */
    public function getAll(): array
    {
        return $this->data;
    }

    /**
     * Removes the value associated with the specified key.
     *
     * @param string $key The key to remove.
     * @return void
     */
    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }
    }

    /**
     * Clears all key-value pairs.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->data = [];
    }

    /**
     * Sets multiple key-value pairs.
     *
     * @param array $data An associative array of key-value pairs to set.
     * @return void
     */
    public function setAll(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Fills the attributes, replacing existing ones.
     * This is a helper for initial loading or full replacement.
     *
     * @param array $data
     * @return void
     */
    public function fill(array $data): void
    {
        $this->data = $data;
    }

    /**
     * Marks the current state of attributes as "clean" by taking a snapshot.
     *
     * @return void
     */
    public function markAsClean(): void
    {
        $this->originalData = $this->data;
    }

    /**
     * Checks if any attributes have been changed since the last call to markAsClean().
     *
     * @return bool True if there are changes, false otherwise.
     */
    public function isDirty(): bool
    {
        // Deep comparison of current data vs. original snapshot
        return $this->data !== $this->originalData;
    }

    /**
     * Retrieves an associative array of only the attributes that have changed
     * since the last call to markAsClean().
     *
     * @return array An array of dirty attributes.
     */
    public function getDirtyAttributes(): array
    {
        $dirty = [];

        // Check for modified or new attributes
        foreach ($this->data as $key => $value) {
            if (!array_key_exists($key, $this->originalData) || $this->originalData[$key] !== $value) {
                $dirty[$key] = $value;
            }
        }

        // You might also want to track removed attributes if your datasource
        // needs to know about them explicitly for update/delete operations.
        // For example:
        // foreach ($this->originalData as $key => $value) {
        //     if (!array_key_exists($key, $this->data)) {
        //         $dirty[$key] = null; // Or some other marker for deletion
        //     }
        // }

        return $dirty;
    }

    public function count(): int
    {
        return count($this->data);
    }   
}
?>