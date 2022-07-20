# Single-Sign-On Authentication

Provides functionality for Google SSO.

**Note**: This plugin requires [Composer](https://usefulangle.com/post/9/google-login-api-with-php-curl) and a `vendor/` directory at the root of the web server.

## Contents

```
├── lang
│   └── sso-auth-lang-default.php       // Default (English) language file
├── config.php                          // Configuration for the Google API
├── customized-dropdown.php             // HTML for drop-down menu
├── metadata.json                       // Plugin metadata
├── qa-plugin.php                       // Registers the plugin
├── sso-authentication-admin.php        // Admin form
├── sso-authentication-layer.php        // Mainly CSS overrides
└── sso-authentication-login.php        // Handles getting user info through Google SSO API
```

## Functionality

This plugin provides SSO functionality through Google.
Through the "Log in with Google" button, you can:

-   Create a new account
-   Reclaim an archived account
-   Log in to an existing account (even if you did not create it with Google SSO)
