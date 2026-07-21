const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

// The evaluations block starts on line 3279 (approx, index 3278)
// Let's find the closing parentheses of evaluations tab.
// Since evaluations is the last tab, it ends right before the main closing tags.
// Let's print the lines around line 5000 to find where it ends exactly.
const lines = content.split('\n');
for (let idx = 4970; idx < 5030; idx++) {
  if (idx < lines.length) {
    console.log(`Line ${idx + 1}: ${lines[idx]}`);
  }
}
