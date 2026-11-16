<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TriSeva - NSS Smart Community Platform</title>

  <!-- Tailwind CSS & Alpine.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    .hero-pattern {
      background-image: radial-gradient(rgba(37, 99, 235, 0.08) 1.5px, transparent 0);
      background-size: 28px 28px;
    }
  </style>
</head>

<body
    class="min-h-screen bg-slate-50/70 text-slate-800 flex flex-col antialiased selection:bg-blue-600 selection:text-white"
    x-data="{
        // USER PROFILE DATA
        user: {
            name: '',
            username: '',
            email: '',
            avatar: null,
            isDonor: false,
            isAvailable: false,
            bloodGroup: '',
            volunteerId: ''
        },

        isProfileModalOpen: false,
        isLoadingProfile: true,

        // GET USER'S FIRST LETTER
        getUserInitial() {
            return this.user.name
                ? this.user.name.trim().charAt(0).toUpperCase()
                : 'U';
        },

        // HANDLE PROFILE IMAGE UPLOAD
        handleImageUpload(event) {
            const file = event.target.files[0];

            if (!file) {
                return;
            }

            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                event.target.value = '';
                return;
            }

            if (file.size > 1024 * 1024) {
                alert('Please select an image smaller than 1 MB.');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();

            reader.onload = (e) => {
                this.user.avatar = e.target.result;
            };

            reader.onerror = () => {
                alert('Unable to read this image. Please try again.');
            };

            reader.readAsDataURL(file);
        },

        // LOAD THE LOGGED-IN USER'S PROFILE FROM MYSQL
        async loadProfile() {
            this.isLoadingProfile = true;

            try {
                const response = await fetch('get_profile.php', {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (response.status === 401) {
                    window.location.href = 'login.php';
                    return;
                }

                const result = await response.json();

                if (!response.ok || !result.success || !result.data) {
                    throw new Error(
                        result.message || 'Unable to load your profile.'
                    );
                }

                const profile = result.data;

                this.user = {
                    name: profile.fullname || '',
                    username: profile.username || '',
                    email: profile.email || '',
                    avatar: profile.avatar || null,
                    isDonor: Number(profile.is_donor) === 1,
                    isAvailable: Number(profile.is_available) === 1,
                    bloodGroup: profile.blood_group || '',
                    volunteerId: profile.volunteer_id || ''
                };

            } catch (error) {
                console.error('Profile loading failed:', error);

                alert(
                    'Unable to load your profile. Please refresh the page. ' +
                    'If the problem continues, check get_profile.php.'
                );
            } finally {
                this.isLoadingProfile = false;
            }
        },

        // OPEN PROFILE MODAL
        openModal() {
            this.isProfileModalOpen = true;

            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        },

        // SAVE PROFILE TO MYSQL
        async saveProfile() {
            try {
                const response = await fetch('update_profile.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: this.user.name,
                        username: this.user.username,
                        email: this.user.email,
                        avatar: this.user.avatar,
                        isDonor: this.user.isDonor,
                        isAvailable: this.user.isAvailable,
                        bloodGroup: this.user.bloodGroup
                    })
                });

                if (response.status === 401) {
                    window.location.href = 'login.php';
                    return;
                }

                const result = await response.json();

                if (!response.ok || !result.success) {
                    throw new Error(
                        result.message || 'Unable to save your profile.'
                    );
                }

                // Reload the saved values from the database
                await this.loadProfile();

                this.isProfileModalOpen = false;

                alert('Profile updated successfully!');

            } catch (error) {
                console.error('Profile save failed:', error);

                alert(
                    'Unable to save your profile. Please check ' +
                    'update_profile.php and your database columns.'
                );
            }
        }
    }"
    x-init="loadProfile()"
>


  <!-- Announcement Bar -->
  <div class="bg-gradient-to-r from-blue-700 via-indigo-600 to-blue-700 text-white text-xs font-semibold tracking-wide py-2.5 px-4 text-center flex items-center justify-center gap-2 shadow-xs">
    <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-[11px] uppercase font-bold tracking-wider text-blue-50 border border-white/20">NSS Motto</span>
    <span>"NOT ME BUT YOU" — Official National Service Scheme Community Portal</span>
  </div>

  <!-- Navigation Header -->
  <nav class="bg-white/90 backdrop-blur-md border-b border-blue-100 sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        
        <div class="flex items-center gap-3.5">
  <div class="relative w-12 h-12 flex-shrink-0">
    <img
      src="trilogo1.png"
      alt="TriSeva NSS Unit Logo" onerror 
      class="w-full h-full object-contain drop-shadow-sm"
    />

    <div
      class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white shadow-xs"
      :class="user.isAvailable ? 'bg-emerald-500' : 'bg-amber-500'"
    ></div>
  </div>

  <div>
    <div class="flex items-center gap-2">
      <h1 class="font-extrabold text-blue-900 text-2xl tracking-tight leading-none">
        Tri<span class="text-blue-600">Seva</span>
      </h1>
      <span class="px-2 py-0.5 rounded-full bg-blue-50 border border-blue-200 text-[10px] font-bold text-blue-800 uppercase">
        NSS Unit
      </span>
    </div>
    <p class="text-xs text-slate-500 font-semibold mt-0.5">
      National Service Scheme Platform
    </p>
  </div>
</div>


        <!-- Navigation Links -->
        <ul class="hidden lg:flex items-center gap-1 font-semibold text-sm text-slate-600">
          <li><a href="home.php" class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 font-bold">Home</a></li>
          <li><a href="bloodsupport.php" class="px-4 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition-colors">Blood Support</a></li>
          <li><a href="missingalerts.php" class="px-4 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition-colors">Missing Alerts</a></li>
          <li><a href="wastemanagement.php" class="px-4 py-2 rounded-xl hover:bg-slate-100 text-slate-700 transition-colors">Waste Management</a></li>
          
        </ul>

        <!-- Right Side: User Profile Trigger & Logout -->
        <div class="flex items-center gap-3">
          
          <!-- Avatar Initial Circle Trigger -->
          <button @click="openModal()" 
                  class="flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-blue-500 text-white font-extrabold text-lg shadow-md shadow-blue-500/20 hover:scale-105 active:scale-95 transition-all duration-200 relative group overflow-hidden border-2 border-white"
                  title="View / Edit Profile">
            
            <template x-if="user.avatar">
              <img :src="user.avatar" :alt="user.name" class="w-full h-full object-cover rounded-2xl">
            </template>
            
            <template x-if="!user.avatar">
              <span x-text="getUserInitial()" class="tracking-wide"></span>
            </template>

            <!-- Donor Indicator Dot -->
            <template x-if="user.isDonor">
              <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500 border border-white"></span>
              </span>
            </template>
          </button>

          <!-- Logout Button -->
          <a href="logout.php" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 p-2.5 sm:px-3.5 sm:py-2.5 rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5" title="Logout">
            <i data-lucide="log-out" class="w-4 h-4"></i>
            <span class="hidden md:inline">Logout</span>
          </a>

        </div>

      </div>
    </div>
  </nav>

  <!-- Hero Banner -->
  <section class="relative bg-gradient-to-b from-blue-50/80 via-sky-50/50 to-slate-50 text-slate-800 py-12 lg:py-20 px-4 overflow-hidden hero-pattern">
    <div class="max-w-7xl mx-auto text-center max-w-3xl">
      <div class="inline-flex items-center gap-2 bg-white border border-blue-200 px-4 py-1.5 rounded-full text-xs font-bold text-blue-700 shadow-xs mb-6">
        <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
        Unified NSS Civic & Emergency Response Portal
      </div>

      <h1 class="text-4xl sm:text-5xl font-black tracking-tight leading-tight text-blue-950 mb-4">
        Community Service through <br/>
        <span class="text-blue-600">Peace, Harmony & Action</span>
      </h1>

      <p class="text-base sm:text-lg text-slate-600 font-medium max-w-2xl mx-auto mb-8">
        Connecting student volunteers with citizens for urgent blood requests, missing person alerts, and smart eco-waste management.
      </p>
    </div>
  </section>

  <!-- Service Modules Grid -->
  <section class="py-12 px-4 max-w-7xl mx-auto"
           x-data="{
            modules: [
                {
                    id: 'blood',
                    title: 'Blood Support Network',
                    subtitle: 'Lifesaving Emergency Network',
                    description: 'Connect with verified voluntary donors during urgent medical situations. Check live blood stock across regional blood banks.',
                    icon: 'heart-pulse',
                    targetPage: 'bloodsupport.php',
                    color: 'from-rose-500 to-red-600',
                    badgeBg: 'bg-rose-50 text-rose-700 border-rose-200',
                    image: 'https://images.unsplash.com/photo-1615461066841-6116e61058f4?auto=format&fit=crop&w=800&q=80',
                    features: ['Instant Donor Matching', 'Emergency Request Alerts', 'Live Donor Availability', 'Verified Medical Badges']
                },
                {
                    id: 'missing',
                    title: 'Missing Person Alerts',
                    subtitle: 'Community Protection',
                    description: 'Rapid broadcast network to publish and locate missing children, elderly citizens, and vulnerable individuals.',
                    icon: 'siren',
                    targetPage: 'missingalerts.php',
                    color: 'from-amber-500 to-orange-600',
                    badgeBg: 'bg-amber-50 text-amber-800 border-amber-200',
                    image: 'https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?auto=format&fit=crop&w=800&q=80',
                    features: ['Instant Photo Broadcast', 'Geofenced Mobile Alerts', 'Volunteer Search Units', 'Direct Hotline Liaison']
                },
                {
                    id: 'waste',
                    title: 'Smart Waste Management',
                    subtitle: 'Clean & Green Earth',
                    description: 'Report local dumping yards with photos, request doorstep recycling pickups, and earn green volunteer credits.',
                    icon: 'leaf',
                    targetPage: 'wastemanagement.php',
                    color: 'from-emerald-500 to-teal-600',
                    badgeBg: 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    image: 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?auto=format&fit=crop&w=800&q=80',
                    features: ['GPS Garbage Mapping', 'Photo Proof Verification', 'Doorstep Recycle Pickup', 'Eco Reward Points']
                }
            ]
           }">

    <div class="text-center max-w-2xl mx-auto mb-10">
      <span class="text-xs font-bold uppercase tracking-widest text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">Our Services</span>
      <h3 class="text-3xl font-extrabold text-blue-950 mt-3 tracking-tight">Integrated NSS Services</h3>
    </div>

    <div class="grid md:grid-cols-3 gap-8">
      <template x-for="module in modules" :key="module.id">
        <div @click="window.location.href = module.targetPage"
             class="group bg-white rounded-3xl overflow-hidden shadow-xs hover:shadow-xl border border-slate-200 transition-all duration-300 cursor-pointer flex flex-col justify-between transform hover:-translate-y-1">
          <div>
            <div class="relative h-48 overflow-hidden bg-slate-100">
              <img :src="module.image" :alt="module.title" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>
              <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
                <span class="px-3 py-1 rounded-full text-xs font-bold border shadow-xs" :class="module.badgeBg" x-text="module.subtitle"></span>
                <div :class="'p-2.5 rounded-xl text-white bg-gradient-to-r ' + module.color + ' shadow-md'">
                  <i :data-lucide="module.icon" class="w-5 h-5"></i>
                </div>
              </div>
            </div>

            <div class="p-6">
              <h4 class="text-xl font-bold text-slate-900 group-hover:text-blue-600 transition-colors mb-2" x-text="module.title"></h4>
              <p class="text-slate-600 text-sm leading-relaxed mb-6 font-normal" x-text="module.description"></p>

              <ul class="space-y-2.5 mb-6 pt-4 border-t border-slate-100">
                <template x-for="(feature, idx) in module.features" :key="idx">
                  <li class="flex items-center text-xs font-semibold text-slate-700">
                    <div class="w-1.5 h-1.5 bg-blue-600 rounded-full mr-2.5"></div>
                    <span x-text="feature"></span>
                  </li>
                </template>
              </ul>
            </div>
          </div>

          <div class="px-6 pb-6 pt-0">
            <div class="w-full bg-slate-50 group-hover:bg-blue-600 group-hover:text-white text-slate-700 py-3 rounded-2xl text-xs font-bold transition-all flex items-center justify-center gap-2 border border-slate-200 group-hover:border-blue-600 shadow-xs">
              <span>Access Service</span>
              <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
            </div>
          </div>
        </div>
      </template>
    </div>
  </section>

  <!-- Emergency Banner -->
  <section class="py-12 px-4 bg-red-600 text-white">
    <div class="max-w-4xl mx-auto text-center">
      <div class="flex justify-center mb-4">
        <i data-lucide="phone" class="w-12 h-12"></i>
      </div>
      <h2 class="text-3xl font-bold mb-3">24/7 Emergency Support</h2>
      <p class="text-xl mb-6">Need urgent help? Contact our emergency hotline</p>
      <div class="text-4xl font-bold mb-2">1800-NSS-HELP</div>
      <div class="text-lg">(1800-677-4357)</div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-white border-t border-slate-200 py-6 px-4 text-slate-500 text-xs mt-auto">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <span class="font-bold text-slate-800">TriSeva Community Platform</span>
        <span>— Official National Service Scheme Initiative</span>
      </div>
      <div>
        <p>&copy; 2026 TriSeva Platform. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- MODERN PROFILE POPUP / MODAL (POPS UP WHEN AVATAR CLICKED) -->
  <div x-show="isProfileModalOpen" 
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
       style="display: none;">
    
    <!-- Modal Card -->
    <div @click.away="isProfileModalOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-200/80 overflow-hidden relative">
      
      <!-- Top Decorative Modal Header -->
      <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 h-28 p-5 relative flex justify-between items-start">
        <div class="text-white">
          <span class="text-[10px] font-extrabold uppercase tracking-widest bg-white/20 px-2.5 py-0.5 rounded-full border border-white/20">Volunteer Profile</span>
          <h3 class="text-lg font-bold mt-0.5">Edit Profile Details</h3>
        </div>
        <button @click="isProfileModalOpen = false" class="w-8 h-8 rounded-full bg-black/20 hover:bg-black/40 text-white flex items-center justify-center backdrop-blur-md transition-colors cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div class="px-6 pb-6 pt-0 relative">
        
        <!-- Avatar Photo Upload Box -->
        <div class="flex flex-col items-center -mt-12 mb-6">
          <div class="relative group">
            <div class="w-24 h-24 rounded-3xl bg-slate-100 border-4 border-white shadow-lg overflow-hidden flex items-center justify-center bg-gradient-to-br from-blue-600 to-indigo-700 text-white font-black text-3xl">
              <template x-if="user.avatar">
                <img :src="user.avatar" class="w-full h-full object-cover">
              </template>
              <template x-if="!user.avatar">
                <span x-text="getUserInitial()"></span>
              </template>
            </div>

            <!-- Upload Badge Overlay Button -->
            <label for="profile-avatar-upload" class="absolute bottom-0 right-0 p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md border-2 border-white cursor-pointer transition-transform hover:scale-110" title="Upload New Photo">
              <i data-lucide="camera" class="w-4 h-4"></i>
            </label>
            <input id="profile-avatar-upload" type="file" accept="image/*" class="hidden" @change="handleImageUpload($event)">
          </div>

          <span class="text-[11px] font-semibold text-slate-500 mt-2">Click icon to upload profile photo</span>
        </div>

        <!-- Edit Profile Form Input Fields -->
        <form @submit.prevent="saveProfile()" class="space-y-4">
          
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
            <input type="text" x-model="user.name" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
              <input type="text" x-model="user.username" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition-all">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Volunteer Cadre ID</label>
              <input type="text" x-model="user.volunteerId" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-xs font-bold text-slate-500 cursor-not-allowed">
            </div>
          </div>

          <!-- Blood Donor Status Selector -->
          <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-2xl space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <i data-lucide="heart" class="w-3.5 h-3.5 text-rose-600 fill-rose-600"></i> Voluntary Blood Donor
              </span>
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" x-model="user.isDonor" class="sr-only peer">
                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-rose-600"></div>
              </label>
            </div>

            <!-- ADDED: DONOR AVAILABILITY TOGGLE OPTION -->
            <template x-if="user.isDonor">
              <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                <div>
                  <span class="block text-xs font-bold text-slate-800">Donor Availability</span>
                  <span class="text-[10px] font-semibold" :class="user.isAvailable ? 'text-emerald-600' : 'text-rose-600'" x-text="user.isAvailable ? 'Marked as Available' : 'Marked as Unavailable'"></span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                  <input type="checkbox" x-model="user.isAvailable" class="sr-only peer">
                  <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
              </div>
            </template>

            <template x-if="user.isDonor">
              <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between">
                <span class="text-[11px] font-semibold text-slate-600">Blood Group</span>
                <select x-model="user.bloodGroup" class="bg-white border border-slate-200 text-slate-800 text-xs font-bold rounded-lg px-2.5 py-1 focus:outline-none focus:border-rose-500">
                  <option value="A+">A+</option>
                  <option value="A-">A-</option>
                  <option value="B+">B+</option>
                  <option value="B-">B-</option>
                  <option value="O+">O+</option>
                  <option value="O-">O-</option>
                  <option value="AB+">AB+</option>
                  <option value="AB-">AB-</option>
                </select>
              </div>
            </template>
          </div>

          <!-- Action Buttons -->
          <div class="pt-2 flex items-center justify-end gap-2">
            <button type="button" @click="isProfileModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
              Cancel
            </button>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition-all cursor-pointer">
              Save Profile
            </button>
          </div>

        </form>

      </div>
    </div>
  </div>

  <script>
    setTimeout(() => {
      lucide.createIcons();
    }, 150);
  </script>
</body>
</html>