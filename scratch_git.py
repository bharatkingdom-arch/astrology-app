import urllib.request
url = "https://raw.githubusercontent.com/sanskrit-coders/jyotisha/master/jyotisha/panchaanga/temporal/zodiac/nakshatra.py"
try:
    req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    response = urllib.request.urlopen(req)
    print("Found!")
except Exception as e:
    print(e)
