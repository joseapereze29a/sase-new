const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

// Search for buttons or forms that mention pensum, asignatura, adding subjects, etc.
const lines = content.split('\n');
lines.forEach((line, idx) => {
  if (line.toLowerCase().includes('asignatura') || line.toLowerCase().includes('pensum')) {
    if (line.toLowerCase().includes('button') || line.toLowerCase().includes('click') || line.toLowerCase().includes('add') || line.toLowerCase().includes('crear') || line.toLowerCase().includes('agregar')) {
      console.log(`Line ${idx + 1}: ${line.trim()}`);
    }
  }
});
