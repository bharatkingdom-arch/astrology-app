import urllib.request
import json
url = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=Amritha+Yoga+Marana&utf8=&format=json"
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
try:
    response = urllib.request.urlopen(req)
    data = json.loads(response.read().decode('utf-8'))
    for item in data['query']['search']:
        print(item['title'])
        print(item['snippet'])
except Exception as e:
    print(e)
