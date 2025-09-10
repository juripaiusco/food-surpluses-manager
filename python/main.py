import os
import sys
import mysql.connector as mariadb
import pandas as pd
from dotenv import load_dotenv
from pathlib import Path
import smtplib, ssl
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.application import MIMEApplication
from datetime import datetime

""" VAR - SET VARIABLES """
pathExcel = './excel'
load_dotenv(Path('./.laravel-env'))
today = datetime.now()

""" FNC - GET DATA FROM DB """
def getData(query, conn):
    cursor = conn.cursor()
    cursor.execute(query)
    dataTables = cursor.fetchall()

    return dataTables

""" ------------------------------------------------------------ """

""" TRY CONNECTION """
try:
    conn = mariadb.connect(
        user=os.getenv('DB_USERNAME'),
        password=os.getenv('DB_PASSWORD'),
        database=os.getenv('DB_DATABASE'),
        host='host.docker.internal' if os.getenv('DB_HOST') == 'db' else '127.0.0.1',
        port=os.getenv('DB_PORT')
    )
except mariadb.errors as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    sys.exit(1)

""" Select TABLES list """
dataTables = getData('SHOW TABLES', conn)

for dataTable in dataTables:

    tbl = dataTable[0]

    """ Select Table Columns - Data """
    dataTableColumns = getData('SHOW COLUMNS FROM ' + tbl, conn)

    """ Select Table Data - DataFrame """
    if (tbl == 'orders'):
        df_dataTable = pd.DataFrame(
            getData('SELECT * FROM ' + tbl + ' WHERE date LIKE \'' + today.strftime("%Y-%m") + '%\'', conn)
        )
    elif (tbl == 'stores'):
        df_dataTable = pd.DataFrame(
            getData('SELECT * FROM ' + tbl + ' WHERE date LIKE \'' + today.strftime("%Y-%m") + '%\'', conn)
        )
    else:
        df_dataTable = pd.DataFrame(getData('SELECT * FROM ' + tbl, conn))

    """ Eseguo l'esportazione in Excel """
    row = [[]]

    c = 0
    for d in dataTableColumns:
        row[0].append(d[0])
        c += 1

    df_tableHeader = pd.DataFrame(row)
    df_dataWithHeader = pd.concat([df_tableHeader, df_dataTable])
    df_dataWithHeader.to_excel(f'{pathExcel}/{tbl}.xlsx')

""" ------------------------------------------------------------ """

""" EXCEL ZIP - COMPRIMO I FILE EXCEL """
os.system(f'zip -r {pathExcel}.zip {pathExcel}')

""" EXCEL FILE - RIMUOVO I FILE EXCEL """
for file in os.listdir(pathExcel):
    os.remove(pathExcel + '/' + file)

""" ------------------------------------------------------------ """

""" EMAIL - INVIO MAIL CON ALLEGATO """
# Try to log in to server and send email
try:
    server = smtplib.SMTP(os.getenv('MAIL_HOST'), os.getenv('MAIL_PORT'))
    server.ehlo() # Can be omitted
    server.starttls(context=ssl.create_default_context()) # Secure the connection
    server.ehlo() # Can be omitted
    server.login(os.getenv('MAIL_USERNAME'), os.getenv('MAIL_PASSWORD'))
    # TODO: Send email here

    """ ------------------------- """
    email = getData('SELECT value FROM settings WHERE name="report_email"', conn)[0][0]
    subject = '[EmporioApp ' + os.getenv('APP_DOMAIN') + '] - '
    subject += 'DB2Excel del ' + datetime.today().strftime('%d/%m/%Y - %H:%M:%S')

    message = MIMEMultipart()
    message['Subject'] = subject
    message['From'] = 'emporioapp@emporiosd.it'
    message['To'] = email

    text = """\
    Ciao,

    trovi in allegato il database in formato Excel

    Buon lavoro,

    Bot EmporioApp
    """

    message.attach(MIMEText(text, 'plain'))

    filename = pathExcel + '.zip'
    with open(filename, 'rb') as file:
        message.attach(MIMEApplication(file.read(), Name='Excel.zip'))

    email_message = message.as_string()
    """ ------------------------- """

    server.sendmail('emporioapp@emporiosd.it', email, email_message)

except Exception as e:
    # Print any error messages to stdout
    print(e)
finally:
    server.quit()

os.remove(pathExcel + '.zip')
""" ------------------------------------------------------------ """

conn.close()
