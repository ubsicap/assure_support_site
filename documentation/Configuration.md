# Site Configuration Information

This document contains information such about installed plugins, custom pages, and admin panel configurations.

## Table of Contents

-   [Donut theme settings](#donut-theme-settings)
-   [General](#general)
-   [Emails](#emails)
-   [Users](#users)
-   [Layout](#layout)
-   [Posting](#posting)
-   [Viewing](#viewing)
-   [Lists](#lists)
-   [Categories](#categories)
-   [Permissions](#permissions)
-   [Pages](#pages)
-   [RSS feeds](#rss-feeds)
-   [Points](#points)
-   [Spam](#spam)
-   [Caching](#caching)
-   [Stats](#stats)
-   [Mailing](#mailing)
-   [Plugins](#plugins)
    -   [account-reclaim](#account-reclaim)
    -   [auto-prune-accounts](#auto-prune-accounts)
    -   [badges-Q2A](#badges-q2a)
    -   [category-logo](#category-logo)
    -   [category-search](#category-search)
    -   [dynamic-mentions](#dynamic-mentions)
    -   [faq-page](#faq-page)
    -   [google-analytics](#google-analytics)
    -   [hashtagger](#hashtagger)
    -   [on-site-notifications](#on-site-notifications)
    -   [open-search-support](#open-search-support)
    -   [post-validator](#post-validator)
    -   [pretty-tags](#pretty-tags)
    -   [q2a-style](#q2a-style)
    -   [random-avatar](#random-avatar)
    -   [random-verse](#random-verse)
    -   [reCAPTCHA](#reCAPTCHA)
    -   [reclaim-account-title](#reclaim-account-title)
    -   [role-markers](#role-markers)
    -   [send-account-reclaim](#send-account-reclaim)
    -   [simple-sticky-sidebar](#simple-sticky-sidebar)
    -   [spam-registration-stopper](#spam-registration-stopper)
    -   [tagging-tools](#tagging-tools)
    -   [sso-authentication](#sso-authentication)
    -   [title-length-counter](#title-length-counter)
    -   [user-info](#user-info)

## Donut theme settings

-   Check **Enable top bar**
    -   **Left text**: `New here? <a href="https://support.bible/register" style="color: inherit;">Create a new account</a>!`
    -   **Right text**: `Got redirected? <a href="https://support.bible/recover-account" style="color: inherit;">Reclaim your existing Paratext Support account</a>!`
-   Check **Enable sticky header on scroll**, **Enable back to top button**, and **Show site status above footer**
-   Either uncheck **Show home page banner**, or configure it to your liking.
-   Uncheck **Show social links at footer** and **Show copyright at footer**

## General

-   **Q&A site name**: `Support.Bible Forum`
-   **Preferred site URL**: Make sure it begins with `https://`
-   **URL structure**: `/123/why-do-birds-sing (requires htaccess file)`
-   **Site language**: `English (UK)`
-   **Site theme** and **Theme for mobiles**: `Donut`
-   **Question classification**: `Tags and Categories`

## Emails

-   **Sender address for messages from site**: `admin@support.bible`
-   **Email address for admin messages - not shown to users**: `admin@support.bible`
-   Check **Send email via SMTP** and **Send SMTP username and password**
-   Fill in the required fields with the appropriate SMTP credentials

## Users

-   **Disallowed usernames**: `anonymous`, `admin`, `anon`
-   Check **Allow Gravatar avatars**

## Layout

-   Check **Show a logo image in the page header**
-   **URL of logo**: `/assets/NewSupportBibleLogo.png`
-   **Logo width**: `300`
-   **Logo height**: `200`
-   Check **Custom HTML in sidebar box**
    -   `Welcome to Support Bible, where you can ask questions and receive answers from other members of the community. <hr>`
-   Add the following widgets:
    -   Available by default:
        -   **Search Bar**: `Main area - Top`
        -   **Related Questions**: `Main area - Bottom`
        -   **Categories**: `Side panel - Below sidebar box`
        -   **Give Feedback Widget**: `Side panel - Below sidebar box`
    -   Available later:
        -   **Tag Descriptions**: `Full width - Top of page`
        -   **Tag Descriptions**: `Main area - Bottom`
        -   **TagSearch Widget**: `Side panel - Top`
    > NOTE: do this after setting plugins

## Posting

-   **Default editor for ...**: `WYSIWYG Editor`
-   Check **Check for similar questions when asking**, **Show example tags based on question**, and **Show matching tags while typing**

## Viewing

## Lists

-   **Use search module**: `Category Search Module`
> NOTE: do this after setting plugins

## Categories

-   Add the following categories:
    -   **Name**: `General`, **Slug**: `general`
    -   **Name**: `Paratext`, **Slug**: `paratext`
    -   **Name**: `PTXprint`, **Slug**: `ptxprint`
    -   **Name**: `Paratext Lite`, **Slug**: `paratext-lite`
    -   **Name**: `FLExTrans`, **Slug**: `flextrans`
    -   **Name**: `Scripture Forge`, **Slug**: `scripture-forge`
    -   **Name**: `Publishing Assistant`, **Slug**: `publishing-assistant`
    -   **Name**: `Platform.Bible`, **Slug**: `platform-bible`
    -   **Name**: `Paratext 10 Studio`, **Slug**: `paratext-10-studio`
-   Uncheck **Allow questions with ...**

## Permissions

-   **Viewing question pages**: `Anybody`
-   **Asking questions**: `Registered users with email confirmed`
-   **Answering questions**: `Registered users with email confirmed`
-   **Adding comments**: `Registered users with email confirmed`
-   **Voting on questions**: `Registered users with email confirmed`
-   **Voting on answers**: `Registered users with email confirmed`

## Pages

-   Check **Questions**, **Unanswered**, **Tags**, **Users**, **Ask a Question**
-   Add the following custom pages:
    -   **Privacy Policy**
        -   **Heading**: `Privacy Policy`
        -   **Content**: [here](../public/qa-custom-pages/privacy-policy.html)
    -   **FAQ**
        -   Use FAQ page available via plugins.
        -   **Position**: `After tabs at top`
    -   **Best Practices**
        -   **Slug**: `best-practices`
        -   **Content**: [here](../public/qa-custom-pages/best_practices.html)
    -   **Welcome**
        -   **Content**: [here](../public/qa-custom-pages/welcome.html)
    -   **Paratext Support Redirect**
        -   **Slug**: `paratext-support-redirect`
        -   **Content**: [here](../public/qa-custom-pages/paratext_redirect.html)

Several custom pages are included in the site. While Q2A supports the creation of custom pages through the administration panel, we have also included the HTML in [a subdirectory of the site](../public/qa-custom-pages) in case the database is cleared.

## RSS feeds

-   Uncheck **Feed for ...**, **Individual feeds per category**, **Include full text in feeds**.

## Points

-   Set **Multiply all points** to `1`

## Spam

-   Check **Request confirmation ...**, **All new users ...**
-   Set **Rate limit for uploading files**: *per user/hour*: `15`
-   Available later:
    -   **User captcha ...**
    -   **Use captcha module**: `reCAPTCHA`
> NOTE: do this after setting plugins

## Caching

## Stats

-   Run each of the **Database clean-up operations** at the bottom of the page

## Mailing

-   Check **Enable mass mailing to all users**
-   Set
    -   **From name**: `Support Bible`
    -   **From email address**: `admin@support.bible`
    -   **Subject line**: `A message from Support Bible`
    -   **Body Text**:
```text
--
Support Bible
https://support.bible/
```


## Plugins

`To do: Add a description for each plugin, a description of the configuration (if there is a stylesheet or some bulk text field the content should be listed or mentioned where to find a copy of it), also if the plugin has been updated from the original in case of future downloads. Any paid plugins also need to be mentioned regardless if they are on the repository or not (i.e. dynamic mentions).`

Below is a list of all [Q2A Plugins](https://docs.question2answer.org/addons/plugins/) installed for this site.
We have omitted the [default plugins](https://github.com/q2a/question2answer/tree/dev/qa-plugin) included with Q2A for the sake of brevity. The default options for most plugins will suffice, unless other options are provided.

### [account-reclaim](../public/qa-plugin/account-reclaim)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/account-reclaim/README.md).

Allows users to reclaim their archived accounts from support.paratext.org.
"Archived Account" is the term used for an account that was once associated with support.paratext.org but has been anonymized upon the migration to this new site.
The process largely mimics the "Forgot Password" process, with a few tweaks.

#### Configuration

-   We recommend checking **Use CAPTCHA on account recovery**

### [auto-prune-accounts](../public/qa-plugin/auto-prune-accounts)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/auto-prune-accounts/README.md).

Automatically delete accounts that have not verified their email after a set amount of time.
Does not use CRON jobs- rather it just marks accounts for deletion and bulk-deletes them on certain triggers.

#### Configuration

-   A 30 minute "grace period" is plenty of time. Shorten this if you find too many spam accounts are being created.
-   Check all *Delete unverified accounts when ...** boxes

### [badges-Q2A](https://github.com/NoahY/q2a-badges)

Assigns users badges for certain (configurable) milestones, such as number of answers posted.

#### Configuration

-   Check **Activate badges** and hit **Save**
-   Enable the following badges:
    -   Nice Question, Good Question, Great Question
    -   Notable Question, Popular Question, Famous Question
    -   Nice Answer, Good Answer, Great Answer
    -   Gifted, Wise, Enlightened
    -   Grateful, Respectful, Reverential
    -   Liked, Loved, Revered
    -   Asker, Questioner, Inquisitor
    -   Answerer, Lecturer, Preacher
    -   Reader, Avid Reader, Devoted Reader
    -   Dedicated, Devoted, Zealous
    -   Visitor, Trouper, Veteran
    -   Regular, Old-Timer, Ancestor
    -   100 Club, 1,000 Club, 10,000 Club
    -   Medalist, Champion, Olympian
    -   Nice Comment, Good Comment, Great Comment
-   Disable the following badges:
    -   Verified Human, Autobiographer, Photogenic
    -   Renewal, Revival, Resurrection
    -   Commenter, Commentator, Annotator
    -   Voter, Avid Voter, Devoted Voter
    -   Editor, Copy Editor, Senior Editor
    -   Watchdog, Bloodhound, Pitbull
-   Set the `Notify Duration` to 0 (disabled)
-   Check **Add badges to profile pages** and **Show badges on main profile field**
-   Uncheck `Show list of ...` boxes

### [category-logo](../public/qa-plugin/category-logo)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/category-logo/README.md).

Display a image next to category names, such as logos for categories that represent products.

#### Configuration

-   Check **Add specific logo to corresponding categories** and **Enable sorting categories based on the amount of questions in a category**
-   Add the following paths:
    -   **Paratext**: `/assets/ParatextCenter.png`
    -   **Publishing Assistant**: `/assets/PublishingAssistant.png`
    -   **Paratext Lite**: `/assets/ParatextLiteCenter.png`
    -   **General**: `/assets/logo.png`
    -   **FLExTrans**: `/assets/FLEXTransIcon.png`
    -   **Scripture Forge**: `/assets/ScriptureForgeIcon.png`
    -   **PTXprint**: `/assets/PTXprint.png`
    -   **PlatformBibleLogo**: `/assets/PlatformBibleLogo.png`
    -   **Paratext10Studio**: `/assets/ParatextStudio.png`

### [category-search](../public/qa-plugin/category-search/README.md)

#### Configuration

-   Enable the plugin

### [dynamic-mentions](https://bitbucket.org/pupi1985/q2a-dynamic-mentions-public)

_Premium Plugin_: This plugin is proprietary. It was purchased during development, and is not included in this repository in honor of the purchase.

Support for dynamically-suggested @mentions in posts.


### [faq-page](https://github.com/gturri/q2a-faq/)

Adds a Frequently Asked Questions page, fully configurable through the admin panel.

#### Configuration

-   Default configuration is acceptable
-   You can add FAQ entries from this section


### [google-analytics](https://github.com/kufeiko/QA-Google-Analytics-Plugin)

Support for Google Analytics. Requires a tracking code from Google Analytics.

#### Configuration

-   Add the **[Google Global Site Tag](https://developers.google.com/analytics/devguides/collection/gtagjs)**
    

### [hashtagger](https://github.com/pupi1985/q2a-hashtagger)

Convert #tags to hyperlinks in posts.
Similar to the dynamic mentions plugin, but does not dynamically suggest tags.

#### Configuration

-   Uncheck **Enable mentions**
-   Check everything else


### [on-site-notifications](https://github.com/q2apro/q2apro-on-site-notifications)

Notification icons similar to social media.

#### Configuration

-   Enable the plugin

### [open-search-support](../qa-plugin/opensearch-support)

#### Configuration

-   Enable the plugin

### [post-validator](../public/qa-plugin/post-validator)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/post-validator/README.md).

Warns users if they attempt to post identifying information.

#### Configuration

-   Check all options

### [pretty-tags](https://github.com/ProThoughts/q2apro-pretty-tags)

Auto-suggest and auto-complete tags as you type them.

This plugin has been modified to be more appropriate for this site, including UI changes.


### [q2a-style](../public/qa-plugin/q2a-style)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/q2a-style/README.md).

Various UI alterations, such as relocating the search bar, hiding redundant titles, highlighting the selected page name, etc.


### [random-avatar](../public/qa-plugin/random-avatar)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/random-avatar/README.md).

All new accounts get a Gravatar image, generated uniquely from their email address.

### [random-verse](../public/qa-plugin/random-verse)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/random-verse/README.md).

Selects a random verse to display in the sidebar in order to welcome users to the site. 

#### Configuration

-   Add verses you want to display in this plugin. Pay attention to the formatting of the verses.

### [reCAPTCHA](../public/qa-plugin/recaptcha-captcha)

#### Configuration

-   Get a reCAPTCHA site and secret key and set it in the settings

### [reclaim-account-title](../public/qa-plugin/reclaim-account-title)

#### Configuration

-   Enable the plugin

### [role-markers](https://github.com/gurjyot/q2a-role-markers)

Custom role names and markers adjacent to users with them.

This plugin has been modified to be more appropriate for this project.
Most notably, the ability to add custom roles to users has been implemented.
Administrators can now assign a unique role to users by visiting their profile.
This custom role will display next to the user's display name across the site, and the color for custom titles can be configured via CSS in the admin panel.

#### Configuration

-   Check all boxes

### [send-account-reclaim](../public/qa-plugin/send-account-reclaim)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/send-account-reclaim/README.md).

Send emails to users in the archived database table through the admin panel.
Toggleable support for HTML in email body.

#### Configuration

-   Set
    -   **From name**: `support.bible`
    -   **From email address**: `admin@support.bible`
    -   **Subject line**: whatever you want
    -   **Body text**: whatever you want


### [simple-sticky-sidebar](https://github.com/stefanmm/q2a-sticky-sidebar-plugin/)

Sidebar stays on the screen when you scroll down.

This plugin has been slightly modified according to the theme of the website.


### [spam-registration-stopper](https://github.com/pupi1985/q2a-pupi-srs)

Provides spam control through multiple well-known spam checking services.

#### Configuration

-  Enter API key of the spam checking services want to be used

### [sso-authentication](../public/qa-plugin/sso-authentication)

_Custom plugin_: This plugin was designed specifically for this website. More details can be found at the plugin's [README](../public/qa-plugin/sso-authentication/README.md).

This plugin allows single sign on for Facebook and Google.
Support for Paratext Registry SSO is not yet configured, as this requires external support.

#### Configuration

-   Add **Client id** and **Client secret** for each service selected

### [tagging-tools](https://github.com/svivian/q2a-tagging-tools)

Enforce min/max length for post tags and allows you to create "tag synonyms" which map similar tags to each other.
For example, the synonym `pt,paratext` would automatically convert the tag `pt` to `paratext` when a post is submitted.
It also has the ability to retroactively apply these synonyms.
Lastly, it can remove tags altogether by placing them on standalone lines.
During the migration from the old site to this one, we are automatically generating tag data for posts, so it will be useful to apply these synonyms retroactively.

#### Configuration

-   **Minimum tag length**: `2`
-   **Maximum tag length**: `25`
-   Do not check **Add 301 redirects for tag synonyms**

To see a list of all tag synonyms used in development (and thus, the synonyms we suggest using), refer to the [TagSynonyms file](../public/qa-plugin/q2a-tagging-tools/TagSynonyms.md).
Copy and paste all desired rules into the Tagging Tools' admin form.

### [title-length-counter](https://github.com/MominRaza/title-length-counter)

Display title length and prevent typing past the max length.

This plugin has been slightly modified to also display when editing questions.
It also displays warning colors when the post's title is below the minimum or at the maximum length.

### [user-info](https://github.com/ProThoughts/q2apro-userinfo)

Hover over a username to see information.

This plugin has been modified to be more appropriate for this site, including UI changes.
