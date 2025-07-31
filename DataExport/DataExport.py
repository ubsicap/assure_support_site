import os
import mysql.connector
import socket;
import argparse;
import csv;
import time

print("Start of Data Export", flush=True)

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

def main(noDays, categoryId):

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


    cursor = db.cursor()

    for i in range(1, 3):
        sqlShow = f"SHOW TABLES LIKE 'temp{i}'"
        print("sqlShow: ", sqlShow)
        cursor.execute(sqlShow)
        rows = cursor.fetchall()
        print("rows: ", rows)

        for row in rows:
           print("row: ", row)
           print("row 0: ", row[0])
           sqlDrop = f"DROP TABLE {row[0]};"
           print("sqlDrop: ", sqlDrop)
           cursor.execute(sqlDrop);


    sqlCreate = f"CREATE TABLE temp1 AS \
        SELECT qa_posts.postid, qa_posts.type, qa_posts.parentid, qa_posts.categoryid, \
        qa_posts.created, qa_posts.title, qa_posts.content, qa_users.handle \
        FROM qa_posts \
        JOIN qa_users on qa_users.userid = qa_posts.userid;"
    cursor = db.cursor()
    cursor.execute(sqlCreate)

    print("Create table temp1 done")

    sqlModifyColumn = f"ALTER TABLE temp1 MODIFY COLUMN content TEXT;"
    cursor = db.cursor()
    cursor.execute(sqlModifyColumn)

    sqlAddColumn = f"ALTER TABLE temp1 ADD uservotes int DEFAULT 0;"
    cursor = db.cursor()
    cursor.execute(sqlAddColumn)
    
    print("Alter Table temp1 done")

    sqlCreate = f"CREATE TABLE temp2 AS \
        SELECT q.postid AS question_id, \
        q.created AS question_created, \
        q.title AS question_title, \
        q.content AS question_content, \
        a.postid AS answer_id, \
        a.content AS answer_content, \
        a.handle AS answer_author, \
        q.uservotes AS uservotes \
        FROM temp1 q \
        JOIN temp1 a ON a.parentid = q.postid \
        WHERE q.type = 'Q' AND ( a.type = 'A' OR a.type = 'C') \
        AND q.categoryid = {categoryId} AND q.created >= DATE_SUB(CURDATE(), INTERVAL {noDays} DAY) \
        ORDER BY q.postid, a.postid;"

    cursor = db.cursor()
    cursor.execute(sqlCreate)

    sqlSelect = "SELECT * FROM temp2;"
    cursor.execute(sqlSelect)
    rows = cursor.fetchall()

    print("Create Table temp2 done")

    sqlSelect = "SELECT * FROM qa_uservotes;" 
    cursor = db.cursor()
    cursor.execute(sqlSelect)
    userVotesRows = cursor.fetchall()

    sqlSelect = "SELECT * FROM temp1;"
    cursor = db.cursor()
    cursor.execute(sqlSelect)
    temp1Rows = cursor.fetchall()

    #print("temp1 rows: ", temp1Rows)
    ##print("temp2 rows: ", rows)
    #print("userVoteRows: ", userVotesRows)

    tableList = []
    # update "uservotes" column with "votes" column from table "uservotes"
    for row in rows:
        rowList = list(row)
        for uvRow in userVotesRows:
            #print("rowList: ", rowList)
            if rowList[0] == uvRow[0]:
               rowList[7] = uvRow[2]
        tableList.append(rowList)

    #print("tableList after vote update")
    #print("tableList: ", tableList)


    columnLabels = ["Question Id", 
                    "Question Created Date",
                    "Question Title",
                    "Question Content",
                    "Answer Id",
                    "Answer Content",
                    "Author",
                    "User Votes"]



    allRows = []
    allRows.append(columnLabels)
    allRows.extend(tableList)


    try:
        with open("/app/outputdb.csv", 'w+', newline='\n') as file:
            writer = csv.writer(file)
            writer.writerows(allRows)
            print("rows written to disk")
    except Exception as e:
        print("open : ", e, flush=True)
        

    print("End of One Data Export Run", flush=True)

if __name__ == "__main__":

   parser = argparse.ArgumentParser()
   parser.add_argument('noDays', help='fetch data for noDays prior to today')
   parser.add_argument('categoryId', help='category of data being retrieved')
   args = parser.parse_args()
   print(f"noDays: {args.noDays}")
   print(f"categoryId: {args.categoryId}")

   while 1:
      main(args.noDays, args.categoryId)
      time.sleep(7*24*60*60)

    


