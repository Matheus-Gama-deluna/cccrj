<?php
// tests/ArchiveApiTest.php

require_once __DIR__ . '/../utils/JsonCache.php';

// Helper function to run a test
function runTest($name, $getParams, $expectedCount) {
    echo "Running test: $name\n";

    // Clear the cache
    $cache = new JsonCache();
    $cache->clearCache();

    // Set the GET parameters
    $_GET = $getParams;

    // Capture the output of the script
    ob_start();
    include __DIR__ . '/../api/json/archive/list.php';
    $output = ob_get_clean();

    // Decode the JSON response
    $response = json_decode($output, true);

    // Check if the response is valid
    if (!$response || !$response['success']) {
        echo "Test failed: Invalid JSON response\n";
        return;
    }

    // Check the number of items returned
    if (count($response['data']) === $expectedCount) {
        echo "Test passed\n";
    } else {
        echo "Test failed: Expected $expectedCount items, but got " . count($response['data']) . "\n";
    }
}

// --- Test Cases ---

// Test case 1: No filters (should return all active items)
runTest('No filters', ['limit' => 10, 'offset' => 0], 6);

// Test case 2: Search filter
runTest('Search filter', ['search' => 'histórico', 'limit' => 10, 'offset' => 0], 3);

// Test case 3: Type filter
runTest('Type filter', ['type' => 'Documento', 'limit' => 10, 'offset' => 0], 3);

// Test case 4: Year filter
runTest('Year filter', ['year' => '2023', 'limit' => 10, 'offset' => 0], 4);

// Test case 5: Combined filters
runTest('Combined filters', ['type' => 'Foto', 'year' => '2023', 'limit' => 10, 'offset' => 0], 1);

// Test case 6: No results
runTest('No results', ['search' => 'nonexistent', 'limit' => 10, 'offset' => 0], 0);

// Test case 7: Inactive items should not be returned
runTest('Inactive items', ['search' => 'inativo', 'limit' => 10, 'offset' => 0], 0);

?>