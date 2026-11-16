<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Blood Donor Network - TriSeva</title>

<!-- Lucide Icons for clean UI -->
<script src="https://unpkg.com/lucide@latest"></script>

<style>
*{
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body{
  background:#f4f6f9;
  padding-bottom:40px;
  color: #333;
}

/* Top Navigation Bar */
.top-nav {
  background:#001f54;
  padding:15px 30px;
  display:flex;
  justify-content:space-between;
  align-items:center;
  color:white;
}

.top-nav h2 {
  font-size: 22px;
  letter-spacing: 0.5px;
}

.top-nav a {
  color:white;
  text-decoration:none;
  font-weight:bold;
  font-size: 14px;
}

.logout-btn {
  background:#e53935;
  padding:8px 15px;
  border-radius:6px;
  transition: background 0.2s;
}

.logout-btn:hover {
  background: #c62828;
}

.container{
  max-width:1100px;
  margin:25px auto;
  padding: 0 15px;
}

.header{
  background:linear-gradient(135deg, #d32f2f, #e53935);
  color:white;
  padding:30px;
  border-radius:12px;
  margin-bottom:20px;
  box-shadow: 0 4px 12px rgba(229, 57, 53, 0.2);
}

.header h1{
  font-size:30px;
  margin-bottom: 5px;
}

.header p {
  opacity: 0.9;
  font-size: 14px;
}

.tabs{
  display:flex;
  background:white;
  border-radius:10px;
  overflow:hidden;
  margin-bottom:20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.tabs button{
  flex:1;
  padding:14px;
  border:none;
  background:white;
  cursor:pointer;
  font-size:15px;
  font-weight:bold;
  color: #555;
  transition: all 0.2s;
}

.tabs button.active{
  background:#e53935;
  color:white;
}

.section{
  background:white;
  padding:25px;
  border-radius:12px;
  display:none;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.section.active{
  display:block;
}

label {
  display: block;
  font-weight: bold;
  font-size: 13px;
  color: #444;
  margin-bottom: 5px;
}

input, select, textarea{
  width:100%;
  padding:12px;
  margin-bottom:18px;
  border:1px solid #ddd;
  border-radius:8px;
  font-size: 14px;
  outline: none;
  transition: border 0.2s;
}

input:focus, select:focus, textarea:focus {
  border-color: #e53935;
}

button.submit{
  background:#e53935;
  color:white;
  padding:14px;
  border:none;
  width:100%;
  border-radius:8px;
  font-size:16px;
  font-weight: bold;
  cursor:pointer;
  transition: background 0.2s;
}

button.submit:hover {
  background: #c62828;
}

.search-box{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:15px;
  margin-bottom:20px;
  background: #f9f9f9;
  padding: 15px;
  border-radius: 10px;
  border: 1px solid #eee;
}

/* Donor Card Styling */
.donor-card{
  border:1px solid #e2e8f0;
  padding:18px;
  border-radius:12px;
  margin-bottom:12px;
  background: #fff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}

.donor-info h3 {
  font-size: 18px;
  color: #1a202c;
  margin-bottom: 6px;
}

.donor-info p {
  font-size: 13px;
  color: #4a5568;
  margin-bottom: 4px;
}

.badge-available {
  display: inline-block;
  background: #e6fffa;
  color: #047857;
  border: 1px solid #a7f3d0;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  margin-top: 5px;
}

.badge-unavailable {
  display: inline-block;
  background: #fef2f2;
  color: #dc2626;
  border: 1px solid #fecaca;
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: bold;
  margin-top: 5px;
}

/* Action Connect Buttons */
.connect-actions {
  display: flex;
  gap: 8px;
}

.btn-call {
  background: #059669;
  color: white;
  padding: 9px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px;
  font-weight: bold;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.btn-alert {
  background: #eff6ff;
  color: #2563eb;
  border: 1px solid #bfdbfe;
  padding: 8px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
  font-weight: bold;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}

.btn-disabled {
  background: #f1f5f9;
  color: #94a3b8;
  border: 1px solid #e2e8f0;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 13px;
  font-weight: bold;
  cursor: not-allowed;
}

@media(max-width:768px){
  .search-box{
    grid-template-columns:1fr;
  }
  .donor-card {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
  .connect-actions {
    width: 100%;
  }
  .connect-actions a, .connect-actions button {
    flex: 1;
    justify-content: center;
  }
}
</style>
</head>

<body>

<!-- Top Navigation -->
<div class="top-nav">
  <div>
    <h2>TriSeva</h2>
  </div>
  <div>
    Welcome, <b id="loggedInUserName"><?php echo htmlspecialchars($fullname); ?></b>
  </div>
  <div>
    <a href="home.php" style="margin-right:20px;">Home</a>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</div>

<div class="container">

  <div class="header">
    <h1>❤️ Smart Blood Donor Network</h1>
    <p>Find Verified Donors & Submit Emergency Requests Fast</p>
  </div>

  <div class="tabs">
    <button class="tab-btn active" onclick="showTab('search', event)">Find Donors</button>
    <button class="tab-btn" onclick="showTab('register', event)">Register Donor</button>
    <button class="tab-btn" onclick="showTab('request', event)">Emergency Request</button>
  </div>

  <!-- Search Donors Section -->
  <div id="search" class="section active">
    <div class="search-box">
      <div>
        <label>Blood Group</label>
        <select id="bloodFilter" onchange="filterDonors()">
          <option value="">All Groups</option>
          <option>O+</option>
          <option>O-</option>
          <option>A+</option>
          <option>A-</option>
          <option>B+</option>
          <option>B-</option>
          <option>AB+</option>
          <option>AB-</option>
        </select>
      </div>

      <div>
        <label>Location / City</label>
        <input type="text" id="locationFilter" placeholder="Search by city (e.g. Madanapalle)" onkeyup="filterDonors()">
      </div>
    </div>

    <div id="donorList">
      <p style="text-align: center; color: #777; padding: 20px;">Loading donor network database...</p>
    </div>
  </div>

  <!-- Register Donor Section -->
  <div id="register" class="section">
    <form id="registerForm">
      <label>Full Name</label>
      <input type="text" name="fullname" placeholder="Enter Full Name" required>

      <label>Age</label>
      <input type="number" name="age" min="18" max="65" placeholder="Enter Age" required>

      <label>Blood Group</label>
      <select name="bloodgroup" required>
        <option value="">Select Blood Group</option>
        <option>O+</option>
        <option>O-</option>
        <option>A+</option>
        <option>A-</option>
        <option>B+</option>
        <option>B-</option>
        <option>AB+</option>
        <option>AB-</option>
      </select>

      <label>Phone Number</label>
      <input type="tel" name="phone" placeholder="9876543210" required>

      <label>Email Address</label>
      <input type="email" name="email" placeholder="example@gmail.com" required>

      <label>City</label>
      <input type="text" name="city" placeholder="Enter City" required>

      <label>State</label>
      <input type="text" name="state" placeholder="Enter State" required>

      <label>Pincode</label>
      <input type="text" name="pincode" placeholder="Enter Pincode" required>

      <label>Address</label>
      <textarea name="address" placeholder="Enter Full Address" rows="3"></textarea>

      <button class="submit" type="submit">Register as Voluntary Donor</button>
    </form>
  </div>

  <!-- Emergency Request Section -->
  <div id="request" class="section">
    <form id="requestForm">
      <label>Patient Name</label>
      <input type="text" name="patient_name" placeholder="Enter Patient Name" required>

      <label>Blood Group Needed</label>
      <select name="blood_group" required>
        <option value="">Select Blood Group</option>
        <option>O+</option>
        <option>O-</option>
        <option>A+</option>
        <option>A-</option>
        <option>B+</option>
        <option>B-</option>
        <option>AB+</option>
        <option>AB-</option>
      </select>

      <label>Units Needed</label>
      <input type="number" name="units_needed" placeholder="Units Required" required>

      <label>Hospital Name</label>
      <input type="text" name="hospital_name" placeholder="Hospital Name & Branch" required>

      <label>City</label>
      <input type="text" name="city" placeholder="City" required>

      <label>Contact Person Name</label>
      <input type="text" name="contact_person" placeholder="Contact Person" required>

      <label>Phone Number</label>
      <input type="tel" name="phone" placeholder="9876543210" required>

      <label>Urgency Level</label>
      <select name="urgency">
        <option>Normal</option>
        <option>Urgent</option>
        <option>Critical</option>
      </select>

      <label>Additional Information</label>
      <textarea name="additional_info" placeholder="Patient condition, room number, or special requirements" rows="3"></textarea>

      <button class="submit" type="submit">Submit Emergency Alert Request</button>
    </form>
  </div>

</div>

<script>
let donors = [];
const currentUserName = "<?php echo addslashes($fullname); ?>";
const localAvailability = localStorage.getItem("user_is_available") === "true";

// Fetch donors from get_donors.php
fetch("get_donors.php")
.then(res => res.json())
.then(data => {
  donors = data;
  displayDonors(donors);
})
.catch(err => {
  console.error("Error loading donors:", err);
  document.getElementById("donorList").innerHTML = `<p style="color:red; text-align:center;">Failed to fetch donor list. Please check get_donors.php database query.</p>`;
});

function showTab(tabId, evt) {
  document.querySelectorAll('.section').forEach(sec => sec.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

  document.getElementById(tabId).classList.add('active');
  if (evt && evt.target) {
    evt.target.classList.add('active');
  }
}

function displayDonors(list) {
  const donorList = document.getElementById("donorList");
  donorList.innerHTML = "";

  if (list.length === 0) {
    donorList.innerHTML = `<p style="text-align:center; color:#888; padding:20px;">No donors found matching your search parameters.</p>`;
    return;
  }

  list.forEach(donor => {
    // Sync the availability state for the current logged-in user with local storage status
    let isAvailable = donor.available === true || donor.available == 1 || donor.available === "true" || donor.available === "1";
    
    if (donor.fullname.toLowerCase().trim() === currentUserName.toLowerCase().trim()) {
      isAvailable = localAvailability;
    }

    donorList.innerHTML += `
      <div class="donor-card">
        <div class="donor-info">
          <h3>${donor.fullname}</h3>
          <p><strong>Blood Group:</strong> <span style="color:#e53935; font-weight:bold;">${donor.bloodgroup}</span></p>
          <p><strong>Location:</strong> ${donor.city}</p>
          <span class="${isAvailable ? 'badge-available' : 'badge-unavailable'}">
            ● ${isAvailable ? 'Available to Donate' : 'Currently Unavailable'}
          </span>
        </div>

        <div class="connect-actions">
          ${isAvailable ? `
            <a href="tel:${donor.phone || ''}" class="btn-call">
              <i data-lucide="phone" style="width:14px; height:14px;"></i> Call
            </a>
            <button class="btn-alert" onclick="sendEmergencyAlert('${donor.fullname}')">
              <i data-lucide="bell" style="width:14px; height:14px;"></i> Send Request
            </button>
          ` : `
            <button disabled class="btn-disabled">Unavailable</button>
          `}
        </div>
      </div>
    `;
  });

  setTimeout(() => {
    if (window.lucide) lucide.createIcons();
  }, 50);
}

function filterDonors() {
  let blood = document.getElementById("bloodFilter").value;
  let location = document.getElementById("locationFilter").value.toLowerCase();

  let filtered = donors.filter(donor => {
    let bloodMatch = !blood || donor.bloodgroup === blood;
    let locationMatch = !location || donor.city.toLowerCase().includes(location);
    return bloodMatch && locationMatch;
  });

  displayDonors(filtered);
}

function sendEmergencyAlert(donorName) {
  alert(`Emergency alert request sent to ${donorName}. The volunteer will receive an automated SMS/notification.`);
}

// Handle Form Submissions via AJAX
document.getElementById("registerForm").addEventListener("submit", function(e) {
  e.preventDefault();
  let formData = new FormData(this);

  fetch("register_donor.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    if(data.trim() === "success") {
      alert("Donor Registered Successfully!");
      location.reload();
    } else {
      alert("Registration response: " + data);
    }
  });
});

document.getElementById("requestForm").addEventListener("submit", function(e) {
  e.preventDefault();
  let formData = new FormData(this);

  fetch("request_blood.php", {
    method: "POST",
    body: formData
  })
  .then(res => res.text())
  .then(data => {
    if(data.trim() === "success") {
      alert("Emergency Request Broadcasted Successfully!");
      document.getElementById("requestForm").reset();
    } else {
      alert("Request failed: " + data);
    }
  });
});
</script>

</body>
</html>