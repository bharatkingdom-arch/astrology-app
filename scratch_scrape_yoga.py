import urllib.request
import re

url = "https://raw.githubusercontent.com/search?q=Amritha+Siddha+Marana+yoga&type=code"
try:
    req = urllib.request.Request("https://raw.githubusercontent.com/iniyamozhi/Tamil-Panchangam/master/panchangam.py", headers={'User-Agent': 'Mozilla/5.0'})
    response = urllib.request.urlopen(req)
    print("Found panchangam.py!")
except Exception as e:
    pass

url2 = "https://gist.githubusercontent.com/search?q=Tamil+Yoga+Marana+Siddha"
