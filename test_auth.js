async function testRegister() {
  const res = await fetch('http://localhost:4005/auth/register', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '14999888', password: 'admin123', role: 2 })
  });
  const data = await res.json();
  console.log('REGISTER => Status:', res.status, '| Response:', data);
  return data;
}

async function testLogin(username, password) {
  const res = await fetch('http://localhost:4005/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password })
  });
  const data = await res.json();
  console.log('LOGIN => Status:', res.status, '| Response:', JSON.stringify(data, null, 2));
  return data;
}

async function testProfile(token) {
  const res = await fetch('http://localhost:4005/auth/profile', {
    method: 'GET',
    headers: { 'Authorization': `Bearer ${token}` }
  });
  const data = await res.json();
  console.log('PROFILE => Status:', res.status, '| Response:', JSON.stringify(data, null, 2));
}

async function run() {
  console.log('\n=== PRUEBA REGISTRO (ADMINISTRADOR) ===');
  await testRegister();

  console.log('\n=== PRUEBA LOGIN (ESTUDIANTE) ===');
  const loginRes = await testLogin('24123456', 'secreta123');

  if (loginRes.access_token) {
    console.log('\n=== PRUEBA RUTA PROTEGIDA /profile ===');
    await testProfile(loginRes.access_token);
  }
}

run().catch(console.error);
