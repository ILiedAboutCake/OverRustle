import threading
import tornado.httpserver
import tornado.websocket
import tornado.ioloop
import tornado.web
import json
import socket
 
#dem variables
numClients = 0
strims = {}

#takes care of updating console
def printStatus():
	threading.Timer(5.0, printStatus).start()
	print 'Currently connected clients: ' + str(numClients)
	
	for key, value in strims.items():
		print key, value

#ayy lmao
#if self.is_enlightened_by(self.intelligence):
#	self.is_euphoric = True

#Stat tracking websocket server
#Hiring PHP developers does not contribute to the quota of employees with disabilities.
class WSHandler(tornado.websocket.WebSocketHandler):
	def check_origin(self, origin):
		return True


	def open(self):
		global numClients
		print 'New connection: (' + self.request.remote_ip + ') ' + socket.getfqdn(self.request.remote_ip)


	def on_message(self, message):
		global strims
		global numClients
		fromClient = json.loads(message)

		#handle session counting
		if fromClient[u'action'] == "join":
			strims.setdefault(fromClient[u'strim'], 0)
			strims[fromClient[u'strim']] += 1
			numClients += 1
			data_string = json.dumps({"streams":strims[fromClient[u'strim']], "totalviewers":numClients}) 
			self.write_message(json.dumps(data_string))	
			print 'User Connected: Watching %s' % (fromClient[u'strim'])
		else:
			strims.setdefault(fromClient[u'strim'], 0)
			strims[fromClient[u'strim']]  -= 1
			numClients -= 1
			print 'User Disconnected: Was Watching %s' % (fromClient[u'strim'])

		#remove the dict key if nobody is watching DaFeels
		if strims[fromClient[u'strim']] <= 0:
			print 'Removing Dict value: %s' % (fromClient[u'strim'])
			strims.pop(fromClient[u'strim'], None)

	def on_close(self):
		print "Websocket connection closed."

#print console updates
printStatus()

#JSON api server
class APIHandler(tornado.web.RequestHandler):
    def get(self):
        self.write(json.dumps({"streams":strims, "totalviewers":numClients}))

#GET address handlers
application = tornado.web.Application([
    (r'/ws', WSHandler),
    (r'/api', APIHandler)
])
 
#starts the server on port 9998
if __name__ == "__main__":
	http_server = tornado.httpserver.HTTPServer(application)
	http_server.listen(9998)
	tornado.ioloop.IOLoop.instance().start()