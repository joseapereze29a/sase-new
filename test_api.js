async function runTests() {
  const API_URL = 'http://localhost:4005';

  console.log('=== 1. OBTENIENDO TOKENS ===');
  
  // Login como administrador (cédula '14999888', rol = 2)
  console.log('Login como Admin (14999888)...');
  const adminLoginRes = await fetch(`${API_URL}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '14999888', password: 'admin123' }),
  });
  const adminData = await adminLoginRes.json();
  const adminToken = adminData.access_token;
  console.log('Admin Token obtenido:', !!adminToken);

  // Login como estudiante (cédula '24123456', rol = 5)
  console.log('Login como Estudiante (24123456)...');
  const studentLoginRes = await fetch(`${API_URL}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '24123456', password: 'secreta123' }),
  });
  const studentData = await studentLoginRes.json();
  const studentToken = studentData.access_token;
  console.log('Student Token obtenido:', !!studentToken);

  // Registrar Profesor (88888888) si no existe
  await fetch(`${API_URL}/auth/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '88888888', password: 'profesor123', role: 4 })
  }).catch(() => {});

  // Login como Profesor (88888888)
  console.log('Login como Profesor (88888888)...');
  const profLoginRes = await fetch(`${API_URL}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '88888888', password: 'profesor123' }),
  });
  const profData = await profLoginRes.json();
  const profToken = profData.access_token;
  console.log('Profesor Token obtenido:', !!profToken);

  console.log('\n=== 2. PRUEBA ACCESOS DATOS PERSONALES ===');
  
  // Crear expediente (Admin)
  console.log('Admin creando expediente 99999999...');
  const createRes = await fetch(`${API_URL}/datos-personales`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      cedula: 99999999,
      nombres: 'Test',
      apellidos: 'User',
      email: 'testuser@test.com',
      sexo: 'Masculino',
      nacionalidad: 'Venezolana'
    })
  });
  console.log('Crear expediente (Admin) Status:', createRes.status);
  if (createRes.status !== 201 && createRes.status !== 409) {
    console.log('Detalle error:', await createRes.text());
  }

  // Estudiante leyendo su propia cédula (Permitido)
  console.log('Estudiante leyendo expediente 24123456 (propio)...');
  const readOwnRes = await fetch(`${API_URL}/datos-personales/24123456`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Estudiante propio Status:', readOwnRes.status);
  if (readOwnRes.status === 200) {
    const data = await readOwnRes.json();
    console.log('Nombre leído:', data.nombres, data.apellidos);
  }

  // Estudiante leyendo cédula ajena (Prohibido)
  console.log('Estudiante leyendo expediente 14999888 (ajeno)...');
  const readOtherRes = await fetch(`${API_URL}/datos-personales/14999888`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Estudiante ajeno Status:', readOtherRes.status); // Debería ser 403

  // Admin leyendo cédula ajena (Permitido)
  console.log('Admin leyendo expediente 99999999...');
  const adminReadRes = await fetch(`${API_URL}/datos-personales/99999999`, {
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Admin leyendo Status:', adminReadRes.status); // Debería ser 200

  console.log('\n=== 3. PRUEBA ACCESOS ACADEMICOS ===');
  
  // Crear Oportunidad de Estudio (Admin)
  console.log('Admin creando oportunidad de estudio en sede S1, carrera M1...');
  const createOpRes = await fetch(`${API_URL}/academico/oportunidades`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      codsede: 'S1',
      codopest: 'M1',
      tipo: 'Maestria',
      mencion_especialidad: 'Inteligencia Artificial',
      periodos: 4,
      titulo_a_otorgar: 'Magister en IA'
    })
  });
  console.log('Crear Oportunidad (Admin) Status:', createOpRes.status);
  
  // Crear Oportunidad de Estudio (Estudiante - Prohibido)
  console.log('Estudiante intentando crear oportunidad de estudio...');
  const createOpStudentRes = await fetch(`${API_URL}/academico/oportunidades`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${studentToken}`
    },
    body: JSON.stringify({
      codsede: 'S1',
      codopest: 'M2',
      tipo: 'Maestria',
      mencion_especialidad: 'Prueba no autorizada',
      titulo_a_otorgar: 'Fallo'
    })
  });
  console.log('Crear Oportunidad (Estudiante) Status:', createOpStudentRes.status); // Debería ser 403

  // Crear asignatura en Pensum (Admin)
  console.log('Admin creando asignatura MAT1 en pensum...');
  const createPensumRes = await fetch(`${API_URL}/academico/pensum`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      codsede: 'S1',
      codopest: 'M1',
      codasig: 'MAT1',
      asignatura: 'Matemática Computacional',
      creditos: 3,
      periodos: 1,
      codasig_imp: 'MAT1'
    })
  });
  console.log('Crear Asignatura (Admin) Status:', createPensumRes.status);

  // Consultar oportunidades (Estudiante - Permitido)
  console.log('Estudiante consultando oportunidades...');
  const readOpsRes = await fetch(`${API_URL}/academico/oportunidades`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Oportunidades (Estudiante) Status:', readOpsRes.status);
  if (readOpsRes.status === 200) {
    const list = await readOpsRes.json();
    console.log('Total Oportunidades:', list.total);
  }

  // Consultar Pensum (Estudiante - Permitido)
  console.log('Estudiante consultando pensum...');
  const readPensumRes = await fetch(`${API_URL}/academico/pensum?codsede=S1&codopest=M1`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Pensum (Estudiante) Status:', readPensumRes.status);
  if (readPensumRes.status === 200) {
    const list = await readPensumRes.json();
    console.log('Asignaturas en Pensum:', list.items.map(i => i.asignatura));
  }

  console.log('\n=== 3.1. PRUEBA ACCESOS COHORTES ===');

  // Crear Cohorte (Admin)
  console.log('Admin creando cohorte S1/M1/COH2026...');
  const createCohRes = await fetch(`${API_URL}/cohortes`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      codsede: 'S1',
      codopest: 'M1',
      codcohorte: 'COH2026',
      fecha_inicio: '2026-09-01',
      periodo_lectivo: '2026-II'
    })
  });
  console.log('Crear Cohorte (Admin) Status:', createCohRes.status);

  // Crear Cohorte (Estudiante - Prohibido)
  console.log('Estudiante intentando crear cohorte...');
  const createCohStudentRes = await fetch(`${API_URL}/cohortes`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${studentToken}`
    },
    body: JSON.stringify({
      codsede: 'S1',
      codopest: 'M1',
      codcohorte: 'COH_FAIL',
      periodo_lectivo: '2026-II'
    })
  });
  console.log('Crear Cohorte (Estudiante) Status:', createCohStudentRes.status); // Debería ser 403

  // Consultar Cohortes (Estudiante - Permitido)
  console.log('Estudiante consultando cohortes...');
  const readCohsRes = await fetch(`${API_URL}/cohortes`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Cohortes (Estudiante) Status:', readCohsRes.status);
  if (readCohsRes.status === 200) {
    const list = await readCohsRes.json();
    console.log('Total Cohortes:', list.total);
  }

  // Consultar cohorte específica (Estudiante - Permitido)
  console.log('Estudiante consultando cohorte S1/M1/COH2026...');
  const readOneCohRes = await fetch(`${API_URL}/cohortes/S1/M1/COH2026`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Cohorte S1/M1/COH2026 (Estudiante) Status:', readOneCohRes.status);

  console.log('\n=== 3.2. PRUEBA ACCESOS PROFESORES ===');

  // Crear Profesor (Admin)
  console.log('Admin creando profesor 88888888...');
  const createProfRes = await fetch(`${API_URL}/profesores`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      cedula_profesor: 88888888,
      apellidos_nombres: 'Gomez Maria',
      nombres: 'Maria'
    })
  });
  console.log('Crear Profesor (Admin) Status:', createProfRes.status);

  // Crear Profesor (Estudiante - Prohibido)
  console.log('Estudiante intentando crear profesor...');
  const createProfStudentRes = await fetch(`${API_URL}/profesores`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${studentToken}`
    },
    body: JSON.stringify({
      cedula_profesor: 77777777,
      apellidos_nombres: 'No Autorizado',
      nombres: 'Fallo'
    })
  });
  console.log('Crear Profesor (Estudiante) Status:', createProfStudentRes.status); // Debería ser 403

  // Consultar Profesores General (Estudiante - Prohibido)
  console.log('Estudiante intentando listar profesores...');
  const readProfsStudentRes = await fetch(`${API_URL}/profesores`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Listar Profesores (Estudiante) Status:', readProfsStudentRes.status); // Debería ser 403

  // Consultar Profesor Individual (Estudiante - Permitido)
  console.log('Estudiante consultando profesor 88888888...');
  const readOneProfStudentRes = await fetch(`${API_URL}/profesores/88888888`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Profesor 88888888 (Estudiante) Status:', readOneProfStudentRes.status); // Debería ser 200

  console.log('\n=== 3.3. PRUEBA ACCESOS EVALUACIONES (ACTAS Y NOTAS) ===');

  // Crear Acta de Evaluación (Admin)
  console.log('Admin creando acta ACT2026 en cohorte S1/M1/COH2026, materia MAT1...');
  const createActaRes = await fetch(`${API_URL}/evaluaciones/actas`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      codcohorte: 'COH2026',
      codasig: 'MAT1',
      codacta: 'ACT2026',
      cedula_profesor: 88888888
    })
  });
  console.log('Crear Acta (Admin) Status:', createActaRes.status); // Debería ser 201

  // Intentar crear acta como Estudiante (Prohibido)
  console.log('Estudiante intentando crear acta...');
  const createActaStudentRes = await fetch(`${API_URL}/evaluaciones/actas`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${studentToken}`
    },
    body: JSON.stringify({
      codcohorte: 'COH2026',
      codasig: 'MAT1',
      codacta: 'ACT_FAIL',
      cedula_profesor: 88888888
    })
  });
  console.log('Crear Acta (Estudiante) Status:', createActaStudentRes.status); // Debería ser 403

  // Listar actas como Profesor (Debería ver la suya)
  console.log('Profesor consultando actas asignadas...');
  const readActasProfRes = await fetch(`${API_URL}/evaluaciones/actas`, {
    headers: { 'Authorization': `Bearer ${profToken}` }
  });
  console.log('Listar Actas (Profesor) Status:', readActasProfRes.status);
  if (readActasProfRes.status === 200) {
    const list = await readActasProfRes.json();
    console.log('Actas asignadas al profesor:', list.items.map(a => a.codacta));
  }

  // Registrar Calificación como Profesor asignado (Permitido)
  console.log('Profesor registrando nota 18 para estudiante 24123456...');
  const createNotaRes = await fetch(`${API_URL}/evaluaciones/notas`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${profToken}`
    },
    body: JSON.stringify({
      codacta: 'ACT2026',
      cedula: 24123456,
      calificacion: 18
    })
  });
  console.log('Registrar Calificación (Profesor asignado) Status:', createNotaRes.status); // Debería ser 201

  // Registrar Calificación como Profesor NO asignado (Prohibido)
  // Nota: Creamos otro profesor (77777777) y registramos su usuario
  await fetch(`${API_URL}/profesores`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify({
      cedula_profesor: 77777777,
      apellidos_nombres: 'Gomez Jose',
      nombres: 'Jose'
    })
  }).catch(() => {});
  await fetch(`${API_URL}/auth/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '77777777', password: 'profesor123', role: 4 })
  }).catch(() => {});
  const prof2LoginRes = await fetch(`${API_URL}/auth/login`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: '77777777', password: 'profesor123' }),
  });
  const prof2Data = await prof2LoginRes.json();
  const prof2Token = prof2Data.access_token;

  console.log('Profesor ajeno intentando registrar nota...');
  const createNotaOtherRes = await fetch(`${API_URL}/evaluaciones/notas`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${prof2Token}`
    },
    body: JSON.stringify({
      codacta: 'ACT2026',
      cedula: 24123456,
      calificacion: 20
    })
  });
  console.log('Registrar Calificación (Profesor ajeno) Status:', createNotaOtherRes.status); // Debería ser 403

  // Consultar su propia nota como Estudiante (Permitido)
  console.log('Estudiante consultando su propia nota...');
  const readOwnNotaRes = await fetch(`${API_URL}/evaluaciones/notas/ACT2026/24123456`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Nota Propia (Estudiante) Status:', readOwnNotaRes.status); // Debería ser 200
  if (readOwnNotaRes.status === 200) {
    const data = await readOwnNotaRes.json();
    console.log('Calificación obtenida:', data.calificacion);
  }

  // Consultar nota ajena como Estudiante (Prohibido)
  // Nota: Consultamos la nota de 99999999
  console.log('Estudiante consultando nota ajena (de 99999999)...');
  const readOtherNotaRes = await fetch(`${API_URL}/evaluaciones/notas/ACT2026/99999999`, {
    headers: { 'Authorization': `Bearer ${studentToken}` }
  });
  console.log('Consultar Nota Ajena (Estudiante) Status:', readOtherNotaRes.status); // Debería ser 403


  console.log('\n=== 4. LIMPIEZA ===');
  
  // Borrar asignatura
  const delAsig = await fetch(`${API_URL}/academico/pensum/S1/M1/MAT1`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Asignatura Status:', delAsig.status);

  // Borrar Oportunidad
  const delOp = await fetch(`${API_URL}/academico/oportunidades/S1/M1`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Oportunidad Status:', delOp.status);

  // Borrar Cohorte
  const delCoh = await fetch(`${API_URL}/cohortes/S1/M1/COH2026`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Cohorte Status:', delCoh.status);

  // Borrar Profesor
  const delProf = await fetch(`${API_URL}/profesores/88888888`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Profesor Status:', delProf.status);

  // Borrar Profesor 2
  const delProf2 = await fetch(`${API_URL}/profesores/77777777`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Profesor 2 Status:', delProf2.status);

  // Borrar Nota de Prueba
  const delNota = await fetch(`${API_URL}/evaluaciones/notas/ACT2026/24123456`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Nota de Prueba Status:', delNota.status);

  // Borrar Acta de Prueba
  const delActa = await fetch(`${API_URL}/evaluaciones/actas/COH2026/MAT1/ACT2026`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Acta Status:', delActa.status);

  // Borrar expediente de prueba
  console.log('Admin borrando expediente de prueba 99999999...');
  const delExp = await fetch(`${API_URL}/datos-personales/99999999`, {
    method: 'DELETE',
    headers: { 'Authorization': `Bearer ${adminToken}` }
  });
  console.log('Borrar Expediente Status:', delExp.status);
}

runTests().catch(console.error);
