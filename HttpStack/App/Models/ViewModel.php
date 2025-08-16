<?php

namespace HttpStack\App\Models;

use Stringable;
use HttpStack\Model\AbstractModel;
use HttpStack\Datasource\Contracts\CRUD;

/**
 * ViewModel class.
 * Represents data for a specific database table, providing structured access
 * and interaction via a CRUD datasource.
 */
class ViewModel extends AbstractModel
{
    protected string $tableName; // Stores the name of the database table this model represents.

    /**
     * Constructor for ViewModel.
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
}
