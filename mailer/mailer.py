import os
import mysql.connector
import smtplib
from email.mime.text import MIMEText
import socket;
import re;

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

while 1:
    
    cursor = db.cursor()
    cursor.execute("SELECT postid, title FROM qa_posts")
    rows = cursor.fetchall()
    body = "\n".join(f"{postid}: {title}" for postid, title in rows)

    print("SMTP_USER: ", os.environ["SMTP_USER"])
    print("SMTP_TO_EMAIL: ", os.environ["SMTP_TO_EMAIL"])
    print("SMTP_HOST: ", os.environ["SMTP_HOST"])
    print("SMTP_PORT: ", int(os.environ["SMTP_PORT"]))
    print("SMTP_PASSWORD: ", os.environ["SMTP_PASSWORD"])

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

