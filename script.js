document.addEventListener('DOMContentLoaded', () => {
    // --- Page Navigation ---
    const navHome = document.getElementById('nav-home');
    const navSettings = document.getElementById('nav-settings');
    const homePage = document.getElementById('home-page');
    const settingsPage = document.getElementById('settings-page');
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');

    const showPage = (pageToShow) => {
        [homePage, settingsPage].forEach(page => page.classList.add('hidden'));
        pageToShow.classList.remove('hidden');

        [navHome, navSettings].forEach(link => link.classList.remove('active-link'));
        if (pageToShow === homePage) {
            navHome.classList.add('active-link');
        } else {
            navSettings.classList.add('active-link');
        }
    };

    navHome.addEventListener('click', (e) => {
        e.preventDefault();
        showPage(homePage);
    });

    navSettings.addEventListener('click', (e) => {
        e.preventDefault();
        showPage(settingsPage);
    });

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('toggled');
    });

    // --- Settings Page Elements ---
    const regionSelect = document.getElementById('region');
    const customBaseUrlContainer = document.getElementById('custom-base-url-container');
    const customBaseUrlInput = document.getElementById('customBaseUrl');
    const credentialTypeRadios = document.querySelectorAll('input[name="credentialType"]');
    const clientIdInput = document.getElementById('clientId');
    const clientSecretInput = document.getElementById('clientSecret');
    const redirectUriContainer = document.getElementById('redirect-uri-container');
    const redirectUriInput = document.getElementById('redirectUri');
    const saveBtn = document.getElementById('save-config-btn');
    const clearBtn = document.getElementById('clear-config-btn');
    const loginBtn = document.getElementById('login-btn');
    const testBtn = document.getElementById('test-auth-btn');
    const settingsMessage = document.getElementById('settings-message');
    const authStatusBadge = document.getElementById('auth-status-badge');
    const identitySummary = document.getElementById('identity-summary');
    const identityDetails = document.getElementById('identity-details');

    // --- API Helper ---
    const apiRequest = async (endpoint, method = 'GET', body = null) => {
        try {
            const options = { method };
            if (body) {
                options.headers = { 'Content-Type': 'application/json' };
                options.body = JSON.stringify(body);
            }
            const response = await fetch(endpoint, options);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `Request failed with status ${response.status}`);
            }
            return response.json();
        } catch (error) {
            console.error('API Request Error:', error);
            showSettingsMessage(error.message, 'error');
            throw error;
        }
    };

    // --- UI Update Functions ---
    const showSettingsMessage = (message, type = 'success') => {
        settingsMessage.textContent = message;
        settingsMessage.className = `mt-4 text-sm ${type === 'error' ? 'text-red-400' : 'text-green-400'}`;
    };

    const updateAuthStatus = (isAuthorized) => {
        if (isAuthorized) {
            authStatusBadge.textContent = 'Authorized';
            authStatusBadge.className = 'status-badge status-green';
        } else {
            authStatusBadge.textContent = 'Unauthorized';
            authStatusBadge.className = 'status-badge status-red';
            identitySummary.classList.add('hidden');
        }
    };

    const displayIdentity = (identity) => {
        identitySummary.classList.remove('hidden');
        identityDetails.innerHTML = `
            <div><span class="font-semibold">Subject Type:</span> ${identity.subjectType}</div>
            <div><span class="font-semibold">User/Client ID:</span> ${identity.userId || 'N/A'}</div>
            <div><span class="font-semibold">Username:</span> ${identity.username || 'N/A'}</div>
            <div><span class="font-semibold">Email:</span> ${identity.email || 'N/A'}</div>
            <div><span class="font-semibold">Org ID:</span> ${identity.orgId || 'N/A'}</div>
            <div><span class="font-semibold">Org Name:</span> ${identity.orgName || 'N/A'}</div>
            <div><span class="font-semibold">Region:</span> ${identity.regionCode}</div>
            <div class="md:col-span-2"><span class="font-semibold">Scopes:</span> ${identity.scopes.join(', ')}</div>
        `;
        updateAuthStatus(true);
    };

    const toggleCredentialFields = () => {
        const selectedType = document.querySelector('input[name="credentialType"]:checked').value;
        if (selectedType === 'authorization_code') {
            redirectUriContainer.classList.remove('hidden');
            loginBtn.classList.remove('hidden');
        } else {
            redirectUriContainer.classList.add('hidden');
            loginBtn.classList.add('hidden');
        }
    };

    const toggleCustomUrlField = () => {
        if (regionSelect.value === 'custom') {
            customBaseUrlContainer.classList.remove('hidden');
        } else {
            customBaseUrlContainer.classList.add('hidden');
        }
    };

    // --- Event Handlers ---
    saveBtn.addEventListener('click', async () => {
        const config = {
            regionCode: regionSelect.value,
            customBaseUrl: customBaseUrlInput.value,
            credentialType: document.querySelector('input[name="credentialType"]:checked').value,
            clientId: clientIdInput.value,
            clientSecret: clientSecretInput.value,
            redirectUri: redirectUriInput.value
        };

        if (!config.clientId || !config.clientSecret) {
            showSettingsMessage('Client ID and Client Secret are required.', 'error');
            return;
        }

        try {
            showSettingsMessage('Saving configuration...', 'info');
            await apiRequest('/api/session.php?action=config', 'POST', config);
            showSettingsMessage('Configuration saved successfully.', 'success');

            // If client credentials, try to authenticate immediately
            if (config.credentialType === 'client_credentials') {
                handleClientCredsAuth();
            }
        } catch (error) {
            // Error is handled by apiRequest helper
        }
    });

    const handleClientCredsAuth = async () => {
         try {
            showSettingsMessage('Authenticating with Client Credentials...', 'info');
            const identity = await apiRequest('/api/session.php?action=token', 'POST');
            displayIdentity(identity);
            showSettingsMessage('Authentication successful!', 'success');
        } catch (error) {
             updateAuthStatus(false);
        }
    }

    clearBtn.addEventListener('click', async () => {
        try {
            await apiRequest('/api/session.php?action=config', 'DELETE');
            showSettingsMessage('Configuration cleared.', 'success');
            // Clear form fields
            clientIdInput.value = '';
            clientSecretInput.value = '';
            customBaseUrlInput.value = '';
            regionSelect.value = 'us-east-1';
            updateAuthStatus(false);
        } catch (error) {
            // Error is handled by apiRequest helper
        }
    });

    loginBtn.addEventListener('click', () => {
        // The backend handles the redirect
        window.location.href = '/api/session.php?action=authorize';
    });

    testBtn.addEventListener('click', async () => {
        try {
            await apiRequest('/api/session.php?action=test');
            showSettingsMessage('Connection is authorized and valid.', 'success');
            updateAuthStatus(true);
            // Also refresh identity
            loadIdentity();
        } catch (error) {
            showSettingsMessage('Connection is not authorized.', 'error');
            updateAuthStatus(false);
        }
    });

    credentialTypeRadios.forEach(radio => radio.addEventListener('change', toggleCredentialFields));
    regionSelect.addEventListener('change', toggleCustomUrlField);

    // --- Initialization ---
    const loadConfig = async () => {
        try {
            const config = await apiRequest('/api/session.php?action=config');
            regionSelect.value = config.regionCode;
            customBaseUrlInput.value = config.customBaseUrl;
            clientIdInput.value = config.clientId;
            // Don't populate secret
            document.querySelector(`input[name="credentialType"][value="${config.credentialType}"]`).checked = true;
            toggleCredentialFields();
            toggleCustomUrlField();
        } catch (error) {
            // No config saved yet, which is fine.
        }
    };

    const loadIdentity = async () => {
         try {
            const identity = await apiRequest('/api/session.php?action=identity');
            displayIdentity(identity);
        } catch (error) {
            updateAuthStatus(false);
        }
    }

    const initialize = () => {
        // Set the redirect URI dynamically
        redirectUriInput.value = `${window.location.origin}/api/session.php?action=callback`;

        loadConfig();
        loadIdentity();
    };

    initialize();
});
