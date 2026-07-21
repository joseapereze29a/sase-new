const fs = require('fs');

const content = fs.readFileSync('/app/prisma/schema.prisma', 'utf8');

const idx = content.indexOf('prelaciones');
if (idx !== -1) {
  console.log(content.substring(idx - 100, idx + 800));
} else {
  console.log('prelaciones not found in schema.prisma');
}
