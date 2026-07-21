const fs = require('fs');

const content = fs.readFileSync('/app/src/academico/academico.service.ts', 'utf8');

const idx = content.indexOf('async createPensum');
if (idx !== -1) {
  console.log(content.substring(idx, idx + 1000));
} else {
  console.log('createPensum not found');
}
