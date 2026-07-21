const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const lines = content.split('\n');
lines.forEach((line, idx) => {
  if (line.includes("activeTab === 'expedientes'") || line.includes("activeTab === 'academico'") || line.includes("activeTab === 'evaluaciones'")) {
    console.log(`Line ${idx + 1}: ${line}`);
  }
});
