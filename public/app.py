
import datetime
import apachelogs
import sys
from time import sleep

access_log = "/var/log/apache2/access.log";
error_log  = "/var/log/apache2/error.log";

#
#  Log Parsing Script
#  
#  1. Delta time without an entry in access.log
#     Log if delta time > 60 secs
#  2. Errors in the error.log (within delta time)
#     503, Segfault, Service unavailable, Apache shutdown
#   

sample_log = "C:\\Users\\bob_b\\assure_support_site\\public\\sample_log.txt";

def parseLogFile(logfile):

   print("logfile: " + logfile);

   fAccess = open(logfile, "r");
   logLine = fAccess.readline();
   parser = apachelogs.LogParser(apachelogs.COMBINED);

   print("after open");

   hour = minute = second = None;

   while logLine != None and logLine != "":

      if logLine.endswith("Apache is up"):
         print("Apache is up - logLine: " + logLine);
         logLine = fAccess.readline();
         return;

      try:
         parsed_obj = parser.parse(logLine);
         print("request_time: " + str(parsed_obj.request_time));

         # Log down time of 1 minute or more
         prevHour = hour;
         prevMinute = minute;
         prevSecond = second;
         hour = parsed_obj.request_time.hour;
         minute = parsed_obj.request_time.minute;
         second = parsed_obj.request_time.second;

         if prevHour == None or prevMinute == None or prevSecond == None: continue;

         deltaSecs = (hour - prevHour)*60*60 + (minute - prevMinute)*60 + (second - prevSecond);
         print("deltaSecs: " + str(deltaSecs));

         if deltaSecs >= 60:
            print("APACHE DOWN TIME: " + str(deltaSecs) + ", at time: " + str(parsed_obj.request_time));

      except:
         i = 0;


      logLine = fAccess.readline();
   # endwhile
   fAccess.close();

#parseLogFile(sample_log);
while 1:
   print("Parse access.log");
   parseLogFile(access_log);
   sleep(60);

print("Parse error.log");
parseLogFile(error_log);



