#!/usr/bin/env python3
import sqlite3
import os

db_file = '/vercel/sandbox/munna-finance-asset-app/database/munna_finance_asset.db'
if os.path.exists(db_file):
    os.remove(db_file)

conn = sqlite3.connect(db_file)
cursor = conn.cursor()

# Read and execute schema
with open('/vercel/sandbox/munna-finance-asset-app/database/schema.sql', 'r') as f:
    schema = f.read()
    cursor.executescript(schema)

# Read and execute seed
with open('/vercel/sandbox/munna-finance-asset-app/database/seed.sql', 'r') as f:
    seed = f.read()
    cursor.executescript(seed)

conn.commit()
conn.close()
print("Database created successfully!")