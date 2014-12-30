OverRustle.com
==========

Watch streams while chatting in Destiny.gg!

API server moved to: https://github.com/ILiedAboutCake/OverRustle-API/

# FAQ/Help
 
## Supported platforms
* Twitch
* Twitch VOD's
* CastAmp
* Hitbox
* Youtube live / Videos
* MLG.TV Streams
* Ustream
* Dailymotion
* Azubu
* Picarto.tv
* Advanced

## How to use the VOD / Youtube player

1. Get the link to the VOD / Video (ex http://www.twitch.tv/kaceytron/b/526235388 / https://www.youtube.com/watch?v=tMgkt9jdjTU)
2. Switch player to Twitch-VOD or Youtube and take the numbers after /b/ (ex 526235388) or /watch (tMgkt9jdjTU) and copy them into Stream/ID box
3. Press enter and enjoy watching!

## Secrets within

* You can set Youtube and twitch-VOD time by adding &s=time in seconds to the URL.

## Help MLG does not work

* MLG disabled embedding of their stream. Except they still allow TeamLiquid embed, so I embed that embed. To get this to work you need to know the TL embed name. Use the name in-between /embed/ and /popout.

## Ustream

* Ustream embeds work, but you cannot get to them by name. This is how Ustream runs their platform. Grab the embed number here http://i.imgur.com/PsPpK7v.png and use that for Stream/ID.

## Advanced
* You can embed any website that allows iframes, or does not throw a fit over CORS. 

## CastAmp

* usage of castamp is highly discouraged, best results in Chrome.

## Contact

* Message/PM ILiedAboutCake in destiny.gg
* send /u/ILiedAboutTheCake a message on reddit
* email iliedaboutthecake@gmail.com


# Install / Configure

## Requirements

OverRustle is known to work with the following:

* PHP5 (FPM) >= 5.4.35
* Composer >= 1.0-dev
* Nginx >= 1.6.2
* Redis >= 2.8.17

## PHP-FPM pool example
```
[rustle]

user = www-data
group = www-data

listen = 127.0.0.1:9001
listen.allowed_clients = 127.0.0.1

pm = ondemand
pm.max_children = 20
pm.start_servers = 3
pm.process_idle_timeout = 10s;
pm.max_requests = 2000


request_terminate_timeout = 120s
php_admin_value[memory_limit] = 128M
php_admin_value[max_execution_time] = 60
request_slowlog_timeout = 60s
slowlog = /var/log/php5-fpm.slow.log

chdir = /
```

# Donations

OverRustle.com runs free of charge, and donations are appreciated

* Paypal: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6TUMKXJ23YGQG
* Bitcoin: 14j82o37d8AShtVP5LydYCptVxwMhbu4Z1
* Dogecoin: DS6JdMnt96CVXEXZ2LNdjKq6kmcSD7mC88
* Linode: If you signup for a VPS on linode, we get a $20 hosting credit. https://www.linode.com/?r=57232eb9908d0f24a8907e61106c88f475248ac7
* DigitalOcean: You get $10 in hosting credit, we get $25 in hosting credit. https://www.digitalocean.com/?refcode=bca8aaedb677