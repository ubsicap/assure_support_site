# Site Configuration Information

This document contains information such about installed plugins, custom pages, and admin panel configurations.

## Table of Contents

-   [Pages](#pages)
-   [Plugins](#plugins)
    -   [account-reclaim](#account-reclaim)
    -   [auto-prune-accounts](#auto-prune-accounts)
    -   [category-logo](#category-logo)
    -   [google-analytics](#google-analytics)
    -   [post-validator](#post-validator)
    -   [q2a-badges](#q2a-badges)
    -   [q2a-faq](#q2a-faq)
    -   [q2a-hashtagger](#q2a-hashtagger)
    -   [q2a-pupi-srs](#q2a-pupi-srs)
    -   [q2a-role-markers](#q2a-role-markers)
    -   [q2a-sticky-sidebar-plugin](#q2a-sticky-sidebar-plugin)
    -   [q2a-style](#q2a-style)
    -   [q2a-tagging-tools](#q2a-tagging-tools)
    -   [q2apro-on-site-notifications](#q2apro-on-site-notifications)
    -   [q2apro-pretty-tags](#q2apro-pretty-tags)
    -   [q2apro-userinfo](#q2apro-userinfo)
    -   [random-avatar](#random-avatar)
    -   [send-account-reclaim](#send-account-reclaim)
    -   [sso-authentication](#sso-authentication)
    -   [title-length-counter](#title-length-counter)

## Pages

Several custom pages are included in the site. While Q2A supports the creation of custom pages through the administration panel, we have also included the HTML in [a subdirectory of the site](../public/qa-custom-pages) in case the database is cleared.

## Plugins

`To do: Add a description for each plugin, a description of the configuration (if there is a stylesheet or some bulk text field the content should be listed or mentioned where to find a copy of it), also if the plugin has been updated from the original in case of future downloads. Any paid plugins also need to be mentioned regardless if they are on the repository or not (i.e. dynamic mentions).`

Below is a list of all [Q2A Plugins](https://docs.question2answer.org/addons/plugins/) installed for this site.
We have omitted the [default plugins](https://github.com/q2a/question2answer/tree/dev/qa-plugin) included with Q2A for the sake of brevity.

### [account-reclaim](../public/qa-plugin/account-reclaim)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/account-reclaim/README.md).

Allows users to reclaim their archived accounts from support.paratext.org.
"Archived Account" is the term used for an account that was once associated with support.paratext.org but has been anonymized upon the migration to this new site.
The process largely mimics the "Forgot Password" process, with a few tweaks.

### [auto-prune-accounts](../public/qa-plugin/auto-prune-accounts)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/auto-prune-accounts/README.md).

Automatically delete accounts that have not verified their email after a set amount of time.
Does not use CRON jobs- rather it just marks accounts for deletion and bulk-deletes them on certain triggers.

### [category-logo](../public/qa-plugin/category-logo)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/category-logo/README.md).

Display a image next to category names, such as logos for categories that represent products.

### [google-analytics](https://github.com/kufeiko/QA-Google-Analytics-Plugin)

Support for Google Analytics. Requires a tracking code from Google Analytics.

### [post-validator](../public/qa-plugin/post-validator)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/post-validator/README.md).

Warns users if they attempt to post identifying information.

### [pupi-dm](https://bitbucket.org/pupi1985/q2a-dynamic-mentions-public)

_Premium Plugin_: This plugin is proprietary. It was purchased during development, and is not included in this repository in honor of the purchase.

Support for dynamically-suggested @mentions in posts.

### [q2a-badges](https://github.com/NoahY/q2a-badges)

Assigns users badges for certain (configurable) milestones, such as number of answers posted.

### [q2a-faq](https://github.com/gturri/q2a-faq/)

Adds a Frequently Asked Questions page, fully configurable through the admin panel.

### [q2a-hashtagger](https://github.com/pupi1985/q2a-hashtagger)

Convert #tags to hyperlinks in posts.
Similar to the dynamic mentions plugin, but does not dynamically suggest tags.

### [q2a-pupi-srs](https://github.com/pupi1985/q2a-pupi-srs)

Provides spam control through multiple well-known spam checking services.

### [q2a-role-markers](https://github.com/gurjyot/q2a-role-markers)

Custom role names and markers adjacent to users with them.

This plugin has been modified to be more appropriate for this project.
Most notably, the ability to add custom roles to users has been implemented.
Administrators can now assign a unique role to users by visiting their profile.
This custom role will display next to the user's display name across the site, and the color for custom titles can be configured via CSS in the admin panel.

### [q2a-sticky-sidebar-plugin](https://github.com/stefanmm/q2a-sticky-sidebar-plugin/)

Sidebar stays on the screen when you scroll down.

This plugin has been slightly modified according to the theme of the website.

### [q2a-style](../public/qa-plugin/q2a-style)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/q2a-style/README.md).

Various UI alterations, such as relocating the search bar, hiding redundant titles, highlighting the selected page name, etc.

### [q2a-tagging-tools](https://github.com/svivian/q2a-tagging-tools)

Enforce min/max length for post tags and allows you to create "tag synonyms" which map similar tags to each other.
For example, the synonym `pt,paratext` would automatically convert the tag `pt` to `paratext` when a post is submitted.
It also has the ability to retroactively apply these synonyms.
Lastly, it can remove tags altogether by placing them on standalone lines.
During the migration from the old site to this one, we are automatically generating tag data for posts, so it will be useful to apply these synonyms retroactively.

To see a list of all tag synonyms used in development (and thus, the synonyms we suggest using), refer to the [TagSynonyms file](../public/qa-plugin/q2a-tagging-tools/TagSynonyms.md).
Copy and paste all desired rules into the Tagging Tools' admin form.

### [q2apro-on-site-notifications](https://github.com/q2apro/q2apro-on-site-notifications)

Notification icons similar to social media.

### [q2apro-pretty-tags](https://github.com/ProThoughts/q2apro-pretty-tags)

Auto-suggest and auto-complete tags as you type them.

This plugin has been modified to be more appropriate for this site, including UI changes.

### [q2apro-userinfo](https://github.com/ProThoughts/q2apro-userinfo)

Hover over a username to see information.

This plugin has been modified to be more appropriate for this site, including UI changes.

### [random-avatar](../public/qa-plugin/random-avatar)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/random-avatar/README.md).

All new accounts get a Gravatar image, generated uniquely from their email address.

### [send-account-reclaim](../public/qa-plugin/send-account-reclaim)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/send-account-reclaim/README.md).

Send emails to users in the archived database table through the admin panel.
Toggleable support for HTML in email body.

### [sso-authentication](../public/qa-plugin/sso-authentication)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/sso-authentication/README.md).

Facebook, Google, and Paratext Registry SSO support.

### [title-length-counter](https://github.com/MominRaza/title-length-counter)

Display title length and prevent typing past the max length.

This plugin has been slightly modified to also display when editing questions.
It also displays warning colors when the post's title is below the minimum or at the maximum length.
