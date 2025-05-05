<?php
require_once __DIR__ . '/../src/polors.php';

$polor = new polors();

// Sample input: some colors provided, others left null to be generated
$colors = [
    '#f5f5f5', // light
    null,      // tertiary
    null,      // primary
    '#3498db', // secondary
    null,      // dark
    '#2ecc71', // success
    '#f1c40f', // warning
    '#e74c3c'  // danger
];

$palette = $polor->generate_colors($colors);

if ($palette) {
    echo "<h2>Generated Color Palette:</h2><ul>";
    $labels = ['light', 'tertiary', 'primary', 'secondary', 'dark', 'success', 'warning', 'danger'];
    foreach ($palette as $i => $color) {
        echo "<li><strong>{$labels[$i]}</strong>: <span style='display:inline-block;width:20px;height:20px;background:{$color};margin-right:5px;'></span> {$color}</li>";
    }
    echo "</ul>";
} else {
    echo "Invalid color input. Please provide exactly 8 colors.";
}
