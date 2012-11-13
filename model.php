<?php

namespace tykiv;

/* This class provides the added benefit of being capable of storing data from a single JSON response into 
 * multiple different local objects, even objects with different properties.
 * 
 * The goal is to mimic casting the StdObject originating from JSON as an instance of the local object.
 * 
 */
abstract class JsonObject {
    
    protected $errors = array();    
    
    /*
     * @return void
     * takes an object with the same properties as the CandidateProjectModel class
     * and casts it to an instance of this class.
     * 
     * There might be cleaner ways to do this without explicity doing $this->accountId = $model->accountId... etc.
     *
     * @param StdClass $model
     */
    protected function instance($model, $class_vars)
    {
        // for each property in $model, if this class has a property with the same name,
        // add it.
        foreach ($model as $k => $v) {
            if (array_key_exists($k, $class_vars)) {        
                $this->$k = $v;
            } else { $this->errors[] = "Property does not exist: " . $k . "... Igoring"; }
        }
    }
    
    public function getErrors() {
        return $this->errors;
    }
}

/**
 * Simple model class for data returned from candidate project web service
 *
 * Build this model out in the most appropriate way you see fit. Treat it as
 * a real-world component being added to our system.
 */
class CandidateProjectModel extends \tykiv\JsonObject {
    
    protected $accountId;
    protected $accountName;
    protected $revenue;
    protected $clicks;
    
    public function __construct($model) {
        $class = get_class($this);
        $class_vars = get_class_vars($class);
        $this->instance($model,$class_vars);
    }
    
    // define getters (setters are not needed):
    
    public function getAccountId() {
        return $this->accountId;
    }
    
    public function getAccountName() {
        return $this->accountName;
    }
    
    public function getClicks() {
        return $this->clicks;
    }
    
    public function getRevenue() {
        return $this->revenue;
    }
}