const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');
const lines = content.split('\n');

const stack = [];

lines.forEach((line, idx) => {
  const lineNum = idx + 1;
  
  // Find all HTML tags on this line
  const regex = /<\/?([a-zA-Z0-9]+)(?:\s+[^>]*)?>/g;
  let match;
  while ((match = regex.exec(line)) !== null) {
    const fullTag = match[0];
    const tagName = match[1];
    const isClosing = fullTag.startsWith('</');
    
    // Ignore self-closing/void tags and SVG presentation tags that we don't track carefully
    if (tagName === 'img' || tagName === 'input' || tagName === 'line' || tagName === 'circle' || tagName === 'path' || tagName === 'stop' || tagName === 'rect' || tagName === 'defs' || tagName === 'linearGradient' || tagName === 'br' || tagName === 'hr') {
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

console.log('Final stack size:', stack.length);
