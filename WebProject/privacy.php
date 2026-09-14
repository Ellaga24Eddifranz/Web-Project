<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service & Privacy Policy | TrashPandaStudio</title>
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
            <div class="header-actions">
                <button type="button" class="btn btn-primary btn-sm" onclick="openAuthModal('login')">LOG-IN</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="openAuthModal('signup')">SIGN-UP</button>
            </div>
            <nav>
            <ul>
                <li><a href="Web2.php">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
            </nav>
        </div>
    </header>

    <!-- ================= TERMS OF SERVICE & PRIVACY POLICY ================= -->
    <section class="legal-section">
        <h2>Terms of Service &amp; Privacy Policy</h2>
        <p class="legal-updated">Placeholder content — last updated: not yet published</p>

        <div class="legal-placeholder-notice">
            This is placeholder text so the Sign-Up flow has somewhere real to link to. Swap this out for your studio's actual reviewed Terms of Service and Privacy Policy before launch — especially before collecting real customer data.
        </div>

        <h3>1. Information We Collect</h3>
        <p>When you create an account, we collect information such as your name, email address, phone number, mailing address, and preferred payment method.</p>

        <h3>2. How We Use Your Information</h3>
        <p>Your information is used to manage your account, communicate about your projects, and process payments for services rendered.</p>

        <h3>3. Data Storage</h3>
        <p>Describe here how and where account data is stored, how long it's retained, and what security measures protect it.</p>

        <h3>4. Third Parties</h3>
        <p>List any third-party services (payment processors, analytics, etc.) that may receive user data, and why.</p>

        <h3>5. Your Rights</h3>
        <p>Explain how users can access, correct, or delete their account information, and how to contact you with privacy questions.</p>
    </section>

    <!-- ================= SIMPLE BRAND FOOTER ================= -->
    <footer class="brand-footer">
        <p>TrashPandaStudio.com</p>
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

    <!-- ================= BACK TO TOP ================= -->
    <button id="back-to-top" aria-label="Back to top" title="Back to top">&#8593;</button>

    <script src="script.js"></script>

</body>
</html>