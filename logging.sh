# Cron job to restart logging at 12:01am 
# 1 0 * * * bash /srv/www/overrustle.com/logging.sh

killall screen;
screen -d -m python /srv/www/overrustle.com/irclogging.py;
