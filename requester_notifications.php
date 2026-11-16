<?php
// requester_notifications.php
require_once 'db_connect.php';

// In production, match this index dynamically using an active session or a tracked verification cookie string key
$request_id = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

if($request_id == 0) {
    die("Error context: missing standard active query execution parameter context tracking indexes.");
}

// 1. Fetch core base request information parameters
$req_sql = "SELECT * FROM emergency_requests WHERE id = ?";
$stmt = mysqli_prepare($conn, $req_sql);
mysqli_stmt_bind_param($stmt, "i", $request_id);
mysqli_stmt_execute($stmt);
$request_info = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// 2. Extract acceptance matches linked directly to target profiles inside blood_donors
$resp_sql = "SELECT dr.responded_at, d.name as donor_name, d.phone as donor_phone, d.city as donor_city 
             FROM donor_responses dr
             JOIN blood_donors d ON dr.donor_id = d.id
             WHERE dr.request_id = ? AND dr.response_status = 'accepted'
             ORDER BY dr.responded_at DESC";

$stmt2 = mysqli_prepare($conn, $resp_sql);
mysqli_stmt_bind_param($stmt2, "i", $request_id);
mysqli_stmt_execute($stmt2);
$responses = mysqli_stmt_get_result($stmt2);
mysqli_stmt_close($stmt2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriSeva - Request Status Tracking Updates</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body bg-white p-4 rounded">
                        <h2 class="text-secondary mb-1">Live Tracking Update Portal</h2>
                        <p class="text-muted">Patient: <strong><?php echo htmlspecialchars($request_info['patient_name'] ?? 'N/A'); ?></strong> | Group Needed: <span class="badge bg-danger fs-6"><?php echo htmlspecialchars($request_info['blood_group'] ?? 'N/A'); ?></span></p>
                    </div>
                </div>

                <h3 class="my-4 text-success"><i class="bi bi-heart-pulse-fill"></i> Donors Who Have Accepted Your Request</h3>
                
                <?php if(mysqli_num_rows($responses) == 0): ?>
                    <div class="alert alert-warning p-4 border-0 shadow-sm" role="alert">
                        <h4 class="alert-heading">Searching for Matches...</h4>
                        <p class="mb-0">We have broadcast your request to available matching donors in the area. This dashboard updates automatically as soon as a donor accepts.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php while($donor = mysqli_fetch_assoc($responses)): ?>
                            <div class="col-md-6">
                                <div class="card border-success h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h5 class="card-title text-success mb-0"><?php echo htmlspecialchars($donor['donor_name']); ?></h5>
                                            <span class="badge bg-success">Ready to Donate</span>
                                        </div>
                                        <p class="card-text mb-1"><strong>Phone Number:</strong> <?php echo htmlspecialchars($donor['donor_phone']); ?></p>
                                        <p class="card-text mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($donor['donor_city']); ?></p>
                                        <p class="card-text text-muted mt-2" style="font-size: 0.85rem;">Accepted at: <?php echo date('h:i A on M d, Y', strtotime($donor['responded_at'])); ?></p>
                                        <a href="tel:<?php echo $donor['donor_phone']; ?>" class="btn btn-success btn-sm w-100 mt-3">Call Donor Now</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-4 text-center">
                    <a href="emergency_request.php" class="btn btn-outline-secondary">Create a New Request</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>