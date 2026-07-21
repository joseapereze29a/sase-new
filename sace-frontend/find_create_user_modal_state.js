const fs = require('fs');

const content = fs.readFileSync('/app/src/app/page.tsx', 'utf8');

const target = "showCreateUserModal";
let idx = 0;
while (true) {
  const foundIdx = content.indexOf(target, idx);
  if (foundIdx === -1) break;
  console.log(`Found "showCreateUserModal" at char ${foundIdx}:`);
  console.log(content.substring(foundIdx - 100, foundIdx + 300));
  console.log('----------------------------------------------------');
  idx = foundIdx + 1;
}
