<?php
namespace HttpStack\Model\Contracts;

interface AttributeState {

    /**
     * Requires:
     * @var array $states 
     * @var array $attributes 
     */


     
    /**
     * Pushes a new state onto the stack.
     *
     * @param string $restorePoint The name of the restore point.
     * @return mixed The result of the operation.
     */
    public function pushState(string $restorePoint): mixed; 
    /**
     * Pops the last state from the stack.
     *
     * @return array The last state that was popped.
     */   
    public function popState():array;
    /**
     * Retrieves a specific state from the stack.
     *
     * @param string $restorePoint The name of the restore point to retrieve.
     * @return mixed The state associated with the restore point.
     */
    public function getState(string $restorePoint): mixed;
}
?>