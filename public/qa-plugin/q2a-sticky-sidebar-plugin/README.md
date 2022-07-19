# Question2Answer Plugin: Sticky Sidebar #

----------

## Description ##

Just a simple plugin that will stick sidebar to bottom and top.
1) If user scrolls bottom, sidebar will scroll also until reach it's end. After that, sidebar will be fixed to bottom edge of browser. 
2) If user scrolls back to top, sidebar will scroll also until reach it'c top. After that, sidebar will be fixed to top edge of browser.

Plugin is based on this script: https://github.com/abouolia/sticky-sidebar/

## Demo ##
![demo gif](https://raw.githubusercontent.com/stefanmm/q2a-sticky-sidebar-plugin/master/sticky-sidebar-demo.gif)

## Installation ##

- Download the plugin as ZIP from [github](https://github.com/stefanmm/q2a-sticky-sidebar-plugin)
- Make a full backup of your q2a database before installing the plugin.
- Extract the folder ``q2a-sticky-sidebar`` from the ZIP file.
- Move the folder ``q2a-sticky-sidebar`` to the ``qa-plugin`` folder of your Q2A installation.
- Use your FTP-Client to upload the folder ``q2a-sticky-sidebar`` into the qa-plugin folder of your server.
- Navigate to your site, go to **Admin -> Plugins** and check if the plugin "Simple Sticky Sidebar" is listed.

## Setup ##

By default plugin works well with "SnowFlat" theme - no need to change anything! But, you can change settings to fit your needs. Basically, you just have to determinate id or class of side panel and parent div. For more info take a look [here](https://abouolia.github.io/sticky-sidebar/#usage).

## To-do ##

- Make better screen size detection
- Better fix for bug when using SnowFlat theme on mobile

## Disclaimer ##

The code is probably okay for production environments, but may not work exactly as expected. You bear the risk!

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.


## Copyright ##

All code herein is [OpenSource](http://www.gnu.org/licenses/gpl.html). Feel free to build upon it and share with the world.


## Final Note ##

I am not experienced PHP developer, I am front-end designer! So, my code is probably very bad, but it's working. Feel free to make it better, send suggestions. Thanks.
