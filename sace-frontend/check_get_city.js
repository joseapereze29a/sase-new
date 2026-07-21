const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

// Let's search for "getCityFromSede" or similar
const idx = content.indexOf('getCityFromSede');
if (idx !== -1) {
  console.log(content.substring(idx - 100, idx + 800));
} else {
  console.log('getCityFromSede not found');
}
