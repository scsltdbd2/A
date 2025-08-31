<?php
// Bootstrap for the backend API

// --- Security & Session Management ---

// Ensure session cookies are secure
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Should be true in production (HTTPS)
ini_set('session.cookie_samesite', 'Strict');

// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the Genesys Cloud API client
require_once 'GenesysClient.php';

// --- Response Helpers ---

function send_json($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function send_error($message, $statusCode = 400, $details = []) {
    send_json(['error' => $message, 'details' => $details], $statusCode);
}

// --- Routing ---

$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

switch ($action) {
    case 'config':
        if ($method === 'POST') {
            handle_save_config();
        } elseif ($method === 'GET') {
            handle_get_config();
        } elseif ($method === 'DELETE') {
            handle_delete_config();
        } else {
            send_error('Method not allowed', 405);
        }
        break;

    case 'authorize':
        if ($method === 'GET') {
            handle_authorize_redirect();
        } else {
            send_error('Method not allowed', 405);
        }
        break;

    case 'callback':
        if ($method === 'GET') {
            handle_oauth_callback();
        } else {
            send_error('Method not allowed', 405);
        }
        break;

    case 'token':
        if ($method === 'POST') {
            handle_get_token_client_credentials();
        } else {
            send_error('Method not allowed', 405);
        }
        break;

    case 'identity':
        if ($method === 'GET') {
            handle_get_identity();
        } else {
            send_error('Method not allowed', 405);
        }
        break;

    case 'test':
        if ($method === 'GET') {
            handle_test_authorization();
        } else {
            send_error('Method not allowed', 405);
        }
        break;

    default:
        send_error('Invalid action', 404);
        break;
}

// --- Route Handlers ---

function handle_save_config() {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        send_error('Invalid JSON body');
    }

    // Basic validation
    $required_fields = ['regionCode', 'credentialType', 'clientId', 'clientSecret'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            send_error("Missing required field: $field");
        }
    }

    // Regenerate session ID for security
    session_regenerate_id(true);

    $_SESSION['config'] = [
        'regionCode' => $input['regionCode'],
        'customBaseUrl' => $input['customBaseUrl'] ?? null,
        'credentialType' => $input['credentialType'],
        'clientId' => $input['clientId'],
        'clientSecret' => $input['clientSecret'], // Stored in session, not on disk
        'redirectUri' => $input['redirectUri'] ?? null,
    ];

    // Clear any previous identity/token data
    unset($_SESSION['token']);
    unset($_SESSION['identity']);

    send_json(['status' => 'Configuration saved successfully']);
}

function handle_get_config() {
    if (empty($_SESSION['config'])) {
        send_error('Configuration not found', 404);
    }

    $config = $_SESSION['config'];
    // Mask the client secret before sending to the frontend
    $config['clientSecret'] = '****************';

    send_json($config);
}

function handle_delete_config() {
    session_destroy();
    send_json(['status' => 'Configuration and session cleared']);
}

function handle_authorize_redirect() {
    if (empty($_SESSION['config'])) {
        send_error('Configuration not found. Please save settings first.', 404);
    }
    $config = $_SESSION['config'];

    $client = new GenesysClient($config);
    $authUrl = $client->getAuthorizationUrl();

    header('Location: ' . $authUrl);
    exit;
}

function handle_oauth_callback() {
    if (empty($_SESSION['config']) || !isset($_GET['code'])) {
        send_error('Invalid callback request. Missing configuration or authorization code.');
    }
    $config = $_SESSION['config'];
    $code = $_GET['code'];

    try {
        $client = new GenesysClient($config);
        $tokenData = $client->exchangeCodeForToken($code);
        $_SESSION['token'] = $tokenData;

        $identity = $client->resolveIdentity($tokenData['access_token']);
        $_SESSION['identity'] = $identity;

        // Redirect back to the main application page
        $app_url = dirname($_SERVER['REQUEST_URI'], 2) . '/index.html';
        header('Location: ' . $app_url);
        exit;

    } catch (Exception $e) {
        send_error('Failed to process OAuth callback', 500, ['reason' => $e->getMessage()]);
    }
}

function handle_get_token_client_credentials() {
    if (empty($_SESSION['config'])) {
        send_error('Configuration not found. Please save settings first.', 404);
    }

    try {
        $client = new GenesysClient($_SESSION['config']);
        $tokenData = $client->getTokenClientCredentials();
        $_SESSION['token'] = $tokenData;

        $identity = $client->resolveIdentity($tokenData['access_token']);
        $_SESSION['identity'] = $identity;

        send_json($identity);

    } catch (Exception $e) {
        send_error('Failed to get token', 500, ['reason' => $e->getMessage()]);
    }
}

function handle_get_identity() {
    if (empty($_SESSION['identity'])) {
        send_error('Identity not found. Please authorize first.', 404);
    }
    send_json($_SESSION['identity']);
}

function handle_test_authorization() {
    if (!empty($_SESSION['identity']) && !empty($_SESSION['token'])) {
        // A more robust test would be to check token expiry and make a lightweight API call
        send_json(['status' => 'Authorized']);
    } else {
        send_json(['status' => 'Unauthorized'], 401);
    }
}

?>
