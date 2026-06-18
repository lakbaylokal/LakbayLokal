<?php
require_once 'config/db.php';
require_once 'database/helpers.php';
include 'includes/amenity-icons.php';

$destId = $_GET['dest']   ?? '';
$region = $_GET['region'] ?? '';
$budget = $_GET['budget'] ?? '';
$sortBy = $_GET['sort']   ?? 'recommended';

// ── If viewing a single destination ──
$dest = $destId ? getDestById($conn, $destId) : null;

// ── All-destinations listing with DB-level filtering ──
if (!$dest) {

    // Build WHERE clauses for budget filter
    $where  = ['1=1'];
    $params = [];
    $types  = '';

    if ($region) {
        $where[]  = 'd.region = ?';
        $params[] = $region;
        $types   .= 's';
    }

    if ($budget === 'low') {
        $where[] = 'd.price < 5000';
    } elseif ($budget === 'mid') {
        $where[] = 'd.price BETWEEN 5000 AND 7500';
    } elseif ($budget === 'high') {
        $where[] = 'd.price > 7500';
    }

    $sql = "
        SELECT d.id, d.name, d.region, d.emoji, d.tagline,
               d.price, d.price_from, d.gradient_bg AS gradient,
               COUNT(DISTINCT h.id) AS hotel_count
        FROM destinations d
        LEFT JOIN hotels h ON h.destination_id = d.id
        WHERE " . implode(' AND ', $where) . "
        GROUP BY d.id
        ORDER BY d.name
    ";

    if ($types) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }

    $filteredDests = [];
    while ($row = $result->fetch_assoc()) {
        $filteredDests[] = $row;
    }

    // Fetch all destinations for the sidebar dropdown
    $destinations = getAllDestinations($conn);

} else {
    // Single-dest view: sort hotels in PHP (small array — fine)
    $destinations = getAllDestinations($conn); // needed for sidebar dropdown
}

$pageTitle  = $dest
    ? 'Hotels in ' . $dest['name'] . ' — LakbayLokal'
    : 'All Destinations — LakbayLokal';
$activePage = 'destinations';
$rootPath   = '';

include 'includes/header.php';
include 'views/destination.view.php';
include 'includes/footer.php';