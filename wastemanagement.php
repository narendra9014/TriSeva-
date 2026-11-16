<?php
session_start();

// 1. Include Database Connection from db.php
include 'db.php';

if (!isset($conn) || $conn->connect_error) {
    die("Database Connection Error: " . ($conn->connect_error ?? "Connection object \$conn not found in db.php"));
}

// 2. Dynamic Logged-In User Retrieval
$fullname = $_SESSION['fullname'] ?? 'Guest User';
$user_id  = $_SESSION['user_id'] ?? 0;

// 3. Form Submission Handling (Report Waste)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action_type = $_POST['action_type'] ?? '';

    if ($action_type === 'report_waste') {
        $location      = trim($_POST['location'] ?? '');
        $landmark      = trim($_POST['landmark'] ?? '');
        $wasteType     = trim($_POST['wasteType'] ?? '');
        $severity      = trim($_POST['severity'] ?? '');
        $description   = trim($_POST['description'] ?? '');
        $reporter_name = $fullname; // Stores session user's name

        // Photo Upload Handling
        $imagePath = "";
        if (isset($_FILES['wastePhoto']) && $_FILES['wastePhoto']['error'] === 0) {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName       = time() . '_' . basename($_FILES['wastePhoto']['name']);
            $targetFilePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['wastePhoto']['tmp_name'], $targetFilePath)) {
                $imagePath = $targetFilePath;
            }
        }

        // Fixed SQL query with correct database column names
        $sql = "INSERT INTO waste_reports (user_id, location, landmark, waste_type, severity, description, status, reported_date, reporter_name) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("issssss", $user_id, $location, $landmark, $wasteType, $severity, $description, $reporter_name);
        
        if ($stmt->execute()) {
            $stmt->close();
            // Redirect back to avoid duplicate POST submissions on refresh
            header("Location: wastemanagement.php");
            exit();
        } else {
            die("Database Insert Error: " . $stmt->error);
        }
    }
}

// 4. Fetch All Active Waste Reports from Database
$reportsQuery  = "SELECT * FROM waste_reports ORDER BY id DESC";
$reportsResult = $conn->query($reportsQuery);

// Fetch Total Report Count for Header Card
$totalReportsCount = 0;
if ($reportsResult) {
    $totalReportsCount = $reportsResult->num_rows;
}

// Fetch Cleaned Count
$cleanedCount = 0;
$cleanedQuery = $conn->query("SELECT COUNT(*) AS total FROM waste_reports WHERE status = 'cleaned'");
if ($cleanedQuery) {
    $cleanedRow = $cleanedQuery->fetch_assoc();
    $cleanedCount = $cleanedRow['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Smart Waste Management - TriSeva</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .eco-glass {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
    }
    .eco-gradient {
      background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #10b981 100%);
    }
    .card-glow:hover {
      box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.1), 0 8px 10px -6px rgba(16, 185, 129, 0.1);
    }
  </style>

</head>

<body class="bg-emerald-50/40 text-slate-800 min-h-screen flex flex-col antialiased">

  <!-- Top Navigation Header -->
  <header class="bg-slate-900 border-b border-emerald-900/40 sticky top-0 z-50 shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <!-- Logo & Branding -->
        <div class="flex items-center space-x-3">
          
          <span class="text-2xl font-black tracking-tight text-white">Tri<span class="text-emerald-400">Seva</span></span>
        </div>

        <!-- User Info & Controls (Dynamic Session User) -->
        <div class="flex items-center space-x-6">
          <div class="hidden sm:flex items-center space-x-2 bg-slate-800/80 px-3 py-1.5 rounded-full border border-slate-700">
            <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
            <span class="text-xs font-medium text-slate-300">Welcome, <strong class="text-white font-semibold"> <b id="loggedInUserName"><?php echo htmlspecialchars($fullname); ?></b> </strong></span>
          </div>

          <nav class="flex items-center space-x-3">
            <a href="home.php" class="text-slate-300 hover:text-white px-3 py-2 rounded-lg text-sm font-semibold transition-all hover:bg-slate-800 flex items-center gap-1.5">
              <i data-lucide="home" class="w-4 h-4"></i> Home
            </a>
            <a href="logout.php" class="bg-rose-600/90 hover:bg-rose-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-sm shadow-rose-900/20 flex items-center gap-1.5">
              <i data-lucide="log-out" class="w-4 h-4"></i> Logout
            </a>
          </nav>
        </div>

      </div>
    </div>
  </header>

  <!-- Main Content Container -->
  <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

      <!-- Hero Banner Section -->
      <div class="eco-gradient text-white rounded-3xl p-8 sm:p-10 mb-8 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
          <i data-lucide="trees" class="w-96 h-96"></i>
        </div>

        <div class="relative z-10 max-w-3xl">
          <div class="inline-flex items-center gap-2 bg-emerald-400/20 border border-emerald-300/30 px-3 py-1 rounded-full text-xs font-semibold text-emerald-200 backdrop-blur-sm mb-4">
            <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> Eco-Friendly Initiative
          </div>
          <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight mb-3">Smart Waste Management</h1>
          <p class="text-base sm:text-lg text-emerald-100 font-normal leading-relaxed">
            Report local dumping points with photos, request convenient recycling pickups, and earn green rewards to power a sustainable environment.
          </p>
        </div>
      </div>

      <!-- Quick Stats Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm card-glow transition-all">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Reports</span>
            <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600"><i data-lucide="file-text" class="w-5 h-5"></i></div>
          </div>
          <div class="text-3xl font-extrabold text-emerald-600"><?php echo $totalReportsCount; ?></div>
          <div class="text-xs text-slate-500 mt-1">Submitted so far</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm card-glow transition-all">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Cleaned</span>
            <div class="p-2 bg-teal-50 rounded-lg text-teal-600"><i data-lucide="check-circle-2" class="w-5 h-5"></i></div>
          </div>
          <div class="text-3xl font-extrabold text-teal-600"><?php echo $cleanedCount; ?></div>
          <div class="text-xs text-slate-500 mt-1">Areas restored</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm card-glow transition-all">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pickups</span>
            <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600"><i data-lucide="truck" class="w-5 h-5"></i></div>
          </div>
          <div class="text-3xl font-extrabold text-indigo-600">0</div>
          <div class="text-xs text-slate-500 mt-1">Recycle pickups</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm card-glow transition-all">
          <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Volunteers</span>
            <div class="p-2 bg-amber-50 rounded-lg text-amber-600"><i data-lucide="users" class="w-5 h-5"></i></div>
          </div>
          <div class="text-3xl font-extrabold text-amber-600">850</div>
          <div class="text-xs text-slate-500 mt-1">Active heroes</div>
        </div>
      </div>

      <!-- Main Action Navigation & Content Box -->
      <div class="bg-white rounded-3xl shadow-xl border border-emerald-100/60 overflow-hidden mb-12">
        
        <!-- Navigation Tabs -->
        <div class="flex flex-wrap sm:flex-nowrap border-b border-slate-100 bg-slate-50/50 p-2 gap-2">
          <button id="tab-btn-reports" onclick="setActiveTab('reports')" class="flex-1 id-tab-btn py-3.5 px-5 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 bg-white text-emerald-700 shadow-sm border border-emerald-100">
            <i data-lucide="list-filter" class="w-4 h-4"></i> Active Reports
          </button>
          <button id="tab-btn-report" onclick="setActiveTab('report')" class="flex-1 id-tab-btn py-3.5 px-5 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 text-slate-600 hover:bg-white/60">
            <i data-lucide="camera" class="w-4 h-4"></i> Report Waste
          </button>
          <button id="tab-btn-pickup" onclick="setActiveTab('pickup')" class="flex-1 id-tab-btn py-3.5 px-5 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 text-slate-600 hover:bg-white/60">
            <i data-lucide="recycle" class="w-4 h-4"></i> Request Pickup
          </button>
        </div>

        <div class="p-6 sm:p-10">

          <!-- TAB 1: ACTIVE REPORTS VIEW -->
          <div id="tab-view-reports" class="tab-content">
            <div id="mock-reports-container" class="space-y-4">

              <?php if ($reportsResult && $reportsResult->num_rows > 0): ?>
                <?php while ($row = $reportsResult->fetch_assoc()): ?>
                  <div class="border border-emerald-100 rounded-2xl p-6 hover:shadow-lg transition-all bg-emerald-50/20">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                      <div class="flex items-start space-x-4">
                        <div class="p-3 bg-emerald-100 text-emerald-700 rounded-2xl flex-shrink-0">
                          <i data-lucide="recycle" class="w-7 h-7"></i>
                        </div>
                        <div>
                          <div class="flex items-center gap-3 mb-1">
                            <h3 class="text-lg font-bold text-slate-900"><?php echo htmlspecialchars($row['location'] ?? ''); ?></h3>
                            <span class="px-3 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 uppercase tracking-wide border border-amber-200">
                              <?php echo htmlspecialchars($row['status'] ?? 'pending'); ?>
                            </span>
                          </div>
                          <div class="flex flex-wrap items-center gap-2 text-xs mb-2">
                            <span class="px-2.5 py-1 rounded-md font-bold bg-slate-200 text-slate-700 uppercase">
                              <?php echo htmlspecialchars($row['severity'] ?? 'LOW'); ?> PRIORITY
                            </span>
                            <span class="text-slate-600 bg-emerald-100/60 px-2.5 py-1 rounded-md font-medium">
                              Type: <strong class="text-slate-800 capitalize"><?php echo htmlspecialchars($row['waste_type'] ?? ''); ?></strong>
                            </span>
                            <?php if (!empty($row['landmark'])): ?>
                            <span class="text-slate-500 flex items-center gap-1">
                              <i data-lucide="map-pin" class="w-3.5 h-3.5"></i> Landmark: <?php echo htmlspecialchars($row['landmark']); ?>
                            </span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>

                    <?php if (!empty($row['description'])): ?>
                    <div class="bg-white p-4 rounded-xl border border-slate-100 text-sm text-slate-700 mb-4 shadow-sm">
                      <p class="italic text-slate-600">"<?php echo htmlspecialchars($row['description']); ?>"</p>
                    </div>
                    <?php endif; ?>

                    <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-400 pt-2 border-t border-slate-100">
                      <div class="flex items-center gap-1">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                        Reported: <span class="font-semibold text-slate-600"><?php echo htmlspecialchars($row['reported_date'] ?? ''); ?></span>
                      </div>
                      <div class="flex items-center gap-1">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                        By: <span class="font-semibold text-slate-600"><?php echo htmlspecialchars($row['reporter_name'] ?? 'Anonymous'); ?></span>
                      </div>
                    </div>
                  </div>
                <?php endwhile; ?>
              <?php else: ?>
                <div class="text-center py-8 text-slate-500">
                  No waste reports submitted yet.
                </div>
              <?php endif; ?>

            </div>
          </div>

          <!-- TAB 2: REPORT WASTE FORM -->
          <div id="tab-view-report" class="tab-content hidden">
            <form id="form-report" method="POST" action="wastemanagement.php" enctype="multipart/form-data">
              <input type="hidden" name="action_type" value="report_waste">
              
              <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-4 mb-8 flex items-start gap-3">
                <div class="p-2 bg-emerald-500 text-white rounded-xl flex-shrink-0 mt-0.5 shadow-sm">
                  <i data-lucide="map-pin-line" class="w-5 h-5"></i>
                </div>
                <p class="text-sm text-emerald-900 leading-relaxed font-medium">
                  Provide precise coordinates, structural landmarks, and high-quality photo evidence to help municipal teams sweep designated regions effectively.
                </p>
              </div>

              <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Location Address *</label>
                  <input type="text" name="location" required placeholder="Street, area, or landmark address" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nearby Landmark</label>
                  <input type="text" name="landmark" placeholder="e.g., Near City Mall, Opposite Metro Gate" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Waste Category *</label>
                  <select name="wasteType" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm text-slate-700">
                    <option value="">Select Waste Category</option>
                    <option value="plastic">Plastic Waste</option>
                    <option value="organic">Organic / Wet Waste</option>
                    <option value="paper">Paper & Cardboard</option>
                    <option value="electronic">E-Waste</option>
                    <option value="mixed">Mixed Garbage</option>
                  </select>
                </div>

                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Severity Level *</label>
                  <select name="severity" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm text-slate-700">
                    <option value="">Select Priority / Severity</option>
                    <option value="low">Low - Small Litter / Minor Pile</option>
                    <option value="medium">Medium - Moderate Accumulation</option>
                    <option value="high">High - Severe Dump Site</option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Upload Garbage Image</label>
                  <div class="relative border-2 border-dashed border-emerald-200 hover:border-emerald-500 rounded-2xl p-6 bg-emerald-50/20 hover:bg-emerald-50/50 transition-all text-center group cursor-pointer">
                    <input type="file" name="wastePhoto" id="wastePhotoInput" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(event)">
                    
                    <div id="upload-placeholder" class="space-y-2">
                      <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                        <i data-lucide="upload-cloud" class="w-6 h-6"></i>
                      </div>
                      <p class="text-sm font-semibold text-slate-700">Click to upload or drag photo here</p>
                      <p class="text-xs text-slate-400">PNG, JPG, or WEBP (Max 5MB)</p>
                    </div>

                    <div id="image-preview-container" class="hidden flex-col items-center gap-3">
                      <img id="image-preview" src="#" alt="Waste Preview" class="max-h-48 rounded-xl object-cover border border-emerald-200 shadow-md">
                      <span class="text-xs font-medium text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">Photo attached successfully</span>
                    </div>
                  </div>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Detailed Notes</label>
                  <textarea name="description" rows="3" placeholder="Provide extra helpful details (e.g., blocking roadway, bad odor, hazard details...)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"></textarea>
                </div>
              </div>

              <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-emerald-900/20 flex items-center justify-center gap-2 text-base">
                <i data-lucide="send" class="w-5 h-5"></i> Submit Waste Report
              </button>
            </form>
          </div>

          <!-- TAB 3: REQUEST PICKUP FORM -->
          <div id="tab-view-pickup" class="tab-content hidden">
            <form id="form-pickup" method="POST" action="wastemanagement.php">
              <input type="hidden" name="action_type" value="request_pickup">
              
              <div class="bg-teal-50/80 border border-teal-200 rounded-2xl p-4 mb-8 flex items-start gap-3">
                <div class="p-2 bg-teal-600 text-white rounded-xl flex-shrink-0 mt-0.5 shadow-sm">
                  <i data-lucide="recycle" class="w-5 h-5"></i>
                </div>
                <p class="text-sm text-teal-900 leading-relaxed font-medium">
                  Schedule home door-step pickups for recyclable materials and earn eco-points redeemable for sustainable green merchandise!
                </p>
              </div>

              <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i> Contact Information
              </h3>
              <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name *</label>
                  <input type="text" name="name" value="<?php echo htmlspecialchars($fullname); ?>" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone Number *</label>
                  <input type="tel" name="phone" required placeholder="Enter mobile number" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>
                <div class="md:col-span-2">
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pickup Address *</label>
                  <input type="text" name="address" required placeholder="Full street address, house no, apartment" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">City *</label>
                  <input type="text" name="city" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pincode *</label>
                  <input type="text" name="pincode" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm"/>
                </div>
              </div>

              <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-emerald-600"></i> Waste & Schedule Details
              </h3>
              <div class="grid md:grid-cols-2 gap-6 mb-8">
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Recyclable Type *</label>
                  <select name="wasteType" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm text-slate-700">
                    <option value="">Select Recyclable Category</option>
                    <option value="plastic">Plastic (Bottles, Containers)</option>
                    <option value="paper">Paper & Cardboard</option>
                    <option value="metal">Metal & Aluminum Cans</option>
                    <option value="glass">Glass Containers</option>
                    <option value="electronic">E-Waste / Appliances</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Approximate Quantity *</label>
                  <select name="quantity" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm text-slate-700">
                    <option value="">Select Estimated Weight/Volume</option>
                    <option value="small">Small (1-2 bags / &lt; 5kg)</option>
                    <option value="medium">Medium (3-5 bags / 5-15kg)</option>
                    <option value="large">Large (6+ bags / &gt; 15kg)</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Preferred Date *</label>
                  <input type="date" name="preferredDate" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm text-slate-700"/>
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Preferred Time Slot</label>
                  <select name="preferredTime" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent outline-none transition-all text-sm text-slate-700">
                    <option value="anytime">Any Time Slot</option>
                    <option value="morning">Morning (8 AM - 12 PM)</option>
                    <option value="afternoon">Afternoon (12 PM - 4 PM)</option>
                    <option value="evening">Evening (4 PM - 8 PM)</option>
                  </select>
                </div>
              </div>

              <!-- Rewards Banner -->
              <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-8 flex items-start gap-3">
                <div class="p-2 bg-amber-500 text-white rounded-xl flex-shrink-0 mt-0.5 shadow-sm">
                  <i data-lucide="award" class="w-5 h-5"></i>
                </div>
                <div>
                  <p class="font-bold text-amber-900 text-sm">Earn Eco-Reward Points!</p>
                  <p class="text-xs text-amber-800 leading-relaxed mt-0.5">
                    Your contribution helps conserve resources. Points are awarded upon successful verification and pickup.
                  </p>
                </div>
              </div>

              <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-emerald-900/20 flex items-center justify-center gap-2 text-base">
                <i data-lucide="calendar-check" class="w-5 h-5"></i> Request Pickup
              </button>
            </form>
          </div>

        </div>
      </div>

    </div>
  </main>

  <script>
    let currentTab = 'reports';

    function setActiveTab(tabName) {
      currentTab = tabName;
      
      // Reset All Tab Button Styles
      document.querySelectorAll('.id-tab-btn').forEach(btn => {
        btn.className = "flex-1 id-tab-btn py-3.5 px-5 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 text-slate-600 hover:bg-white/60";
      });

      // Hide All Content Tabs
      document.querySelectorAll('.tab-content').forEach(view => {
        view.classList.add('hidden');
      });

      // Activate Selected Tab View
      document.getElementById(`tab-view-${tabName}`).classList.remove('hidden');

      // Highlight Active Tab Button
      document.getElementById(`tab-btn-${tabName}`).className = "flex-1 id-tab-btn py-3.5 px-5 rounded-2xl font-bold text-sm transition-all flex items-center justify-center gap-2 bg-white text-emerald-700 shadow-sm border border-emerald-100";
    }

    // Photo Preview Functionality
    function previewImage(event) {
      const input = event.target;
      const placeholder = document.getElementById('upload-placeholder');
      const previewContainer = document.getElementById('image-preview-container');
      const previewImg = document.getElementById('image-preview');

      if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
          previewImg.src = e.target.result;
          placeholder.classList.add('hidden');
          previewContainer.classList.remove('hidden');
          previewContainer.classList.add('flex');
        }

        reader.readAsDataURL(input.files[0]);
      }
    }

    // Initialize Lucide Icons
    window.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
    });
  </script>

</body>
</html>