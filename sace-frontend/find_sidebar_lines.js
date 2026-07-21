const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const lines = content.split('\n');
lines.forEach((line, idx) => {
  if (line.includes("setActiveTab('evaluaciones')") || line.includes("setActiveTab('usuarios')")) {
    console.log(`Line ${idx + 1}: ${line}`);
  }
});
