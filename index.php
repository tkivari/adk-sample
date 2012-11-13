<?php

/**
 * Adk candidate programming project
 *
 * Instructions:
 *     1. Use web service information located in dao.php to retrieve results from the web service
 *     2. Populate models (model.php) with results from web service call
 *     3. Display results returned from CandidateProjectDao::getTestData() in view.phtml
 */

// Required files for this project
require_once 'dao.php';
require_once 'model.php';

// Get modeled data from DAO
$project_model = new \tykiv\CandidateProjectDao();

try {
    $project_model->getTestData();
    require 'view.phtml';
} catch (Exception $e) {
    print("There was an error: " . $e->getMessage());
}
