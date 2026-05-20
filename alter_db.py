import psycopg2

conn_str = "postgresql://neondb_owner:npg_Mlb8CkEIcH6Z@ep-silent-queen-ao0yz2oh.c-2.ap-southeast-1.aws.neon.tech/neondb?sslmode=require"

try:
    conn = psycopg2.connect(conn_str)
    cur = conn.cursor()
    
    # Add birth_place column if it doesn't exist
    cur.execute("""
    ALTER TABLE kundlis
    ADD COLUMN IF NOT EXISTS birth_place VARCHAR(255) DEFAULT 'Unknown';
    """)
    
    conn.commit()
    print("Column 'birth_place' added successfully.")
    
    cur.close()
    conn.close()
except Exception as e:
    print(f"Error: {e}")
