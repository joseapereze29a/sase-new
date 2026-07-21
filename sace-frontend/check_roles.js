const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const startIdx = content.indexOf('const ROLES');
if (startIdx !== -1) {
  console.log(content.substring(startIdx, startIdx + 500));
} else {
  console.log('ROLES not found');
}
