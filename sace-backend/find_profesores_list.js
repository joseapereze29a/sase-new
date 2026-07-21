const { PrismaClient } = require('@prisma/client');
const prisma = new PrismaClient();

async function findProfs() {
  try {
    const profs = await prisma.profesores.findMany({
      take: 10
    });
    console.log('Registered professors in database:', profs);
  } catch (err) {
    console.error('Error finding professors:', err.message);
  } finally {
    await prisma.$disconnect();
  }
}

findProfs();
