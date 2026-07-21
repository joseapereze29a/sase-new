const fs = require('fs');

const buf = fs.readFileSync('/app/binlog.000009');
const pos = 143887121;

const slice = buf.slice(pos - 50, pos + 250);
console.log('Surrounding ASCII:', slice.toString('ascii').replace(/[^ -~]/g, '.'));
console.log('Hex:', slice.toString('hex').match(/../g).join(' '));
