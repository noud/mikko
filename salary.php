<?php
require_once('generateCSVs.php');

try {
    $csv = new generateCSVs();
    $file = $csv->generate();
	echo 'File is saved with name ' . $file ;
} catch (\Exception $e) {
	echo $e->getMessage();
}