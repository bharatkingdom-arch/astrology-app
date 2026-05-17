const https = require('https');
const query = 'tamil panchangam amritha siddha marana yoga table';
https.get('https://html.duckduckgo.com/html/?q=' + encodeURIComponent(query), (res) => {
  let data = '';
  res.on('data', (chunk) => data += chunk);
  res.on('end', () => console.log(data.length));
});
