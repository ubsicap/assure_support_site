# Single-Sign-On Authentication

Provides functionality for external provider SSO, including Google, Facebook, and Paratext.

**Note**: This plugin requires [Composer](https://usefulangle.com/post/9/google-login-api-with-php-curl) and a `vendor/` directory at the root of the web server.

## Contents

```
├── lang
│   └── sso-auth-lang-default.php       // Default (English) language file
├── config.php                          // Configuration for the Google API
├── metadata.json                       // Plugin metadata
├── qa-plugin.php                       // Registers the plugin
├── sso-authentication-layer.php        // Mainly CSS overrides
└── sso-authentication-login.php        // Handles getting user info through external provider SSO API
```

## Functionality

This plugin provides SSO functionality through Google, Facebook, and Paratext.
Through the "Log in with" button, you can:

-   Create a new account
-   Reclaim an archived account
-   Log in to an existing account (even if you did not create it with the external provider SSO)
