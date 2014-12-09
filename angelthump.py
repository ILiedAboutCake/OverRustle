from urllib2 import Request, urlopen, URLError, HTTPError
from subprocess import call
import threading
import datetime
import time

def plsDontKillYourself():
	req = Request('http://overrustle.com/strims')

	threading.Timer(60.0, plsDontKillYourself).start()
	
	try:
		response = urlopen(req)
	except URLError as e:
		if hasattr(e, 'reason'):
			print 'ERROR: unable to connect. Restarting Nginx + PHP5-FPM..'
			print 'Reason: ', e.reason
			call(["service", "nginx", "restart"])
			time.sleep(1)
			call(["service", "php5-fpm", "restart"])
		elif hasattr(e, 'code'):
			print 'ERROR: PHP stopped responding. Restarting PHP5-FPM...'
			print 'Error code: ', e.code
			call(["service", "php5-fpm", "restart"])

plsDontKillYourself()