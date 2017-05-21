# GCWC Heroes of the Storm Draft System
The draft system which worked for Heroes of the Storm : 2016 Gold Club World Champoinship.

Open sources under GPL license.

The last hero in this draft system is ragnaros.

## System Requirement
This draft system was tested on following servers:
1. php 5.6 + (including 7.0+)
2. MariaDB 10.2.5
3. nginx 1.12.0

Also works with XAMPP for Windows.

**Please do remember to turn off the error and warning display as there's a warning which may make AJAX responding content invalid.**

## Installtion
This site will only works in the root directory of the HTTP server if you don't modify the code.

Placing the php files correctly and import the sql file to the database.

Configure the database connection in `/heroes/include/mysql.inc.php` And it will works.

Memcache support was changed to using a database table for a fake realization as the server at GCWC doesn't support Memcache.

## Usage
Create a draft session in `/heroes/create.gcwc.php`.

To join a draft session, use `/heroes/go.php`.

## Notice
According to the live executor of GCWC (Neither Blizzard nor NetEase, they were sponsors and organizers), the draft system won't take any action when the timer went 0.
If you need the auto-lockin, go `/heroes/include/judge.inc.php` and uncomment line 99-112.
