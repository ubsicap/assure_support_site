# Account Reclaim Plugin for Q2A

Allows users to reclaim archived accounts.

## Contents

```
├── lang                            // Language files
│   └── qa-ar-lang-default.php      // Default (English)
├── metadata.json                   // Plugin metadata
├── qa-ar-admin.php                 // Admin page configuration
├── qa-ar-event.php                 // Handles deletion of archived accounts
├── qa-ar-filter.php                // Registration page confirmation for archived accounts
├── qa-ar-functions.php             // Custom helper functions
├── qa-ar-layer.php                 // Registration page layer for archived accounts
├── qa-ar-overrides.php             // Overrides of Q2A builtin functions
├── qa-ar-page.php                  // Recovery and Reclaim pages
└── qa-plugin.php                   // Registers the plugin
```

## Functionality

This plugin provides the functionality for users to "reclaim" accounts that have been archived.
It largely mimics the "I forgot my password" process.

It requires a custom table to be created called `^accountreclaim` with the following fields:

1. `userid`: The same value of a user in `^users` that is archived
1. `email`: Original email of an archived user
1. `reclaimcode`: Similar to `emailcode` in `^users`, but used for this process

Two pages are created: `recover-account` and `account-reclaim`.
The recovery page explains the process and prompts the user to enter an email address associated with an archived account.
Once entered, they are redirected to the reclaim page and an email is sent to them containing a code to enter on this page.
If the code entered is correct, they are redirected to a page wherein they are prompted to create a new password and change their username.
They are not required to change their username, as their current one is provided, but they are informed about the lack of anonymity on the forum.
Once their credentials have been updated, they are logged in and redirected to a destination page that is customizable in the admin panel.
After the process completes, `^users` is updated to reflect their new username, password, and correct email address.
Likewise, their entry in `^accountreclaim` is deleted, so they cannot repeat this process.

In addition to this process, this plugin also provides a layer over the standard registration process.
If a user attempts to create an account with an email address that is located in `^accountreclaim`, they are prompted to reclaim that account instead.
Users have the choice to abandon their old account and create a new one.
If they choose to do so, their archived account remains permanently inaccessible.

Lastly, the process for removing entries from the `^accountreclaim` table is handled by an event module that monitors events relating to archived accounts.
Any time an account is registered with an email address that is in `^accountreclaim`, that address is notified via email that an account has been registered using it.
When an archived account is reclaimed or its email is confirmed, its entry is deleted and any spam accounts created using its email are deleted.