const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const lines = content.split('\n');
for (let idx = 1320; idx < 1365; idx++) {
  if (idx < lines.length) {
    console.log(`Line ${idx + 1}: ${lines[idx]}`);
  }
}
