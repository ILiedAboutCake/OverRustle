# Copyright (c) Twisted Matrix Laboratories.
# See LICENSE for details.

# twisted imports
from twisted.words.protocols import irc
from twisted.internet import reactor, protocol
from twisted.python import log

#system imports
from time import gmtime, strftime
import time, sys, os

# generate logging time
def logTime():
	return strftime("%m-%d-%Y", gmtime())


def folderTime():
        return strftime("%B %Y", gmtime())

# logfile location
logSpot = "/srv/www/overrustle.com/logs/" + folderTime() + "/" + logTime() + ".txt"
logDir = "/srv/www/overrustle.com/logs/" +folderTime() + "/"

# make sure the month folder exists
try:
    os.makedirs(logDir)
    print "Creating Logging Folder" + logDir
except OSError:
    if os.path.exists(logDir):
	print "Logging location good to go: " + logSpot
        pass
    else:
	print "Logging location failure" + logSpot
        raise


# start of logging
class MessageLogger:
    def __init__(self, file):
        self.file = file

    def log(self, message):
        """Write a message to the file."""
        timestamp = time.strftime("[%H:%M:%S]", time.localtime(time.time()))
        self.file.write('%s %s\n' % (timestamp, message))
        self.file.flush()

    def close(self):
        self.file.close()


class LogBot(irc.IRCClient):
    
    nickname = "overrustleLogger"
    
    def connectionMade(self):
        irc.IRCClient.connectionMade(self)
        self.logger = MessageLogger(open(self.factory.filename, "a"))
        self.logger.log("[connected at %s]" % 
                        time.asctime(time.localtime(time.time())))

    def connectionLost(self, reason):
        irc.IRCClient.connectionLost(self, reason)
        self.logger.log("[disconnected at %s]" % 
                        time.asctime(time.localtime(time.time())))
        self.logger.close()



    def signedOn(self):
        """Called when bot has succesfully signed on to server."""
        self.join(self.factory.channel)

    def privmsg(self, user, channel, msg):
        """This will get called when the bot receives a message."""
        user = user.split('!', 1)[0]
        self.logger.log("<%s> %s" % (user, msg))
        
    def action(self, user, channel, msg):
        """This will get called when the bot sees someone do an action."""
        user = user.split('!', 1)[0]
        self.logger.log("* %s %s" % (user, msg))

    def alterCollidedNick(self, nickname):
        """
        Generate an altered version of a nickname that caused a collision in an
        effort to create an unused related name for subsequent registration.
        """
        return nickname + '^'



class LogBotFactory(protocol.ClientFactory):

    def __init__(self, channel, filename):
        self.channel = channel
        self.filename = filename

    def buildProtocol(self, addr):
        p = LogBot()
        p.factory = self
        return p

    def clientConnectionLost(self, connector, reason):
        """If we get disconnected, reconnect to server."""
        connector.connect()

    def clientConnectionFailed(self, connector, reason):
        print "connection failed:", reason
        reactor.stop()


if __name__ == '__main__':
    # initialize logging
    log.startLogging(sys.stdout)
    

    # create factory protocol and application
    f = LogBotFactory("destinyecho", logSpot)

    # connect factory to this host and port
    reactor.connectTCP("irc.rizon.net", 6667, f)

    # run bot
    reactor.run()