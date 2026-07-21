const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

// Find all occurrences of getSedeFromCity
let idx = 0;
while ((idx = content.indexOf('getSedeFromCity', idx)) !== -1) {
  console.log('--- occurrence ---');
  console.log(content.substring(idx - 100, idx + 200).replace(/\n/g, ' '));
  idx += 15;
}
