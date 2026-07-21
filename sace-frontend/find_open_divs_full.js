const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');
const lines = content.split('\n');
const blockLines = lines.slice(5583, 6821);

const stack = [];

blockLines.forEach((line, idx) => {
  const lineNum = 5584 + idx;
  
  // Find all tags in this line
  const regex = /<\/?([a-zA-Z0-9]+)(?:\s+[^>]*)?>/g;
  let match;
  while ((match = regex.exec(line)) !== null) {
    const fullTag = match[0];
    const tagName = match[1];
    const isClosing = fullTag.startsWith('</');
    
    if (tagName === 'img' || tagName === 'input' || tagName === 'line' || tagName === 'circle' || tagName === 'path' || tagName === 'stop' || tagName === 'rect' || tagName === 'defs' || tagName === 'linearGradient') {
      continue;
    }
    
    if (isClosing) {
      if (stack.length > 0 && stack[stack.length - 1].name === tagName) {
        stack.pop();
      } else {
        console.log(`[Line ${lineNum}] Mismatched closing tag: </${tagName}>. Current top of stack:`, stack[stack.length - 1]);
      }
    } else {
      stack.push({ name: tagName, line: lineNum });
    }
  }
});

console.log('Remaining open tags in stack at end of file:');
console.log(stack);
