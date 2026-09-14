let wipTimer = null;

// used to check email formats on any form on the site
const EMAIL_PATTERN = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Log-In / Sign-Up talk to login.php / signup.php / config.php, a small
// PHP + MySQL backend running under XAMPP. Passwords are hashed with
// password_hash() — we never store or check the real password.

function showWipToast(event) {
    if (event) event.preventDefault();
    const toast = document.getElementById('wip-toast');
    toast.classList.add('show');
    clearTimeout(wipTimer);
    wipTimer = setTimeout(() => {
        toast.classList.remove('show');
    }, 2800);
}

/* ---------- Log-In / Sign-Up popup ----------
   Opens the Log-In or Sign-Up card on top of the current page instead
   of going to a different page. */
function openAuthModal(type) {
    const modal = document.getElementById(type + '-modal');
    if (!modal) return;
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeAuthModal(type) {
    const modal = document.getElementById(type + '-modal');
    if (modal) modal.classList.remove('show');
    document.body.style.overflow = '';
}

function switchAuthModal(event, toType) {
    if (event) event.preventDefault();
    closeAuthModal(toType === 'login' ? 'signup' : 'login');
    openAuthModal(toType);
}

/* ---------- Who's logged in ----------
   session-check.php is the single source of truth for who's logged in.
   currentUser is just a copy of that in memory so we don't have to
   re-fetch it constantly. It resets on every page load and gets
   refreshed by checkSession() below. */
let currentUser = null;

// asks the server "am I logged in?" using the session cookie
async function checkSession() {
    try {
        const response = await fetch('session-check.php', {
            method: 'GET',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await response.json();

        currentUser = data.loggedIn ? data.user : null;
    } catch (err) {
        console.error('checkSession failed:', err);
        currentUser = null;
    }

    refreshAuthHeaderUI();
    return currentUser;
}

// swaps the LOG-IN/SIGN-UP buttons for the profile icon, and back again
function refreshAuthHeaderUI() {
    document.querySelectorAll('.auth-buttons-group').forEach((el) => {
        el.style.display = currentUser ? 'none' : '';
    });

    document.querySelectorAll('.profile-icon-btn').forEach((btn) => {
        btn.classList.toggle('show', Boolean(currentUser));
    });

    updateAvatarImages();
}

// sets every profile picture on the page to whatever the current
// account actually has saved — or the default icon if logged out or
// no picture was ever uploaded. Called any time currentUser changes,
// so a picture never carries over from one account to a different one.
function updateAvatarImages() {
    const src = (currentUser && currentUser.avatarPath) ? currentUser.avatarPath : 'images/default icon.png';
    document.querySelectorAll('.profile-icon-img').forEach((img) => { img.src = src; });
    document.querySelectorAll('.dash-avatar-img').forEach((img) => { img.src = src; });
}

/* ---------- Admin Console / User Dashboard popup ----------
   Opens on top of the current page. The X button just closes it, it
   does not log you out. */
function openDashboard(role) {
    if (!currentUser) return; // no login, no dashboard

    const id = role === 'admin' ? 'admin-dashboard' : 'user-dashboard';
    const overlay = document.getElementById(id);
    if (!overlay) return;

    if (role !== 'admin') {
        overlay.querySelectorAll('.user-dash-firstname').forEach((el) => {
            el.textContent = currentUser.firstName;
        });
    }

    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';

    if (role === 'admin') {
        loadAdminDashboardData();
    } else {
        loadUserDashboardData();
    }
}

function closeDashboard(role) {
    const id = role === 'admin' ? 'admin-dashboard' : 'user-dashboard';
    const overlay = document.getElementById(id);
    if (overlay) overlay.classList.remove('show');
    document.body.style.overflow = '';
}

// ends the real session on the server and resets the page back to logged out
async function logOut() {
    try {
        await fetch('logout.php', { method: 'POST', credentials: 'same-origin' });
    } catch (err) {
        console.error('logout.php request failed:', err);
    }
    currentUser = null;
    closeDashboard('admin');
    closeDashboard('user');
    refreshAuthHeaderUI(); // this also resets every avatar picture back to default
}

/* ---------- Admin Console: pulling real data ----------
   admin-data.php only ever answers with real data if the session's
   role is 'admin' — anyone else gets refused, even if they call this
   URL directly. */
async function loadAdminDashboardData() {
    try {
        const response = await fetch('admin-data.php', { method: 'GET', credentials: 'same-origin' });
        if (!response.ok) return;
        const data = await response.json();

        const statMap = {
            'stat-total-users': data.stats.totalUsers,
            'stat-new-signups': data.stats.newSignups,
            'stat-total-logins': data.stats.totalLogins,
            'stat-active-projects': data.stats.activeProjects
        };
        Object.entries(statMap).forEach(([id, value]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value;
        });

        const usersTbody = document.getElementById('admin-users-tbody');
        if (usersTbody) {
            usersTbody.innerHTML = '';
            if (data.users.length === 0) {
                usersTbody.innerHTML = '<tr><td colspan="4" class="dash-empty-row">No data yet.</td></tr>';
            } else {
                data.users.forEach((user) => {
                    const tr = document.createElement('tr');
                    [user.name, user.email, user.role, user.joined].forEach((value) => {
                        const td = document.createElement('td');
                        td.textContent = value;
                        tr.appendChild(td);
                    });
                    usersTbody.appendChild(tr);
                });
            }
        }

        const briefsTbody = document.getElementById('admin-briefs-tbody');
        if (briefsTbody) {
            briefsTbody.innerHTML = '';
            if (data.briefs.length === 0) {
                briefsTbody.innerHTML = '<tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>';
            } else {
                data.briefs.forEach((brief) => {
                    const tr = document.createElement('tr');
                    const nameTd = document.createElement('td');
                    nameTd.textContent = brief.title;
                    const statusTd = document.createElement('td');
                    statusTd.textContent = brief.status;
                    if (brief.status !== 'Completed') statusTd.classList.add('status-pending');
                    tr.appendChild(nameTd);
                    tr.appendChild(statusTd);
                    briefsTbody.appendChild(tr);
                });
            }
        }
    } catch (err) {
        console.error('loadAdminDashboardData failed:', err);
    }
}

/* ---------- User Dashboard: pulling real data ----------
   user-data.php always answers with only the logged-in account's own
   data — never anyone else's. */
async function loadUserDashboardData() {
    try {
        const response = await fetch('user-data.php', { method: 'GET', credentials: 'same-origin' });
        if (!response.ok) return;
        const data = await response.json();

        const activityTbody = document.getElementById('user-activity-tbody');
        if (activityTbody) {
            activityTbody.innerHTML = '';
            if (data.activity.length === 0) {
                activityTbody.innerHTML = '<tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>';
            } else {
                data.activity.forEach((item) => {
                    const tr = document.createElement('tr');
                    const actTd = document.createElement('td');
                    actTd.textContent = item.activity;
                    const dateTd = document.createElement('td');
                    dateTd.textContent = item.date;
                    tr.appendChild(actTd);
                    tr.appendChild(dateTd);
                    activityTbody.appendChild(tr);
                });
            }
        }

        const projectTbody = document.getElementById('user-project-tbody');
        if (projectTbody) {
            projectTbody.innerHTML = '';
            if (data.projects.length === 0) {
                projectTbody.innerHTML = '<tr><td colspan="2" class="dash-empty-row">No data yet.</td></tr>';
            } else {
                data.projects.forEach((project) => {
                    const tr = document.createElement('tr');
                    const titleTd = document.createElement('td');
                    titleTd.textContent = project.title;
                    const descTd = document.createElement('td');
                    descTd.textContent = project.description;
                    tr.appendChild(titleTd);
                    tr.appendChild(descTd);
                    projectTbody.appendChild(tr);
                });
            }
        }
    } catch (err) {
        console.error('loadUserDashboardData failed:', err);
    }
}

/* ---------- Lightbox: click a photo to see it bigger ----------
   Any <img class="zoomable"> gets this automatically. */
function openLightbox(src, alt) {
    const overlay = document.getElementById('lightbox-overlay');
    const img = document.getElementById('lightbox-img');
    if (!overlay || !img) return;
    img.src = src;
    img.alt = alt || '';
    overlay.classList.add('show');
}

function closeLightbox() {
    const overlay = document.getElementById('lightbox-overlay');
    if (overlay) overlay.classList.remove('show');
}

document.addEventListener('DOMContentLoaded', () => {
    checkSession(); // ask the server if we're logged in

    document.querySelectorAll('img.zoomable').forEach((img) => {
        img.addEventListener('click', () => openLightbox(img.src, img.alt));
    });

    const overlay = document.getElementById('lightbox-overlay');
    if (overlay) {
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) closeLightbox();
        });
    }

    document.querySelectorAll('.modal-overlay').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    document.querySelectorAll('.dashboard-overlay').forEach((overlay) => {
        overlay.addEventListener('click', (event) => {
            if (event.target === overlay) {
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    /* ---------- Profile icon opens the dashboard ---------- */
    document.querySelectorAll('.profile-icon-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (currentUser) openDashboard(currentUser.role);
        });
    });

    /* ---------- Clicking the dashboard avatar uploads a new picture ----------
       Sends the picture to upload-avatar.php, which saves it against
       the logged-in account. This is what makes the picture stick to
       the right account instead of leaking to whoever logs in next. */
    const avatarFileInput = document.getElementById('avatar-file-input');
    if (avatarFileInput) {
        document.querySelectorAll('.profile-avatar-lg').forEach((avatarWrap) => {
            avatarWrap.style.cursor = 'pointer';
            avatarWrap.addEventListener('click', () => avatarFileInput.click());
        });

        avatarFileInput.addEventListener('change', async () => {
            const file = avatarFileInput.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please choose an image file.');
                avatarFileInput.value = '';
                return;
            }

            if (!currentUser) {
                avatarFileInput.value = '';
                return;
            }

            const formData = new FormData();
            formData.append('avatar', file);

            try {
                const response = await fetch('upload-avatar.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });
                const data = await response.json();

                if (!response.ok) {
                    alert(data.message || 'Could not upload the image.');
                    return;
                }

                currentUser.avatarPath = data.avatarPath;
                updateAvatarImages();
            } catch (err) {
                alert('Could not reach the server to save the picture.');
            } finally {
                avatarFileInput.value = '';
            }
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeLightbox();
            document.querySelectorAll('.modal-overlay.show, .dashboard-overlay.show').forEach((el) => {
                el.classList.remove('show');
            });
            document.body.style.overflow = '';
        }
    });

    /* ---------- User Dashboard: My Account (Edit) ----------
       This still only updates the on-screen copy — there's no backend
       yet to actually change the email in the database. Wire that up
       before relying on this for real. */
    const accountSaveBtn = document.getElementById('account-save-btn');
    if (accountSaveBtn) {
        accountSaveBtn.addEventListener('click', () => {
            const emailInput = document.getElementById('account-email-input');
            const passwordInput = document.getElementById('account-password-input');
            const messageEl = document.getElementById('account-save-message');

            if (emailInput.value.trim() && !EMAIL_PATTERN.test(emailInput.value.trim())) {
                emailInput.classList.add('field-error');
                return;
            }
            emailInput.classList.remove('field-error');

            if (currentUser && emailInput.value.trim()) {
                currentUser.email = emailInput.value.trim().toLowerCase();
            }

            if (messageEl) {
                messageEl.textContent = 'Saved locally — no backend endpoint yet to update the database.';
                messageEl.classList.remove('hidden');
            }
            passwordInput.value = '';
        });
    }

    /* ---------- Back-to-top button ---------- */
    const backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        const toggleBackToTop = () => {
            if (window.scrollY > 400) {
                backToTop.classList.add('show');
            } else {
                backToTop.classList.remove('show');
            }
        };
        window.addEventListener('scroll', toggleBackToTop);
        toggleBackToTop();

        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ---------- Project Brief form (Services page) ----------
       Needs to be logged in — the brief gets tied to that account so
       it shows up in the admin's brief table and the user's own
       dashboard. Actually uploads to submit-brief.php now. */
    const dropzone = document.getElementById('dropzone');
    if (dropzone) {
        const fileInput = document.getElementById('brief-file-input');
        const fileListEl = document.getElementById('brief-file-list');
        const titleInput = document.getElementById('brief-title');
        const descInput = document.getElementById('brief-desc');
        const submitBtn = document.getElementById('brief-submit');

        const ACCEPTED_TYPES = ['image/jpeg', 'image/png', 'video/mp4'];
        const MAX_SIZE_BYTES = 500 * 1024 * 1024; // 500MB
        let selectedFiles = [];

        function renderFileList() {
            fileListEl.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const chip = document.createElement('span');
                chip.className = 'file-chip';
                chip.textContent = file.name;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.textContent = '\u00d7';
                removeBtn.setAttribute('aria-label', 'Remove ' + file.name);
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectedFiles.splice(index, 1);
                    renderFileList();
                });

                chip.appendChild(removeBtn);
                fileListEl.appendChild(chip);
            });
        }

        function addFiles(fileArray) {
            const errors = [];
            fileArray.forEach((file) => {
                if (!ACCEPTED_TYPES.includes(file.type)) {
                    errors.push(file.name + ' — not a JPG, PNG, or MP4 file.');
                    return;
                }
                if (file.size > MAX_SIZE_BYTES) {
                    errors.push(file.name + ' — over the 500MB limit.');
                    return;
                }
                selectedFiles.push(file);
            });
            renderFileList();
            if (selectedFiles.length > 0) {
                dropzone.classList.remove('field-error');
            }
            if (errors.length) {
                alert(errors.join('\n'));
            }
        }

        dropzone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', () => {
            addFiles(Array.from(fileInput.files));
            fileInput.value = '';
        });

        ['dragenter', 'dragover'].forEach((evtName) => {
            dropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                dropzone.classList.add('dragover');
            });
        });

        ['dragleave', 'drop'].forEach((evtName) => {
            dropzone.addEventListener(evtName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files) {
                addFiles(Array.from(e.dataTransfer.files));
            }
        });

        if (submitBtn) {
            submitBtn.addEventListener('click', async (event) => {
                event.preventDefault();

                if (!currentUser) {
                    alert('Please log in first to submit a project brief.');
                    openAuthModal('login');
                    return;
                }

                let valid = true;

                [titleInput, descInput].forEach((field) => {
                    if (!field.value.trim()) {
                        field.classList.add('field-error');
                        valid = false;
                    } else {
                        field.classList.remove('field-error');
                    }
                });

                if (selectedFiles.length === 0) {
                    dropzone.classList.add('field-error');
                    valid = false;
                } else {
                    dropzone.classList.remove('field-error');
                }

                if (!valid) return;

                const formData = new FormData();
                formData.append('title', titleInput.value.trim());
                formData.append('description', descInput.value.trim());
                selectedFiles.forEach((file) => formData.append('files[]', file));

                submitBtn.disabled = true;

                try {
                    const response = await fetch('submit-brief.php', {
                        method: 'POST',
                        credentials: 'same-origin',
                        body: formData
                    });
                    const data = await response.json();

                    if (!response.ok) {
                        alert(data.message || 'Something went wrong submitting your brief.');
                        return;
                    }

                    alert('Your project brief has been submitted! You can track it from your dashboard.');
                    titleInput.value = '';
                    descInput.value = '';
                    selectedFiles = [];
                    renderFileList();
                } catch (err) {
                    alert('Could not reach the server. Make sure Apache and MySQL are running in XAMPP.');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        }
    }

    /* ---------- Contact form (Contact page) ----------
       No backend yet to actually send the message, so this just shows
       the WIP toast once the fields are valid. */
    const contactSend = document.getElementById('contact-send');
    if (contactSend) {
        const nameInput = document.getElementById('contact-name');
        const emailInput = document.getElementById('contact-email');
        const messageInput = document.getElementById('contact-message');

        contactSend.addEventListener('click', (event) => {
            let valid = true;

            if (!nameInput.value.trim()) {
                nameInput.classList.add('field-error');
                valid = false;
            } else {
                nameInput.classList.remove('field-error');
            }

            if (!EMAIL_PATTERN.test(emailInput.value.trim())) {
                emailInput.classList.add('field-error');
                valid = false;
            } else {
                emailInput.classList.remove('field-error');
            }

            if (!messageInput.value.trim()) {
                messageInput.classList.add('field-error');
                valid = false;
            } else {
                messageInput.classList.remove('field-error');
            }

            if (!valid) return;

            showWipToast(event);
        });
    }

    /* ---------- Sign-Up form ----------
       Validates the fields, then sends them to signup.php to create
       the account, then opens the Log-In popup. */
    const signupSubmit = document.getElementById('signup-submit');
    if (signupSubmit) {
        const firstNameInput = document.getElementById('signup-first-name');
        const lastNameInput = document.getElementById('signup-last-name');
        const emailInput = document.getElementById('signup-email');
        const emailError = document.getElementById('signup-email-error');
        const passwordInput = document.getElementById('signup-password');
        const confirmInput = document.getElementById('signup-confirm-password');
        const passwordError = document.getElementById('signup-password-error');
        const phoneInput = document.getElementById('signup-phone');
        const addressInput = document.getElementById('signup-address');
        const cityStateZipInput = document.getElementById('signup-city-state-zip');
        const paymentSelect = document.getElementById('signup-payment-method');
        const termsCheckbox = document.getElementById('signup-terms');
        const termsError = document.getElementById('signup-terms-error');

        const requiredTextFields = [
            firstNameInput, lastNameInput, phoneInput, addressInput, cityStateZipInput
        ];

        signupSubmit.addEventListener('click', async () => {
            let valid = true;

            [firstNameInput, lastNameInput, emailInput, passwordInput,
                confirmInput, phoneInput, addressInput, cityStateZipInput,
                paymentSelect].forEach((el) => el.classList.remove('field-error'));
            [emailError, passwordError].forEach((el) => {
                el.classList.add('hidden');
                el.textContent = '';
            });
            termsError.classList.add('hidden');

            requiredTextFields.forEach((field) => {
                if (!field.value.trim()) {
                    field.classList.add('field-error');
                    valid = false;
                }
            });

            if (!EMAIL_PATTERN.test(emailInput.value.trim())) {
                emailInput.classList.add('field-error');
                emailError.textContent = 'Please enter a valid email address.';
                emailError.classList.remove('hidden');
                valid = false;
            }

            if (!passwordInput.value) {
                passwordInput.classList.add('field-error');
                valid = false;
            }
            if (!confirmInput.value) {
                confirmInput.classList.add('field-error');
                valid = false;
            }
            if (passwordInput.value && confirmInput.value && passwordInput.value !== confirmInput.value) {
                passwordInput.classList.add('field-error');
                confirmInput.classList.add('field-error');
                passwordError.textContent = 'Passwords do not match.';
                passwordError.classList.remove('hidden');
                valid = false;
            }

            if (!paymentSelect.value) {
                paymentSelect.classList.add('field-error');
                valid = false;
            }

            if (!termsCheckbox.checked) {
                termsError.classList.remove('hidden');
                valid = false;
            }

            if (!valid) return;

            signupSubmit.disabled = true;

            try {
                const response = await fetch('signup.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        firstName: firstNameInput.value.trim(),
                        lastName: lastNameInput.value.trim(),
                        email: emailInput.value.trim(),
                        password: passwordInput.value,
                        phone: phoneInput.value.trim(),
                        address: addressInput.value.trim(),
                        cityStateZip: cityStateZipInput.value.trim(),
                        paymentMethod: paymentSelect.value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 409) {
                        emailInput.classList.add('field-error');
                        emailError.textContent = data.error || 'An account with this email already exists.';
                        emailError.classList.remove('hidden');
                    } else {
                        alert(data.error || 'Something went wrong. Please try again.');
                    }
                    return;
                }

                alert('Account created! Please log in.');
                switchAuthModal(null, 'login');
            } catch (err) {
                alert('Could not reach the server. Make sure Apache and MySQL are running in XAMPP and this page is being served from localhost (not opened as a file://).');
            } finally {
                signupSubmit.disabled = false;
            }
        });
    }

    /* ---------- Log-In form ----------
       Checks the email + password against login.php. */
    const loginSubmit = document.getElementById('login-submit');
    if (loginSubmit) {
        const usernameInput = document.getElementById('login-username');
        const passwordInput = document.getElementById('login-password');
        const confirmInput = document.getElementById('login-confirm-password');
        const usernameError = document.getElementById('login-username-error');
        const passwordError = document.getElementById('login-password-error');
        const confirmError = document.getElementById('login-confirm-error');

        loginSubmit.addEventListener('click', async () => {
            [usernameInput, passwordInput, confirmInput].forEach((el) => el.classList.remove('field-error'));
            [usernameError, passwordError, confirmError].forEach((el) => {
                el.classList.add('hidden');
                el.textContent = '';
            });

            let valid = true;
            if (!usernameInput.value.trim()) {
                usernameInput.classList.add('field-error');
                valid = false;
            }
            if (!passwordInput.value) {
                passwordInput.classList.add('field-error');
                valid = false;
            }
            if (!confirmInput.value) {
                confirmInput.classList.add('field-error');
                valid = false;
            }
            if (!valid) return;

            if (passwordInput.value !== confirmInput.value) {
                passwordInput.classList.add('field-error');
                confirmInput.classList.add('field-error');
                confirmError.textContent = 'Passwords do not match.';
                confirmError.classList.remove('hidden');
                return;
            }

            loginSubmit.disabled = true;

            try {
                const response = await fetch('login.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        username: usernameInput.value.trim(),
                        password: passwordInput.value
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.error === 'NO_ACCOUNT') {
                        usernameInput.classList.add('field-error');
                        usernameError.innerHTML = 'No account found for this username. <a href="#" onclick="switchAuthModal(event, \'signup\')">Create one now</a>.';
                        usernameError.classList.remove('hidden');
                    } else if (data.error === 'WRONG_PASSWORD') {
                        passwordInput.classList.add('field-error');
                        passwordError.textContent = 'Incorrect password. Please try again.';
                        passwordError.classList.remove('hidden');
                    } else {
                        alert(data.message || 'Something went wrong. Please try again.');
                    }
                    return;
                }

                // login.php already started the real session by this point —
                // just update our copy so the page reacts right away.
                const role = data.user.role === 'admin' ? 'admin' : 'user';

                currentUser = {
                    firstName: data.user.firstName,
                    lastName: data.user.lastName,
                    email: data.user.email,
                    role: role,
                    avatarPath: data.user.avatarPath || null
                };
                refreshAuthHeaderUI();
                closeAuthModal('login');
                openDashboard(role);
            } catch (err) {
                alert('Could not reach the server. Make sure Apache and MySQL are running in XAMPP and this page is being served from localhost (not opened as a file://).');
            } finally {
                loginSubmit.disabled = false;
            }
        });
    }
});