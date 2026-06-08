<?php
require_once 'config/db.php';
require_once 'database/helpers.php';
require_once 'data.php';
include 'includes/amenity-icons.php';

$destId  = $_GET['dest'] ?? '';
$region  = $_GET['region'] ?? '';
$budget  = $_GET['budget'] ?? '';
$sortBy  = $_GET['sort'] ?? 'recommended';

$dest = $destId ? getDestById($destId) : null;

// filtering
$filteredDests = $destinations;

if ($region) {
    $filteredDests = array_filter(
        $filteredDests,
        fn($d) => strtolower($d['region']) === strtolower($region)
    );
}

if ($budget === 'low') {
    $filteredDests = array_filter($filteredDests, fn($d) => $d['price'] < 5000);
}

if ($budget === 'mid') {
    $filteredDests = array_filter($filteredDests, fn($d) => $d['price'] >= 5000 && $d['price'] <= 7500);
}

if ($budget === 'high') {
    $filteredDests = array_filter($filteredDests, fn($d) => $d['price'] > 7500);
}

$pageTitle = $dest
    ? 'Hotels in ' . $dest['name'] . ' — LakbayLokal'
    : 'All Destinations — LakbayLokal';

$activePage = 'destinations';
$rootPath = '';

include 'includes/header.php';
include 'views/destination.view.php';
include 'includes/footer.php';