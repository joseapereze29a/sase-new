const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const startIdx = content.indexOf('useEffect(() =>');
if (startIdx !== -1) {
  let idx = 0;
  while ((idx = content.indexOf('useEffect(() =>', idx)) !== -1) {
    console.log('--- useEffect occurrence ---');
    console.log(content.substring(idx - 100, idx + 500).replace(/\n/g, ' '));
    idx += 15;
  }
} else {
  console.log('useEffect not found');
}
