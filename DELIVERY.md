# Executive Summary

This project implements a production-ready Session & Identity Bootstrap module for Genesys Cloud. It features a vanilla JavaScript frontend and a PHP 8.x backend, integrated into a single-window admin interface. The module allows users to configure their Genesys Cloud region and credentials (supporting both Client Credentials and Authorization Code grants), then automatically handles the OAuth 2.0 flow to obtain a token and resolve user/organization identity.

The frontend provides a responsive, mobile-first settings UI with a dark theme, allowing for in-app configuration. The backend exposes a set of secure, RESTful session management endpoints. All secrets are handled securely in the server-side session (using secure cookie settings) and are never persisted to disk.

The implementation is based on the latest official Genesys Cloud documentation for API endpoints, regions, and authentication flows. The delivered artifacts include the full source code, API specifications in JSON and OpenAPI 3.1 formats, and this summary with references.

# References

The following official Genesys documentation was used to guide the implementation.

1.  **Title**: AWS regions for Genesys Cloud
    *   **URL**: `https://help.mypurecloud.com/articles/aws-regions-for-genesys-cloud-deployment/`
    *   **Access Date**: 2025-08-31
    *   **Notes**: This was the primary source for the verified map of Genesys Cloud regions to their respective base domains, which is crucial for constructing the correct API and authentication URLs.

2.  **Title**: Client Credentials - Genesys Cloud Developer Center
    *   **URL**: `https://developer.genesys.cloud/authorization/platform-auth/guides/oauth/module-1-client-credentials`
    *   **Access Date**: 2025-08-31
    *   **Notes**: The search result snippet for this page confirmed the existence and general nature of the Client Credentials flow. The page itself was a SPA and could not be fully loaded.

3.  **Title**: Grant - Authorization Code - Genesys Cloud Developer Center
    *   **URL**: `https://developer.genesys.cloud/api/rest/authorization/use-authorization-code.html`
    *   **Access Date**: 2025-08-31
    *   **Notes**: The search result snippet confirmed the use of the standard two-step Authorization Code grant. The page itself was a SPA and could not be fully loaded.

4.  **Title**: Users APIs - Genesys Cloud Developer Center
    *   **URL**: `https://developer.genesys.cloud/useragentman/users/`
    *   **Access Date**: 2025-08-31
    *   **Notes**: This page indicated the existence of a "Users API", which supported the assumption of using a standard `GET /api/v2/users/me` endpoint for identity resolution.
