const fs = require('fs');

const content = fs.readFileSync('/app/../sace-frontend/src/app/page.tsx', 'utf8');
console.log('Read page.tsx size:', content.length);

const targets = ['estadist', 'stats', 'grafic', 'chart'];

targets.forEach(target => {
  let count = 0;
  let idx = 0;
  while ((idx = content.toLowerCase().indexOf(target, idx)) !== -1) {
    count++;
    idx += target.length;
  }
  console.log(`Keyword '${target}' count:`, count);
});
