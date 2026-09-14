<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TrashPandaStudio</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- ================= HEADER ================= -->
    <header>
        <div class="logo">
            <img src="images/logo.png" alt="TrashPandaStudio logo">
            <span>TrashPandaStudio</span>
        </div>
        <div class="header-right">
            <!-- Log-in / Sign-up open a popup for now (see script.js) -->
            <div class="header-actions auth-buttons-group">
                <button type="button" class="btn btn-primary btn-sm" onclick="openAuthModal('login')">LOG-IN</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="openAuthModal('signup')">SIGN-UP</button>
            </div>
            <button type="button" class="profile-icon-btn" aria-label="Open your dashboard">
                <img class="profile-icon-img" src="images/default icon.png" alt="Your account">
            </button>
            <input type="file" id="avatar-file-input" accept="image/*" hidden>
            <nav>
            <!-- nav links -->
            <ul>
                <li><a href="#" class="active" onclick="return false;">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            </nav>
        </div>
    </header>

    <!-- ================= HERO ================= -->
    <div class="hero">
        <h1>RAW IDEAS.<br><span>HIGH-IMPACT VISUALS</span></h1>
    </div>

    <!-- ================= SERVICES ================= -->
    <section class="services" id="services">
        <h2 class="section-title">Services Overview</h2>
        <div class="services-grid">

            <div class="service-card">
                <img class="service-icon" src="images/icon1.png" alt="Production icon">
                <h3>PRODUCTION</h3>
                <p>Professional photoshoot execution tailored for commercial brands, personal portraits, and events. We handle high-end studio lighting, camera setups, frame composition and art direction to capture crisp, high-resolution source imagery.</p>
            </div>

            <div class="service-card">
                <img class="service-icon" src="images/icon2.png" alt="Editing icon">
                <h3>EDITING</h3>
                <p>Translating raw photo captures into a curated, cohesive collection. We review and cull the best frames, apply primary cropping and alignment, and perform initial tone adjustments so every image flows seamlessly together.</p>
            </div>

            <div class="service-card">
                <img class="service-icon" src="images/icon3.png" alt="Post-production icon">
                <h3>POST-PRODUCTION</h3>
                <p>Fine-tuning your images into polished, print- and web-ready assets. From precise skin and surface retouching to advanced color grading and background manipulation, we make sure every detail meets commercial publishing standards.</p>
            </div>

        </div>
    </section>

    <!-- ================= FEATURED WORKS ================= -->
    <section class="works" id="gallery">
        <h2 class="section-title">Featured Works</h2>
        <div class="works-grid">
            <div class="work-item"><img class="zoomable" src="images/image1.jpg" alt="Featured work 1"></div>
            <div class="work-item"><img class="zoomable" src="images/image2.jpg" alt="Featured work 2"></div>
            <div class="work-item"><img class="zoomable" src="images/image3.jpg" alt="Featured work 3"></div>
            <div class="work-item"><img class="zoomable" src="images/image4.jpg" alt="Featured work 4"></div>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer id="contact">
        <h4 class="footer-cta-heading">CONTACT US</h4>
        <div class="footer-top">

            <div class="footer-col">
                <h4 class="footer-heading-desc">GET IN TOUCH</h4>
                <ul class="footer-contact">
                    <li>
                        <img src="images/contact1.png" alt="Location icon">
                        3rd Floor, Leon Kilat Mall, Negros Oriental
                    </li>
                    <li>
                        <img src="images/contact2.png" alt="Phone icon">
                        +63 912 345 6789
                    </li>
                    <li>
                        <img src="images/contact3.png" alt="Email icon">
                        TrashPandaStudio@gmail.com
                    </li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-heading-desc">SOCIAL MEDIA</h4>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook" onclick="showWipToast(event)">
                        <img src="images/contact5.png" alt="Facebook">
                    </a>
                    <a href="#" aria-label="YouTube" onclick="showWipToast(event)">
                        <img src="images/contact6.png" alt="YouTube">
                    </a>
                    <a href="#" aria-label="Instagram" onclick="showWipToast(event)">
                        <img src="images/contact4.png" alt="Instagram">
                    </a>
                </div>
            </div>

            <div class="footer-col footer-cta">
                <div class="cta-buttons auth-buttons-group">
                    <button type="button" class="btn btn-primary" onclick="openAuthModal('login')">LOG-IN</button>
                    <button type="button" class="btn btn-secondary" onclick="openAuthModal('signup')">SIGN-UP</button>
                </div>
                <button type="button" class="profile-icon-btn" aria-label="Open your dashboard">
                    <img class="profile-icon-img" src="images/default icon.png" alt="Your account">
                </button>
            </div>

        </div>
    </footer>

    <!-- ================= LOG-IN / SIGN-UP MODALS ================= -->
    <div class="modal-overlay" id="login-modal">
        <div class="auth-card">
            <button class="modal-close" aria-label="Close" onclick="closeAuthModal('login')">&times;</button>
            <h2 class="auth-title"><span class="brand">TRASHPANDASTUDIO</span><span class="action">/LOG-IN</span></h2>

            <input type="email" id="login-username" class="field-input" placeholder="EMAIL">
            <p class="field-message hidden" id="login-username-error"></p>

            <input type="password" id="login-password" class="field-input" placeholder="PASSWORD">
            <p class="field-message hidden" id="login-password-error"></p>

            <input type="password" id="login-confirm-password" class="field-input" placeholder="CONFIRM PASSWORD">
            <p class="field-message hidden" id="login-confirm-error"></p>

            <button type="button" class="btn btn-primary form-submit" id="login-submit">LOG-IN</button>

            <div class="auth-links">
                <a href="#" onclick="showWipToast(event)">Forgot Password?</a>
            </div>
            <div class="auth-divider">OR</div>
            <div class="auth-links">
                <a href="#" onclick="switchAuthModal(event, 'signup')">CREATE AN ACCOUNT</a>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="signup-modal">
        <div class="auth-card auth-card-wide">
            <button class="modal-close" aria-label="Close" onclick="closeAuthModal('signup')">&times;</button>
            <h2 class="auth-title"><span class="brand">TRASHPANDASTUDIO</span><span class="action">/SIGN-IN</span></h2>

            <input type="text" id="signup-first-name" class="field-input" placeholder="FIRST NAME">
            <input type="text" id="signup-last-name" class="field-input" placeholder="LAST NAME">

            <input type="email" id="signup-email" class="field-input" placeholder="EMAIL ADDRESS">
            <p class="field-message hidden" id="signup-email-error"></p>

            <input type="password" id="signup-password" class="field-input" placeholder="PASSWORD">
            <input type="password" id="signup-confirm-password" class="field-input" placeholder="CONFIRM PASSWORD">
            <p class="field-message hidden" id="signup-password-error"></p>

            <input type="tel" id="signup-phone" class="field-input" placeholder="PHONE NUMBER">
            <input type="text" id="signup-address" class="field-input" placeholder="ADDRESS">
            <input type="text" id="signup-city-state-zip" class="field-input" placeholder="CITY, STATE, ZIP">

            <select id="signup-payment-method" class="field-input" required>
                <option value="" disabled selected hidden>PAYMENT METHOD</option>
                <option value="paypal">PayPal</option>
                <option value="gcash">GCash</option>
                <option value="cash">Cash in Person</option>
            </select>

            <div class="auth-terms">
                <input type="checkbox" id="signup-terms">
                <label for="signup-terms">
                    ACCEPT <a href="terms.php" target="_blank">TERMS &amp; CONDITIONS</a>
                    <span class="auth-terms-sub"><a href="privacy.php" target="_blank">Terms of Service &amp; Privacy Policy</a></span>
                </label>
            </div>
            <p class="field-message hidden" id="signup-terms-error">You must accept the Terms &amp; Conditions to continue.</p>

            <button type="button" class="btn btn-primary form-submit" id="signup-submit">SIGN-UP</button>

            <div class="auth-links">
                <a href="#" onclick="switchAuthModal(event, 'login')">Already have an account? LOG-IN</a>
            </div>
        </div>
    </div>

    <!-- ================= ADMIN CONSOLE OVERLAY ================= -->
    <div class="dashboard-overlay" id="admin-dashboard">
      <div class="dashboard-sheet">
        <div class="dashboard-header">
            <h2 class="dashboard-title"><span class="brand">TRASHPANDASTUDIO</span><span class="action">/ADMIN CONSOLE</span></h2>
            <div class="dashboard-header-icons">
                <button type="button" class="btn btn-secondary btn-sm" onclick="logOut()">LOG OUT</button>
                <button type="button" class="dashboard-icon-btn" aria-label="Notifications" onclick="showWipToast(event)">
                    <img src="images/notification icon.png" alt="Notifications">
                </button>
                <button type="button" class="dashboard-icon-btn" aria-label="Close" onclick="closeDashboard('admin')">
                    <img src="images/exit icon.png" alt="Close" onerror="this.replaceWith(document.createTextNode('\u2715'))">
                </button>
            </div>
        </div>

        <div class="admin-dash-grid">
            <div class="dash-panel">
                <div class="dash-panel-label">System Overview</div>
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-label">Total Registered Users</div>
                        <div class="stat-value" id="stat-total-users">&#8212;</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">New Sign Ups</div>
                        <div class="stat-value" id="stat-new-signups">&#8212;</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Total Logins</div>
                        <div class="stat-value" id="stat-total-logins">&#8212;</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Active Projects</div>
                        <div class="stat-value" id="stat-active-projects">&#8212;</div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-avatar-lg">
                    <img class="dash-avatar-img" src="images/default icon.png" alt="Admin avatar">
                </div>
                <button type="button" class="btn btn-primary" onclick="showWipToast(event)">MY PROFILE</button>
            </div>

            <div class="dash-panel dash-panel-wide">
                <div class="dash-panel-label">Project Brief Management</div>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Brief name</th>
                            <th>Current Status</th>
                        </tr>
                    </thead>
                    <tbody id="admin-briefs-tbody">
                        <tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>
                    </tbody>
                </table>
                <div style="display:flex; justify-content:flex-end; margin-top:20px;">
                    <button type="button" class="btn btn-primary" onclick="showWipToast(event)">Manage All Projects</button>
                </div>
            </div>

            <div class="dash-panel dash-panel-wide">
                <div class="dash-panel-label">Registered Users</div>
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody id="admin-users-tbody">
                        <tr><td colspan="4" class="dash-empty-row">No data yet.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
      </div>
    </div>

    <!-- ================= USER DASHBOARD OVERLAY ================= -->
    <div class="dashboard-overlay" id="user-dashboard">
      <div class="dashboard-sheet">
        <div class="dashboard-header">
            <h2 class="dashboard-title"><span class="brand">TRASHPANDASTUDIO</span><span class="action">/USER DASHBOARD</span></h2>
            <div class="dashboard-header-icons">
                <button type="button" class="btn btn-secondary btn-sm" onclick="logOut()">LOG OUT</button>
                <button type="button" class="dashboard-icon-btn" aria-label="Notifications" onclick="showWipToast(event)">
                    <img src="images/notification icon.png" alt="Notifications">
                </button>
                <button type="button" class="dashboard-icon-btn" aria-label="Close" onclick="closeDashboard('user')">
                    <img src="images/exit icon.png" alt="Close" onerror="this.replaceWith(document.createTextNode('\u2715'))">
                </button>
            </div>
        </div>

        <h3 class="dashboard-welcome">WELCOME BACK, <span class="accent user-dash-firstname">[USER NAME]</span></h3>

        <div class="user-dash-grid">
            <div class="dash-panel">
                <div class="dash-panel-label">My Recent Activity</div>
                <div class="dash-scroll">
                    <table class="dash-table dash-table-fill-head">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="user-activity-tbody">
                            <tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="dash-panel">
                <div class="dash-panel-label">My Project</div>
                <div class="dash-scroll">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Brief Title</th>
                                <th>Project Desc.</th>
                            </tr>
                        </thead>
                        <tbody id="user-project-tbody">
                            <tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="profile-card dash-sidebar" id="my-account-section">
                <div class="profile-avatar-lg">
                    <img class="dash-avatar-img" src="images/default icon.png" alt="User avatar">
                </div>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('my-account-section').scrollIntoView({behavior:'smooth'})">MY PROFILE</button>

                <div style="width:100%;">
                    <div class="dash-panel-label" style="margin-top:20px;">My Account (Edit)</div>

                    <label class="account-edit-label" for="account-email-input">Update Email</label>
                    <input type="email" id="account-email-input" class="field-input" placeholder="you@example.com">

                    <label class="account-edit-label" for="account-password-input">Update Password</label>
                    <input type="password" id="account-password-input" class="field-input" placeholder="New password">

                    <p class="field-message hidden" id="account-save-message"></p>

                    <button type="button" class="btn btn-primary form-submit" id="account-save-btn">SAVE CHANGES</button>
                </div>
            </div>

            <div class="dash-panel">
                <div class="dash-panel-label">Messages / Updates</div>
                <div class="dash-scroll">
                    <ul class="updates-list updates-list-empty">
                        <li>No updates yet.</li>
                    </ul>
                </div>
            </div>

            <div class="dash-panel">
                <div class="dash-panel-label">Payment History</div>
                <div class="dash-scroll">
                    <table class="dash-table">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Order ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
      </div>
    </div>

    <!-- ================= WORK-IN-PROGRESS TOAST ================= -->
    <div id="wip-toast" role="status" aria-live="polite">This section is still a work in progress — check back soon!</div>

    <!-- ================= LIGHTBOX (click any photo to view full-size) ================= -->
    <div id="lightbox-overlay">
        <button id="lightbox-close" aria-label="Close" onclick="closeLightbox()">&times;</button>
        <img id="lightbox-img" src="" alt="">
    </div>

    <!-- ================= BACK TO TOP ================= -->
    <button id="back-to-top" aria-label="Back to top" title="Back to top">&#8593;</button>

    <script src="script.js"></script>

</body>
</html>