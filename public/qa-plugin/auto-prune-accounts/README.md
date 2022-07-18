# Auto-Prune Accounts for Q2A

Automatically delete accounts that have not verified their email after a configurable amount of time.

**Note**: Only works if Q2A requires users to verify their email after registration.

## Contents

```
├── lang                            // Language files
│   └── qa-ar-lang-default.php      // Default (English)
├── metadata.json                   // Plugin metadata
├── qa-ar-admin.php                 // Admin page configuration
├── qa-ar-events.php                // Bulk-deletes unverified accounts when triggered
├── qa-ar-filters.php               // Email filter for pruning when attempting to register
└── qa-plugin.php                   // Registers the plugin
```

## Functionality

This plugin sets up the process to automatically remove accounts who have not confirmed their email after a specified amount of time.
The admin page allows you to configure a timeout (in minutes) to control how long users have to verify their account before it is marked for deletion.
Accounts are not automatically deleted once they exceed the timeout period, rather they are just marked for deletion and will be removed once the deletion is triggered.
There are currently three triggers for deletion:

-   When a new account is registered (note that this newly-registered account will not be removed in the deletion triggered by this event)
-   When any user logs in or out
-   When any user confirms their email address

Each of these events is toggleable in the admin page.

In addition to the above triggers, this plugin provides a filter on registration that both alerts a user if the email they are attempting to register with is associated with an unverified account and deletes this unverified account if it is marked for deletion.
This prevents the ability to "steal" email addresses by creating accounts with various email addresses and never verifying them.
