import urllib.request
import json

# let's try to search github api for amritha siddha marana yoga
url = "https://api.github.com/search/code?q=Amritha+Siddha+Marana+Yoga"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    response = urllib.request.urlopen(req)
    print(response.read().decode('utf-8'))
except Exception as e:
    print(e)
