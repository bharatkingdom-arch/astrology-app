import psycopg2

conn_str = "postgresql://neondb_owner:npg_Mlb8CkEIcH6Z@ep-silent-queen-ao0yz2oh.c-2.ap-southeast-1.aws.neon.tech/neondb?sslmode=require"

try:
    conn = psycopg2.connect(conn_str)
    cur = conn.cursor()
    
    cur.execute("""
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    """)
    
    conn.commit()
    print("Table 'users' created successfully.")
    
    cur.close()
    conn.close()
except Exception as e:
    print(f"Error: {e}")
