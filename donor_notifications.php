<?php
// donor_notifications.php
require_once 'db.php';

// Simulate donor session logic (Update this block to match your global application authentication keys)
session_start();
if(!isset($_SESSION['donor_id'])) {
    // For test simulation purpose, hardcoding donor_id = 1. Replace with your actual authentication redirect.
    $_SESSION['donor_id'] = 1; 
}
$donor_id = $_SESSION['donor_id'];

// 1. Fetch Badge Counter Metric (Unread Notifications)
$badge_sql = "SELECT COUNT(*) as unread_count FROM notifications WHERE donor_id = ? AND status = 'unread'";
$stmt = mysqli_prepare($conn, $badge_sql);
mysqli_stmt_bind_param($stmt, "i", $donor_id);
mysqli_stmt_execute($stmt);
$badge_res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$unread_count = $badge_res['unread_count'];
mysqli_stmt_close($stmt);

// 2. Get all alerts matching this user
$notif_sql = "SELECT n.*, er.urgency, er.blood_group FROM notifications n 
              JOIN emergency_requests er ON n.request_id = er.id 
              WHERE n.donor_id = ? ORDER BY n.created_at DESC";
$stmt2 = mysqli_prepare($conn, $notif_sql);
mysqli_stmt_bind_param($stmt2, "i", $donor_id);
mysqli_stmt_execute($stmt2);
$notifications = mysqli_stmt_get_result($stmt2);
mysqli_stmt_close($stmt2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriSeva - Donor Dashboard Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    
    <!-- Simple Navbar -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">TriSeva Donor Portal</a>
            <div class="position-relative ms-auto me-3">
                <i class="bi bi-bell-fill text-white fs-4"></i>
                <?php if($unread_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $unread_count; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">Your Emergency Notifications</h2>
        <div class="row">
            <div class="col-lg-9">
                <?php if(mysqli_num_rows($notifications) == 0): ?>
                    <div class="card p-5 text-center shadow-sm">
                        <i class="bi bi-shield-check text-success display-1 mb-3"></i>
                        <p class="text-muted fs-5">No emergency alerts active or available for your area right now.</p>
                    </div>
                <?php else: ?>
                    <div class="list-group shadow-sm">
                        <?php while($row = mysqli_fetch_assoc($notifications)): 
                            // Establish localized boundary layout rules matching contextual urgency metrics
                            $border_class = "border-start border-4 ";
                            if($row['urgency'] == 'Critical') $border_class .= "border-start-danger";
                            elseif($row['urgency'] == 'Urgent') $border_class .= "border-start-warning";
                            else $border_class .= "border-start-info";
                            
                            $bg_unread = ($row['status'] == 'unread') ? 'bg-white font-weight-bold' : 'bg-light';
                        ?>
                            <div class="list-group-item list-group-item-action p-4 <?php echo $bg_unread; ?> <?php echo $border_class; ?> mb-2 rounded position-relative" id="notif-card-<?php echo $row['id']; ?>">
                                <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                    <h5 class="mb-1 text-danger">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($row['title']); ?>
                                    </h5>
                                    <small class="text-muted"><?php echo date('M d, h:i A', strtotime($row['created_at'])); ?></small>
                                </div>
                                <p class="mb-3 text-secondary" style="font-size: 0.95rem; line-height: 1.5;">
                                    <?php echo htmlspecialchars($row['message']); ?>
                                </p>
                                
                                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                    <div>
                                        <span class="badge bg-secondary p-2 me-2">Status: <?php echo ucfirst($row['status']); ?></span>
                                    </div>
                                    <div class="action-buttons shadow-wrapper">
                                        <?php if($row['status'] == 'unread' || $row['status'] == 'read'): ?>
                                            <button onclick="processResponse(<?php echo $row['id']; ?>, 'accept')" class="btn btn-success btn-sm px-3 me-2">
                                                <i class="bi bi-check-circle me-1"></i> Accept Request
                                            </button>
                                            <button onclick="processResponse(<?php echo $row['id']; ?>, 'decline')" class="btn btn-outline-danger btn-sm px-3">
                                                <i class="bi bi-x-circle me-1"></i> Decline
                                            </button>
                                        <?php elseif($row['status'] == 'accepted'): ?>
                                            <span class="text-success fw-bold"><i class="bi bi-check-lg"></i> You accepted this request</span>
                                        <?php else: ?>
                                            <span class="text-muted text-decoration-line-through"><i class="bi bi-x"></i> Declined</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- AJAX Engine Module logic -->
    <script>
    function processResponse(notificationId, actionsign) {
        const targetUrl = actionsign === 'accept' ? 'accept_request.php' : 'decline_request.php';
        
        // Use Fetch API to execute smooth updates without refreshing page
        fetch(targetUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'notification_id=' + notificationId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                location.reload(); // Reload UI state to clean badges and update card styles seamlessly
            } else {
                alert('Action failed: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error executing operation execution context:', error);
        });
    }
    </script>
</body>
</html>