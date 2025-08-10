<?php
namespace HttpStack\App\Models;

use Stringable;
use HttpStack\Model\AbstractModel;
use HttpStack\Datasource\Contracts\CRUD;

/**
 * PageModel class.
 * Represents data for a specific database table, providing structured access
 * and interaction via a CRUD datasource.
 */
class PageModel extends AbstractModel implements Stringable
{
    protected string $tableName; // Stores the name of the database table this model represents.

    /**
     * Constructor for PageModel.
     *
     * @param CRUD $datasource The datasource for this model (e.g., DBDatasource).
     * @param string $tableName The name of the database table this model represents.
     * @param array $initialData Optional initial data to populate the model with.
     * @throws \InvalidArgumentException If the table name is not provided or is empty.
     */
    public function __construct(CRUD $datasource, array $initialData = [])
    {
        // Call the parent constructor from AbstractModel, passing the datasource and initial data.
        parent::__construct($datasource, $initialData);

        // Validate that a table name is provided.

    }


    /**
     * String representation of the PageModel.
     * Returns a string indicating the table it represents and its ID if available.
     *
     * @return string
     */
    public function __toString(): string
    {
        // Access the table name via getTableName() and the ID via the inherited get() method.
        return "PageModel: Table '" . $this->getTableName() . "' (ID: " . ($this->get('id') ?? 'N/A') . ")";
    }

    // You can add specific methods here for common page operations,
    // which would internally use $this->datasource->read(), create(), update(), delete().
    // For example:
    /*
    public function loadBySlug(string $slug): ?array
    {
        $data = $this->datasource->read(['slug' => $slug]);
        if (!empty($data)) {
            $this->setAll($data[0]); // Assuming slug is unique and returns one record
            return $data[0];
        }
        return null;
    }

    public function savePage(array $pageData): mixed
    {
        if (isset($pageData['id']) && !empty($pageData['id'])) {
            // Update existing page
            $id = $pageData['id'];
            unset($pageData['id']); // Remove ID from data payload
            return $this->datasource->update(['data' => $pageData, 'where' => ['id' => $id]]);
        } else {
            // Create new page
            return $this->datasource->create($pageData);
        }
    }
    */
}
