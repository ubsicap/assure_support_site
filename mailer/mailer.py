import os
import mysql.connector
import smtplib
from email.mime.text import MIMEText
import socket;
import re;
from datetime import date, timedelta;

print("Start of mailer", flush=True)

def get_dns_info():
    dns_info = socket.getaddrinfo('smtp.zoho.com', None)
    for info in dns_info:
        print(info)

def check_internet():
    try:
        socket.create_connection(("smtp.zoho.com", 587))
        print("zoho Internet connection is available.")
    except OSError:
        print("No zoho Internet connection.")

import time
for i in range(10):
    try:
        db = mysql.connector.connect(
            host=os.environ["DB_HOST"],
            user=os.environ["DB_LOGIN"],
            password=os.environ["DB_KEY"],
            database=os.environ["DB_NAME"],
            port=3306,
        )
        break
    except mysql.connector.Error as err:
        print(f"Waiting for database... ({i+1}/10) - {err}", flush=True)
        time.sleep(5)
else:
    raise Exception("Could not connect to database after 10 attempts")

print("Succesful dbase connect", flush=True)

try:
    get_dns_info()
except socket.gaierror as e:
    print(f"Error: {e}")
check_internet()

paratextCategoryId = 1; # paratext
noRowsPerBuffer = 1000

sqlDrop = "DROP TABLE temp;"
cursor = db.cursor()
try:
   cursor.execute(sqlDrop);
except mysql.connectorError as err:
   print("Exception in DROP TABLE temp: ", err)

print("DROP TABLE temp done")

sqlCreate = f"CREATE TABLE temp AS \
    SELECT qa_posts.postid, qa_posts.type, qa_posts.parentid, qa_posts.categoryid, \
    qa_posts.created, qa_posts.title, qa_posts.content, qa_users.handle \
    FROM qa_posts JOIN qa_users on qa_users.userid = qa_posts.userid;"
cursor = db.cursor()
cursor.execute(sqlCreate)
rows = cursor.fetchall()
print("rows: ", rows)



while 1:
    
    cursor = db.cursor()
    cursor.execute("SELECT COUNT(*) from qa_posts")
    rows = cursor.fetchall();
    print("rows: ", rows)

    maxNoRows = rows[0][0]
    print("maxNoRows: ", maxNoRows, flush=True)
    maxPostId = maxNoRows - 1 - noRowsPerBuffer if maxNoRows - 1 - noRowsPerBuffer > 0 else 1
    print("maxPostId: ", maxPostId, flush=True)
    sortedRows = []

    while maxPostId >= 1:

       if maxPostId > maxNoRows: break # done with this loop                                                                                                                                 

       sqlQuery = f"SELECT q.postid AS question_id, \
           q.type as question_type, \
           q.title AS question_title, \
           q.created as question_created, \
           q.content AS question_content, \
           q.categoryid as question_categoryid, \
           a.postid AS answer_id, \
           a.content AS answer_content, \
           a.handle AS answer_author \
        FROM temp q \
        JOIN temp a ON a.parentid = q.postid \
        WHERE q.type = 'Q' AND ( a.type = 'A' OR a.type = 'C') \
              AND q.postid >= {maxPostId} AND q.postid <= {maxNoRows} \
              AND q.categoryid = {paratextCategoryId} \
        ORDER BY q.postid, a.postid;"

       #sqlQuery = f"SELECT created, postid, title, categoryid from qa_posts \
       # WHERE postid >=  {maxPostId} AND postid <= {maxNoRows};"
       print("sqlQuery: ", sqlQuery, flush=True)
       cursor.execute(sqlQuery)
       rows = cursor.fetchall()
       print("rows: ", rows)

       yesterday = date.today() - timedelta(days=1)
       yesterdaysDate = yesterday.strftime("%m/%d/%Y")
       print("yesterday date: ", yesterdaysDate)

       for question_id, question_type, question_title, question_created, question_content, \
           question_categoryid, answer_id, answer_content, answer_author  in rows:

          print("question_created: ", question_created.strftime("%m/%d/%Y"))
          print("question_id: ", question_id)
          print("answer_id: ", answer_id)
          print("categoryid: ", question_categoryid)

          if question_created.strftime("%m/%d/%Y") < yesterdaysDate:
             continue; # only include posts from before yesterday
        
          sortedRows.append([answer_id, question_title, question_content, question_categoryid])
        

       maxNoRows = 0 if maxNoRows - noRowsPerBuffer - 1 < 0 else maxNoRows - noRowsPerBuffer
       maxPostId = maxNoRows - noRowsPerBuffer if maxNoRows - noRowsPerBuffer > 0 else 1
       print("maxPostId: ", maxPostId, flush=True)

    print("sortedRows: ", sortedRows, flush=True)

    exit

    #cursor.execute("SELECT postid, title FROM qa_posts LIMIT noRows")
    rows = cursor.fetchall()
    body = "\n".join(f"{postid}: {title}" for postid, title in rows)

    host = os.environ["SMTP_HOST"].strip('"')
    clean_host = re.sub(r"^[\"']+|[\"']+$", "", host)
    port = int(os.environ["SMTP_PORT"])
    print(repr(clean_host))
    print(repr(port))

    msg = MIMEText(body)
    msg["Subject"] = "Recent Posts"
    msg["From"] = os.environ["SMTP_USER"]
    msg["To"] = os.environ["SMTP_TO_EMAIL"]

    try:
        with smtplib.SMTP(clean_host, port) as server:
            server.starttls()
            clean_user = re.sub(r"^[\"']+|[\"']+$", "", os.environ["SMTP_USER"])
            clean_passwd = re.sub(r"^[\"']+|[\"']+$", "", os.environ["SMTP_PASSWORD"])
            server.login(clean_user, clean_passwd)
            server.send_message(msg)
            print("Email sent!", flush=True)
    except:
        print("Exception in smtplib")




    time.sleep(24*60*60)
    # time.sleep(5)

print("Done with mailer", flush=True)

