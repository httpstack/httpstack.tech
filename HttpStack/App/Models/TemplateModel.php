<?php
namespace HttpStack\App\Models;

use Stringable;
use HttpStack\Model\AbstractModel; 
use HttpStack\Datasource\Contracts\CRUD;
//Import your specific datasource

use HttpStack\App\Datasources\FS\JsonDirectory; // For __toString() if desired

/**
 * TemplateModel class.
 * Represents the data specific to a template, providing structured access
 * to common template data elements like assets and links.
 */
class TemplateModel extends AbstractModel implements Stringable // Added Stringable for demonstration
{
    protected string $templateName;

    /**
     * Constructor for TemplateModel.
     *
     * @param CRUD $datasource The datasource for this model.
     * @param string $templateName The logical name or identifier for this template's data.
     * @param array $initialData Optional initial data for the model.
     */
    public function __construct(CRUD $datasource, array $initialData = [])
    {
        parent::__construct($datasource, $initialData);
        // 1.)  Pass the array of assets you want URLS for to the fileloader
        //put generated links array into bindAssets
        $temp = [];
        $temp['links'] = $this->getLinks("main");
        foreach($this->getVariables() as $key => $value){
            $temp[$key] = $value;
        }
        
        $this->setAll($temp);
    }
    public function getVariables(){
        return $this->get('base.json');
    }
 
    public function getLinks($which){
        return $this->getAll()['links.json'][$which];
    }


    /**
     * String representation of the TemplateModel.
     *
     * @return string
     */
    protected function processArray($a){
        $str = "";
        foreach($a as $k => $v){
            if(is_array($v)){
                $str .= $this->processArray($v);
            }else{
                $str .= "Key: $k Value: $v \n<br/>";
            }
        }
        return $str;
    }
    public function __toString(): string
    {
        return $this->processArray($this->getAll());
    }
}