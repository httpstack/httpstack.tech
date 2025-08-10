<?php
namespace HttpStack\Contracts;
interface DatasourceInterface {
    
    public function fetch(string|array|null $key): array;
    public function save(array $data):void;

    /* * Delete a variable from the model.
      * This method should be implemented in subclasses to provide
      * the specific logic for deleting a variable.
      * The matching field or key in the datasource is unchaged until
      * the save() method is called.
     * @param mixed $var The variable to delete.
     * @return void
     */
    public function delete(mixed $var):void;
}
?>