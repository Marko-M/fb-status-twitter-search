Facebook Status Twitter Search
==============================
This is simple PHP application to demonstrate interfacing Facebook Graph API with Twitter Search API. The idea is to detect when Facebook user changes his Facebook status and to search Twitter for latest related tweets. Search results are then added as comments to that Facebook status message. Since it is necessary to remember user's Facebook status, this program needs an SQL database. Database stores user's last status ID and Facebook access token for later use. There is a basic login system in place to protect access to Facebook related information stored inside database. This program has been created as an execise and it doesn't have a lot of practical value.

Instructions
------------
Before testing Facebook Status Twitter Search for the first time you must configure database settings as well as Facebook App ID, APP Secret and APP URL. Configuration is done trough config.php file.
