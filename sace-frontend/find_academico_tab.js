const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const startIdx = content.indexOf("{activeTab === 'academico' && (");
if (startIdx !== -1) {
  // Let's print 12000 characters of the academico tab to read its structure!
  console.log(content.substring(startIdx, startIdx + 12000));
} else {
  console.log('Academico tab not found');
}
