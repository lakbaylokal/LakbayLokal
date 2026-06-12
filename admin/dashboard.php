<?php
session_start();

// 1. Authentication Check (Pansamantalang naka-bypass o naka-check depende sa login mo)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    // Para sa testing, kung wala pang session user, i-simulate natin para hindi ka ma-kickout:
    $_SESSION['user'] = ['role' => 'admin', 'name' => 'Admin Tester'];
}

// 2. MAGLAGAY NG DEFAULT DATA KUNG WALANG LAMAN ANG SESSION
if (!isset($_SESSION['mock_destinations'])) {
    $_SESSION['mock_destinations'] = [
        ['id' => 1, 'name' => 'Boracay Island', 'description' => 'Famous for its powdery white sand beaches and crystal clear waters.', 'price' => 5500.00],
        ['id' => 2, 'name' => 'El Nido, Palawan', 'description' => 'Stunning limestone cliffs, hidden lagoons, and rich marine life.', 'price' => 7500.00],
        ['id' => 3, 'name' => 'Batanes', 'description' => 'Breathtaking rolling hills, traditional stone houses, and peaceful scenery.', 'price' => 12000.00]
    ];
    $_SESSION['next_id'] = 4; // Para sa susunod na idadagdag
}

$message = '';

// 3. CRUD LOGIC (CREATE AT UPDATE) VIA POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // --- CREATE ACTION ---
    if ($_POST['action'] === 'create_destination') {
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        
        if (!empty($name) && !empty($price)) {
            $new_dest = [
                'id' => $_SESSION['next_id']++,
                'name' => $name,
                'description' => $description,
                'price' => $price
            ];
            $_SESSION['mock_destinations'][] = $new_dest;
            $message = "Destination successfully added to Session!";
        }
    }
    
    // --- UPDATE ACTION ---
    if ($_POST['action'] === 'update_destination') {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        
        foreach ($_SESSION['mock_destinations'] as $key => $dest) {
            if ($dest['id'] === $id) {
                $_SESSION['mock_destinations'][$key]['name'] = $name;
                $_SESSION['mock_destinations'][$key]['description'] = $description;
                $_SESSION['mock_destinations'][$key]['price'] = $price;
                $message = "Destination successfully updated in Session!";
                break;
            }
        }
    }
}

// 4. CRUD LOGIC (DELETE) VIA GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $page_type = $_GET['page'];
    
    if ($page_type === 'destinations') {
        foreach ($_SESSION['mock_destinations'] as $key => $dest) {
            if ($dest['id'] === $id) {
                unset($_SESSION['mock_destinations'][$key]);
                // I-reindex ang array para walang bakanteng susi
                $_SESSION['mock_destinations'] = array_values($_SESSION['mock_destinations']);
                header("Location: ?page=destinations&msg=Deleted from Session successfully");
                exit;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LakbayLokal | Admin Panel (No DB Mockup)</title>
  <link rel="stylesheet" href="assets/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
  
  <style>
    /* UI design elements para maging malinis tingnan */
    .crud-table { width: 100%; border-collapse: collapse; margin-top: 1rem; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    .crud-table th, .crud-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
    .crud-table th { background-color: #f8f9fa; font-weight: 600; color: #333; }
    .btn { padding: 8px 14px; border-radius: 6px; border: none; cursor: pointer; text-decoration: none; font-size: 14px; display: inline-block; font-family: 'DM Sans', sans-serif; }
    .btn-add { background: #E8905D; color: white; font-weight: bold; }
    .btn-edit { background: #4A90E2; color: white; margin-right: 5px; }
    .btn-delete { background: #D9534F; color: white; }
    .alert { padding: 12px; background: #E2F0D9; color: #385723; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #333; }
    .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-family: 'DM Sans', sans-serif; }
    .badge-mock { background: #6c757d; color: white; padding: 2px 6px; border-radius: 4px; font-size: 11px; font-weight: normal; }
  </style>
</head>

<body>
  <div class="admin-sidebar">
    <div class="sidebar-brand">
      <h3>Lakbay<span>Lokal</span></h3>
    </div>
    <ul class="sidebar-menu">
      <li><a href="?page=dashboard" class="<?php echo (!isset($_GET['page']) || $_GET['page'] == 'dashboard') ? 'active' : ''; ?>">📊 Dashboard</a></li>
      <li><a href="?page=destinations" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'destinations') ? 'active' : ''; ?>">🗺️ Destinations</a></li>
      <li><a href="?page=hotels" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'hotels') ? 'active' : ''; ?>">🏨 Hotels</a></li>
      <li><a href="?page=itineraries" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'itineraries') ? 'active' : ''; ?>">🎯 Itineraries</a></li>
      <li><a href="?page=bookings" class="<?php echo (isset($_GET['page']) && $_GET['page'] == 'bookings') ? 'active' : ''; ?>">🧾 Bookings List</a></li>
      <li style="margin-top: auto;"><a href="../index.php" style="color: #E8905D;">🚪 Exit Admin</a></li>
    </ul>
  </div>

  <main class="main-content">
    <div class="page" id="page-admin">
      
      <?php if (!empty($message)): ?>
          <div class="alert">📢 <?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['msg'])): ?>
          <div class="alert">📢 <?php echo htmlspecialchars($_GET['msg']); ?></div>
      <?php endif; ?>
      
      <?php
      $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
      $action = isset($_GET['action']) ? $_GET['action'] : 'list';

      switch ($page) {
          
          case 'dashboard':
              // Pagkuha ng bilang mula sa Session arrays
              $countBookings = 0; // Mock data for bookings counter
              $totalRevenue = 0.00;
              $countDestinations = count($_SESSION['mock_destinations']);
              ?>
              <div class="dash-header">
                <h1>Admin Dashboard <span class="badge-mock">In-Memory Mode</span></h1>
                <p>Welcome back! App is currently running on temporary session storage (No database needed).</p>
              </div>

              <div class="dash-stats">
                <div class="dash-stat">
                  <div class="dash-stat-num"><?php echo $countBookings; ?></div>
                  <div class="dash-stat-label">Global Bookings</div>
                </div>
                <div class="dash-stat">
                  <div class="dash-stat-num">₱<?php echo number_format($totalRevenue, 2); ?></div>
                  <div class="dash-stat-label">Total Revenue</div>
                </div>
                <div class="dash-stat">
                  <div class="dash-stat-num"><?php echo $countDestinations; ?></div>
                  <div class="dash-stat-label">Active Destinations</div>
                </div>
              </div>
              <?php
              break;

          case 'destinations':
              ?>
              <div class="dash-header">
                <h1>🗺️ Manage Destinations <span class="badge-mock">Session CRUD</span></h1>
                <p>Mag-add, mag-edit, o mag-delete nang walang database connection.</p>
              </div>
              
              <div class="dash-content">
              <?php if ($action === 'add'): ?>
                  <h2>Add New Destination</h2>
                  <form method="POST" action="?page=destinations">
                      <input type="hidden" name="action" value="create_destination">
                      <div class="form-group">
                          <label>Destination Name</label>
                          <input type="text" name="name" placeholder="Halimbawa: Siargao" required>
                      </div>
                      <div class="form-group">
                          <label>Description</label>
                          <textarea name="description" rows="4" placeholder="Isulat ang detalye dito..."></textarea>
                      </div>
                      <div class="form-group">
                          <label>Price (₱)</label>
                          <input type="number" step="0.01" name="price" placeholder="4500.00" required>
                      </div>
                      <button type="submit" class="btn btn-add">Save Destination</button>
                      <a href="?page=destinations" class="btn" style="background:#ccc; color: black;">Cancel</a>
                  </form>

              <?php elseif ($action === 'edit' && isset($_GET['id'])): 
                  // B. RETRIEVE PARA SA UPDATE
                  $id = intval($_GET['id']);
                  $target = null;
                  foreach ($_SESSION['mock_destinations'] as $dest) {
                      if ($dest['id'] === $id) {
                          $target = $dest;
                          break;
                      }
                  }
                  
                  if ($target):
                  ?>
                  <h2>Edit Destination (ID: <?php echo $target['id']; ?>)</h2>
                  <form method="POST" action="?page=destinations">
                      <input type="hidden" name="action" value="update_destination">
                      <input type="hidden" name="id" value="<?php echo $target['id']; ?>">
                      <div class="form-group">
                          <label>Destination Name</label>
                          <input type="text" name="name" value="<?php echo htmlspecialchars($target['name']); ?>" required>
                      </div>
                      <div class="form-group">
                          <label>Description</label>
                          <textarea name="description" rows="4"><?php echo htmlspecialchars($target['description']); ?></textarea>
                      </div>
                      <div class="form-group">
                          <label>Price (₱)</label>
                          <input type="number" step="0.01" name="price" value="<?php echo $target['price']; ?>" required>
                      </div>
                      <button type="submit" class="btn btn-add">Update Destination</button>
                      <a href="?page=destinations" class="btn" style="background:#ccc; color: black;">Cancel</a>
                  </form>
                  <?php else: echo "<p>Destination not found in current session.</p>"; endif; ?>

              <?php else: ?>
                  <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; align-items: center;">
                      <h2 class="dash-bookings-title">All Travel Packages (Live Mock Table)</h2>
                      <a href="?page=destinations&action=add" class="btn btn-add">+ Add Destination</a>
                  </div>
                  
                  <?php if (count($_SESSION['mock_destinations']) > 0): ?>
                      <table class="crud-table">
                          <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Name</th>
                                  <th>Description</th>
                                  <th>Price</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php foreach ($_SESSION['mock_destinations'] as $dest): ?>
                              <tr>
                                  <td><?php echo $dest['id']; ?></td>
                                  <td><strong><?php echo htmlspecialchars($dest['name']); ?></strong></td>
                                  <td><?php echo htmlspecialchars(substr($dest['description'], 0, 70)) . '...'; ?></td>
                                  <td>₱<?php echo number_format($dest['price'], 2); ?></td>
                                  <td>
                                      <a href="?page=destinations&action=edit&id=<?php echo $dest['id']; ?>" class="btn btn-edit">Edit</a>
                                      <a href="?page=destinations&action=delete&id=<?php echo $dest['id']; ?>" class="btn btn-delete" onclick="return confirm('Sigurado ka bang buburahin mo ito sa session storage?');">Delete</a>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  <?php else: ?>
                      <div style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 2rem; text-align: center; color: #777;">
                          <p>Ubos na ang data sa session array mo! Mag-add ka ulit gamit ang button sa itaas.</p>
                      </div>
                  <?php endif; ?>
              <?php endif; ?>
              </div>
              <?php
              break;

          case 'hotels':
              echo '<div class="dash-header"><h1>🏨 Partner Hotels</h1></div><p style="color:gray;">Naka-focus ang temporary session mockup sa Destinations tab.</p>';
              break;

          case 'itineraries':
              echo '<div class="dash-header"><h1>🎯 Activities</h1></div><p style="color:gray;">Maaari mo ring gayahin ang coding logic ng destinations kung gusto mo ng interactive simulations para dito.</p>';
              break;

          case 'bookings':
              echo '<div class="dash-header"><h1>🧾 Master Booking Records</h1></div><p style="color:gray;">Naka-pending ang temporary structure nito.</p>';
              break;
      }
      ?>

    </div>
  </main>
</body>
</html>