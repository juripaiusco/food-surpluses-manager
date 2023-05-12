import os
import sys
import mysql.connector as mariadb
import pandas as pd

""" Try connection """
try:
    conn = mariadb.connect(
        user='root',
        password="secret",
        database='emporiosd_app',
        host='db',
        port='3306'
    )
except mariadb.errors as e:
    print(f"Error connecting to MariaDB Platform: {e}")
    sys.exit(1)

pathToSave = './excel'

""" Select TABLES list """
query = 'SHOW TABLES'

cursor = conn.cursor()
cursor.execute(query)
dataTables = cursor.fetchall()

for dataTable in dataTables:

    tbl = dataTable[0]

    """ Select Table Columns """
    query = f'SHOW COLUMNS FROM {tbl}'

    cursor = conn.cursor()
    cursor.execute(query)
    dataTableColumns = cursor.fetchall()
    df_dataTableColumns = pd.DataFrame(dataTableColumns)

    """ Select Table Data """
    query = f'SELECT * FROM {tbl} LIMIT 0, 2'

    cursor = conn.cursor()
    cursor.execute(query)
    dataTable = cursor.fetchall()
    df_dataTable = pd.DataFrame(dataTable)

    """ Eseguo l'esportazione in Excel """
    row = [[]]

    c = 0
    for d in dataTableColumns:
        row[0].append(d[0])
        c += 1

    df_tableHeader = pd.DataFrame(row)

    df_dataWithHeader = pd.concat([df_tableHeader, df_dataTable])
    df_dataWithHeader.to_excel(f'{pathToSave}/{tbl}.xlsx')

conn.close()

""" Comprimo i file Excel """
os.system(f'zip -r {pathToSave}.zip {pathToSave}')