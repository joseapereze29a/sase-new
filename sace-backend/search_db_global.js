const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function run() {
  console.log('Starting global search for 14152942...');
  
  // Let's query using raw SQL to be database-agnostic and search all tables
  const tablesResult = await prisma.$queryRaw`SHOW TABLES;`;
  const tables = tablesResult.map(r => Object.values(r)[0]);
  
  for (const table of tables) {
    const columnsResult = await prisma.$queryRawUnsafe(`DESCRIBE \`${table}\`;`);
    const columns = columnsResult.map(c => c.Field);
    
    // Construct search query
    const conditions = columns.map(col => `\`${col}\` = '14152942'`).join(' OR ');
    try {
      const results = await prisma.$queryRawUnsafe(`SELECT * FROM \`${table}\` WHERE ${conditions};`);
      if (results.length > 0) {
        console.log(`Found in table [${table}]:`, results);
      }
    } catch (e) {
      // Ignore type mismatch errors for specific columns
    }
  }
  console.log('Search completed.');
}

run().catch(console.error).finally(() => prisma.$disconnect());
