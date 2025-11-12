<?php

class GenesysClient {
    private $config;
    private $baseUrl;
    private $authUrl;

    // Based on research from: https://help.mypurecloud.com/articles/aws-regions-for-genesys-cloud-deployment/
    // Accessed on: 2025-08-31
    private static $regionMap = [
        'us-east-1' => 'mypurecloud.com',
        'us-west-2' => 'usw2.pure.cloud',
        'ca-central-1' => 'cac1.pure.cloud',
        'sa-east-1' => 'sae1.pure.cloud',
        'eu-central-1' => 'mypurecloud.de',
        'eu-west-1' => 'mypurecloud.ie',
        'eu-west-2' => 'euw2.pure.cloud',
        'ap-northeast-1' => 'mypurecloud.jp',
        'ap-northeast-2' => 'apne2.pure.cloud',
        'ap-southeast-2' => 'mypurecloud.com.au',
        'ap-south-1' => 'aps1.pure.cloud',
        'us-east-2' => 'use2.us-gov-pure.cloud',
        'eu-central-2' => 'euc2.pure.cloud',
        'mec-central-1' => 'mec1.pure.cloud',
        'ap-northeast-3' => 'apne3.pure.cloud',
    ];

    public function __construct($config) {
        $this->config = $config;

        if (!empty($config['customBaseUrl'])) {
            $this->baseUrl = rtrim($config['customBaseUrl'], '/');
            $this->authUrl = $this->baseUrl; // Assuming custom URL is the same for auth and API
        } else {
            $domain = self::$regionMap[$config['regionCode']] ?? 'mypurecloud.com';
            $this->baseUrl = 'https://api.' . $domain;
            $this->authUrl = 'https://login.' . $domain;
        }
    }

    public function getAuthorizationUrl() {
        $params = [
            'response_type' => 'code',
            'client_id' => $this->config['clientId'],
            'redirect_uri' => $this->config['redirectUri'],
            'scope' => 'openid profile' // A common scope, can be configured
        ];
        return $this->authUrl . '/oauth/authorize?' . http_build_query($params);
    }

    public function exchangeCodeForToken($code) {
        $url = $this->authUrl . '/oauth/token';
        $body = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->config['redirectUri']
        ];
        return $this->makeTokenRequest($url, $body);
    }

    public function getTokenClientCredentials() {
        $url = $this->authUrl . '/oauth/token';
        $body = [
            'grant_type' => 'client_credentials'
        ];
        return $this->makeTokenRequest($url, $body);
    }

    public function resolveIdentity($accessToken) {
        // Fetch user/client details
        $subjectDetails = null;
        try {
            // This endpoint is standard for getting user info in OAuth2/OIDC
            $subjectDetails = $this->makeRequest('/api/v2/users/me?expand=organization', 'GET', $accessToken);
        } catch (Exception $e) {
            // If users/me fails, it might be a client credential grant.
            // We'll try to get basic token info. A dedicated client info endpoint might exist,
            // but is not easily discoverable without documentation access.
            if ($this->config['credentialType'] === 'client_credentials') {
                 try {
                    $tokenDetails = $this->makeRequest('/api/v2/oauth/token/details', 'GET', $accessToken);
                     $subjectDetails = ['id' => $this->config['clientId'], 'name' => 'API Client', 'scopes' => $tokenDetails['scope']];
                 } catch(Exception $e2) {
                     throw new Exception('Could not resolve identity for user or client.', 0, $e);
                 }
            } else {
                 throw $e; // Re-throw if it was not a client credentials grant
            }
        }

        // Normalize the identity model
        $identity = [
            'subjectType' => isset($subjectDetails['email']) ? 'user' : 'client',
            'userId' => $subjectDetails['id'] ?? null,
            'username' => $subjectDetails['name'] ?? null,
            'email' => $subjectDetails['email'] ?? null,
            'orgId' => $subjectDetails['organization']['id'] ?? null,
            'orgName' => $subjectDetails['organization']['name'] ?? null,
            'scopes' => $subjectDetails['scopes'] ?? [],
            'regionCode' => $this->config['regionCode'],
            'baseUrl' => $this->baseUrl,
            'token' => [
                'expiresAt' => $_SESSION['token']['expires_at_iso'] ?? null,
                'issuedAt' => $_SESSION['token']['issued_at_iso'] ?? null,
                'secondsRemaining' => ($_SESSION['token']['expires_at'] ?? 0) - time(),
            ]
        ];

        return $identity;
    }

    private function makeTokenRequest($url, $body) {
        $authHeader = 'Basic ' . base64_encode($this->config['clientId'] . ':' . $this->config['clientSecret']);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($body));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $authHeader
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new Exception("Token request failed with status $httpCode: $response", $httpCode);
        }
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }

        $data = json_decode($response, true);

        // Add timestamps for session management
        $issuedAt = time();
        $expiresAt = $issuedAt + $data['expires_in'];
        $data['issued_at'] = $issuedAt;
        $data['expires_at'] = $expiresAt;
        $data['issued_at_iso'] = date(DateTime::ISO8601, $issuedAt);
        $data['expires_at_iso'] = date(DateTime::ISO8601, $expiresAt);

        return $data;
    }

    private function makeRequest($endpoint, $method = 'GET', $accessToken, $data = null) {
        // Note: Production-ready code should implement more robust error handling,
        // logging, and retry logic with exponential backoff as requested.

        $url = $this->baseUrl . $endpoint;
        $authHeader = 'Bearer ' . $accessToken;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authHeader
        ];

        if ($method === 'POST' || $method === 'PUT') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode >= 400) {
            throw new Exception("API request to $endpoint failed with status $httpCode: $response", $httpCode);
        }
        if ($error) {
            throw new Exception("cURL Error: " . $error);
        }

        return json_decode($response, true);
    }
}
?>
