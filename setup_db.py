import psycopg2

conn_str = "postgresql://neondb_owner:npg_Mlb8CkEIcH6Z@ep-silent-queen-ao0yz2oh.c-2.ap-southeast-1.aws.neon.tech/neondb?sslmode=require"

try:
    conn = psycopg2.connect(conn_str)
    cur = conn.cursor()
    
    cur.execute("""
    CREATE TABLE IF NOT EXISTS kundlis (
        id SERIAL PRIMARY KEY,
        user_email VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        gender VARCHAR(50),
        birth_date DATE,
        birth_time TIME,
        latitude DECIMAL(10, 8),
        longitude DECIMAL(11, 8),
        planets JSONB,
        houses JSONB,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    """)
    
    conn.commit()
    print("Table 'kundlis' created successfully.")
    
    cur.close()
    conn.close()
except Exception as e:
    print(f"Error: {e}")
