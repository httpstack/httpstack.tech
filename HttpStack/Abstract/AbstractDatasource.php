<?php 
namespace HttpStack\Abstract;
/**
 * DatasourceInterface defines the contract for data sources in the HttpStack framework.
 * It provides methods for fetching and saving data, as well as deleting variables.
 * 
 * @package HttpStack\Contracts
 */
abstract class AbstractDatasource{
    protected bool $readOnly = true;
    
    public function __construct() {
        
    }
    public function setReadOnly(bool $readOnly): void {
        $this->readOnly = $readOnly;
    }

    public function isReadOnly(): bool {
        return $this->readOnly ?? false;
    }
}
?>