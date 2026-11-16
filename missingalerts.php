<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$fullname = $_SESSION['fullname'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missing Person Alerts</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50 py-8 px-4">

<!-- Top Navigation -->
<div style="background:#001f54; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; color:white; margin-bottom:20px;">
    <div>
        <h2>TriSeva</h2>
    </div>
    <div>
        Welcome, <b id="loggedInUserName"><?php echo htmlspecialchars($fullname); ?></b>
    </div>
    <div>
        <a href="home.php" style="color:white; text-decoration:none; margin-right:20px; font-weight:bold;">Home</a>
        <a href="logout.php" style="background:red; padding:10px 15px; border-radius:5px; color:white; text-decoration:none; font-weight:bold;">Logout</a>
    </div>
</div>

<div class="max-w-7xl mx-auto" x-data="missingApp()">
    <div class="bg-gradient-to-r from-orange-600 to-yellow-600 text-white rounded-lg p-8 mb-8">
        <div class="flex items-center mb-4">
            <i data-lucide="alert-circle" class="w-12 h-12 mr-4"></i>
            <div>
                <h1 class="text-4xl font-bold">Missing Person Alerts</h1>
                <p class="text-lg text-orange-100">Help reunite families by spreading alerts</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-orange-600 mb-1" x-text="alerts.length">0</div>
            <div class="text-gray-600">Total Alerts</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-green-600 mb-1" x-text="countByStatus('found')">0</div>
            <div class="text-gray-600">People Found</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-blue-600 mb-1">12 min</div>
            <div class="text-gray-600">Avg Response Time</div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="flex border-b">
            <button
                @click="activeTab = 'alerts'"
                :class="activeTab === 'alerts' ? 'border-b-4 border-orange-600 text-orange-600' : 'text-gray-600 hover:text-gray-900'"
                class="flex-1 py-4 px-6 font-semibold transition-colors flex items-center justify-center cursor-pointer"
            >
                <i data-lucide="search" class="w-5 h-5 inline mr-2"></i>
                Active Alerts
            </button>
            <button
                @click="activeTab = 'report'"
                :class="activeTab === 'report' ? 'border-b-4 border-orange-600 text-orange-600' : 'text-gray-600 hover:text-gray-900'"
                class="flex-1 py-4 px-6 font-semibold transition-colors flex items-center justify-center cursor-pointer"
            >
                <i data-lucide="alert-circle" class="w-5 h-5 inline mr-2"></i>
                Report Missing Person
            </button>
        </div>

        <div class="p-6">
            <!-- Active Alerts Tab -->
            <div x-show="activeTab === 'alerts'">
                
                <!-- Search and Status Filter Bar -->
                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <div class="md:col-span-2">
                        <input
                            type="text"
                            x-model="searchQuery"
                            placeholder="Search by name or location..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none"
                        />
                    </div>
                    <div>
                        <select 
                            x-model="statusFilter" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 bg-white font-medium text-gray-700 outline-none"
                        >
                            <option value="all">All Statuses</option>
                            <option value="missing">Active (Missing)</option>
                            <option value="found">Found</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <template x-for="alert in alerts" :key="alert.id">
                        <div 
                            x-show="filterAlerts(alert)"
                            :class="{
                                'border-orange-200 bg-orange-50': alert.status === 'missing' || !alert.status,
                                'border-green-200 bg-green-50': alert.status === 'found',
                                'border-gray-200 bg-gray-100': alert.status === 'closed'
                            }"
                            class="border-2 rounded-lg p-6 mb-4"
                        >
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-start flex-1">
                                    <img
                                        :src="'uploads/' + (alert.photo || 'placeholder.png')"
                                        alt="Missing Person"
                                        class="w-24 h-24 object-cover rounded-lg mr-4"
                                    >
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                <h3 class="text-2xl font-bold text-gray-900" x-text="alert.person_name"></h3>
                                                <span
                                                    :class="{
                                                        'bg-orange-200 text-orange-800': alert.status === 'missing' || !alert.status,
                                                        'bg-green-200 text-green-800': alert.status === 'found',
                                                        'bg-gray-300 text-gray-800': alert.status === 'closed'
                                                    }"
                                                    class="ml-3 px-3 py-1 rounded-full text-sm font-semibold capitalize"
                                                    x-text="alert.status === 'missing' ? 'Missing' : (alert.status === 'found' ? 'Found' : 'Closed')"
                                                ></span>
                                            </div>

                                            <!-- Admin Status Control Dropdown -->
                                            <div class="flex items-center bg-white px-3 py-1 rounded border border-gray-300 shadow-sm">
                                                <span class="text-xs font-bold text-gray-500 uppercase mr-2">Admin Status:</span>
                                                <select 
                                                    :value="alert.status || 'missing'" 
                                                    @change="updateStatus(alert.id, $event.target.value)"
                                                    class="text-sm font-semibold text-gray-800 bg-transparent outline-none cursor-pointer"
                                                >
                                                    <option value="missing">Active (Missing)</option>
                                                    <option value="found">Found</option>
                                                    <option value="closed">Closed</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                            <div>
                                                <span class="text-gray-600">Age:</span>
                                                <span class="font-semibold" x-text="alert.age + ' years'"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Gender:</span>
                                                <span class="font-semibold" x-text="alert.gender"></span>
                                            </div>
                                            <div>
                                                <span class="text-gray-600">Height:</span>
                                                <span class="font-semibold" x-text="alert.height || 'N/A'"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-gray-700">
                                    <i data-lucide="map-pin" class="w-5 h-5 mr-2 text-orange-600"></i>
                                    <span class="font-semibold">Last Seen:</span>
                                    <span class="ml-2" x-text="alert.last_seen_location"></span>
                                </div>
                                <div class="flex items-center text-gray-700">
                                    <i data-lucide="clock" class="w-5 h-5 mr-2 text-orange-600"></i>
                                    <span class="font-semibold">Date & Time:</span>
                                    <span class="ml-2" x-text="alert.last_seen_date + (alert.last_seen_time ? ' at ' + alert.last_seen_time : '')"></span>
                                </div>
                            </div>

                            <div class="bg-white rounded p-3 mb-4 border">
                                <p class="text-gray-700"><strong>Clothing:</strong> <span x-text="alert.clothing"></span></p>
                                <template x-if="alert.distinguishing_features">
                                    <p class="text-gray-700 mt-1"><strong>Features:</strong> <span x-text="alert.distinguishing_features"></span></p>
                                </template>
                            </div>

                            <div class="flex gap-3">
                                <a :href="'tel:' + alert.contact_phone" class="flex-1 bg-orange-600 text-white py-2 rounded-lg hover:bg-orange-700 transition-colors font-semibold flex items-center justify-center cursor-pointer text-center">
                                    <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                    Contact: <span class="ml-1" x-text="alert.contact_phone"></span>
                                </a>
                                <button class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold cursor-pointer">
                                    I Have Information
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Report Missing Person Tab -->
            <div x-show="activeTab === 'report'">
                <form id="missingForm" @submit.prevent="submitForm" enctype="multipart/form-data">
                    <div class="bg-orange-50 border-l-4 border-orange-600 p-4 mb-6">
                        <div class="flex items-center">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-orange-600 mr-2"></i>
                            <p class="text-sm text-orange-700">
                                Please provide as much detail as possible.
                            </p>
                        </div>
                    </div>

                    <h3 class="font-semibold text-lg mb-4">Missing Person Details</h3>
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="person_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                            <input type="number" name="age" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                            <select name="gender" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Height (approx.)</label>
                            <input type="text" name="height" placeholder="e.g., 5'6&quot;" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Seen Location *</label>
                            <input type="text" name="last_seen_location" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Seen Date *</label>
                            <input type="date" name="last_seen_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Seen Time</label>
                            <input type="time" name="last_seen_time" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Photo</label>
                            <input type="file" name="photo" accept="image/*" class="w-full border p-2 rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Clothing Description *</label>
                            <input type="text" name="clothing" required placeholder="What was the person wearing?" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Distinguishing Features</label>
                            <input type="text" name="distinguishing_features" placeholder="Scars, birthmarks, tattoos, etc." class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <h3 class="font-semibold text-lg mb-4">Contact Information</h3>
                    <div class="grid md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Name *</label>
                            <input type="text" name="contact_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Phone Number *</label>
                            <input type="tel" name="contact_phone" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Relation to Missing Person *</label>
                            <input type="text" name="contact_relation" required placeholder="e.g., Father, Friend" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Police Complaint Filed? *</label>
                            <select name="police_complaint" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-white">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                                <option value="in-progress">In Progress</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Information</label>
                            <textarea name="additional_info" rows="3" placeholder="Any other relevant details..." class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        :disabled="isSubmitting"
                        :class="isSubmitting ? 'bg-gray-400 cursor-not-allowed' : 'bg-orange-600 hover:bg-orange-700 cursor-pointer'"
                        class="w-full text-white py-3 rounded-lg transition-colors font-semibold"
                    >
                        <span x-text="isSubmitting ? 'Submitting Report...' : 'Submit Missing Person Report'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function missingApp() {
    return {
        activeTab: 'alerts',
        searchQuery: '',
        statusFilter: 'all',
        alerts: [],
        isSubmitting: false,

        init() {
            this.fetchAlerts();
        },

        refreshIcons() {
            this.$nextTick(() => {
                if (window.lucide) lucide.createIcons();
            });
        },

        fetchAlerts() {
            fetch('get_missing_persons.php')
                .then(res => res.json())
                .then(data => {
                    this.alerts = data;
                    this.refreshIcons();
                })
                .catch(err => console.error("Error fetching data:", err));
        },

        countByStatus(statusName) {
            return this.alerts.filter(a => a.status === statusName).length;
        },

        filterAlerts(alert) {
            // Search query filtering
            const q = this.searchQuery.toLowerCase();
            const nameMatch = alert.person_name && alert.person_name.toLowerCase().includes(q);
            const locationMatch = alert.last_seen_location && alert.last_seen_location.toLowerCase().includes(q);
            const matchesSearch = !q || nameMatch || locationMatch;

            // Status filter dropdown matching
            const currentStatus = alert.status || 'missing';
            const matchesStatus = (this.statusFilter === 'all') || (currentStatus === this.statusFilter);

            return matchesSearch && matchesStatus;
        },

        updateStatus(id, newStatus) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', newStatus);

            fetch('update_status.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    // Update in place so UI reflects changes instantly
                    const target = this.alerts.find(a => a.id == id);
                    if (target) target.status = newStatus;
                    this.refreshIcons();
                } else {
                    alert('Failed to update status: ' + res.message);
                }
            })
            .catch(err => {
                console.error("Error updating status:", err);
                alert("Network error updating status.");
            });
        },

        submitForm(e) {
            this.isSubmitting = true;
            const formData = new FormData(e.target);

            fetch('save_missing_person.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                this.isSubmitting = false;
                const response = data.trim();

                if (response === "success") {
                    alert('Report submitted successfully!');
                    e.target.reset();
                    this.fetchAlerts();
                    this.activeTab = 'alerts';
                } else {
                    console.error("Server error response:", response);
                    alert('Failed to submit report. Server returned: ' + response);
                }
            })
            .catch(err => {
                this.isSubmitting = false;
                console.error("Network error:", err);
                alert('Network error while submitting report.');
            });
        }
    }
}
</script>
</body>
</html>