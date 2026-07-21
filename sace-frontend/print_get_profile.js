const fs = require('fs');

const content = fs.readFileSync('/app/../sace-backend/src/auth/auth.service.ts', 'utf8');

const startIdx = content.indexOf('async getProfile');
if (startIdx !== -1) {
  console.log(content.substring(startIdx, startIdx + 800));
} else {
  console.log('getProfile not found');
}
