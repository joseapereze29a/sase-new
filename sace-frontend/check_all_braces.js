const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

// We want to count open and close braces { and } and parentheses ( and ) to see if they match overall,
// and trace where they become unbalanced!
let braces = 0;
let parens = 0;
const lines = content.split('\n');
lines.forEach((line, idx) => {
  const lineNum = idx + 1;
  for (let c of line) {
    if (c === '{') braces++;
    if (c === '}') braces--;
    if (c === '(') parens++;
    if (c === ')') parens--;
  }
  if (braces < 0) {
    console.log(`[Line ${lineNum}] Braces went negative! Current: ${braces}`);
  }
  if (parens < 0) {
    console.log(`[Line ${lineNum}] Parens went negative! Current: ${parens}`);
  }
});

console.log('Final Braces balance:', braces);
console.log('Final Parens balance:', parens);
