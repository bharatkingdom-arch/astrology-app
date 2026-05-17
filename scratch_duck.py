import urllib.request
import re

url = "https://html.duckduckgo.com/html/?q=Tamil+Amrithadi+yoga+nakshatra+table+siddha+marana+amritha"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})
try:
    response = urllib.request.urlopen(req)
    html = response.read().decode('utf-8')
    # try to find any text mentioning the rules
    text = re.sub('<[^<]+>', ' ', html)
    print(text[:1000])
except Exception as e:
    print(e)
