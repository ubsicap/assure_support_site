SPAM Registration Stopper [by [Gabriel Zanetti][author]]
========================================================

Description
-----------

SPAM Registration Stopper is a [Question2Answer][Q2A] plugin that prevents highly probable SPAM user registrations based on well-known SPAM checking services.

Features
--------

 * Prevents potential SPAM user registrations (users do not need to be removed as they are not even created)
 * SPAM users are tested against well-known services
   * [BotScout](https://botscout.com)
   * [FSpamlist](https://fspamlist.com)
   * [IsTempMail](https://www.istempmail.com)
   * [ProjectHoneyPot](https://www.projecthoneypot.org)
   * [StopForumSpam](https://www.stopforumspam.com)
   * [VerifierMeetChopra](https://verifier.meetchopra.com)
 * Each service is implemented as an internal plugin on its own, allowing for easy extension
 * Rejects registrations with duplicate emails
 * Rejects registrations with trash emails
 * Statistics for the last months are tracked for each service 
 * Internationalization support
 * No need for core hacks or plugin overrides
 * Simple installation

Bear in mind that the plugin will not assure in any way SPAM users won't register. The plugin will decrease the amount of these unwanted users.

Requirements
------------

 * Q2A version 1.8.0+
 * PHP 7.0.0+

Installation instructions
-------------------------

 1. Copy the plugin directory into the `qa-plugin` directory
 1. Enable the plugin from the *Admin -> Plugins* menu option
 1. Click on the `Save` button

Support
-------

If you have found a bug then create a ticket in the [Issues][issues] section.

Get the plugin
--------------

The plugin can be downloaded from [this link][download]. You can say thanks [donating using PayPal][paypal].

[Q2A]: https://www.question2answer.org
[author]: https://question2answer.org/qa/user/pupi1985
[download]: https://github.com/pupi1985/q2a-pupi-srs/archive/master.zip
[issues]: https://github.com/pupi1985/q2a-pupi-srs/issues
[paypal]: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=Y7LUM6ML4UV9L
