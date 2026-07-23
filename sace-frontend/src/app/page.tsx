'use client';

import { useState, useEffect } from 'react';

const ROLES: Record<number, string> = {
  1: 'Super Usuario',
  2: 'Administrador',
  3: 'Coordinador',
  4: 'Profesor',
  5: 'Estudiante',
};

// Types for data models
interface UserProfile {
  userId: number;
  username: string;
  role: number;
  cedula?: number | null;
  nombre_display?: string;
  perfil?: any;
}

export default function Home() {
  const [token, setToken] = useState<string | null>(null);
  const [profile, setProfile] = useState<UserProfile | null>(null);
  const [directorioSedes, setDirectorioSedes] = useState<any[]>([]);
  const [apiUrl, setApiUrl] = useState(() => {
    if (typeof window !== 'undefined') {
      if (window.location.hostname !== 'localhost') {
        return `${window.location.origin}/api`;
      }
    }
    return 'http://localhost:4005';
  });
  
  // Login form state
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  
  // UI Tabs & Views
  const [activeTab, setActiveTab] = useState<'dashboard' | 'expedientes' | 'academico' | 'cohortes' | 'profesores' | 'evaluaciones' | 'usuarios' | 'estadisticas' | 'configuracion_academica'>('dashboard');
  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  // Module data states
  const [stats, setStats] = useState({ expedientes: 0, cohortes: 0, profesores: 0, actas: 0 });
  const [students, setStudents] = useState<any[]>([]);
  const [searchCedula, setSearchCedula] = useState('');
  const [singleStudent, setSingleStudent] = useState<any | null>(null);
  const [programs, setPrograms] = useState<any[]>([]);
  const [pensum, setPensum] = useState<any[]>([]);
  const [cohortes, setCohortes] = useState<any[]>([]);
  const [profesores, setProfesores] = useState<any[]>([]);
  const [actas, setActas] = useState<any[]>([]);
  const [notas, setNotas] = useState<any[]>([]);

  // Selection states for details/editing
  const [selectedActa, setSelectedActa] = useState<any | null>(null);
  const [actaNotas, setActaNotas] = useState<any[]>([]);

  // Modals / Create forms states
  const [showCreateStudent, setShowCreateStudent] = useState(false);
  const [showFullDataModal, setShowFullDataModal] = useState(false);
  const [isEditingProfile, setIsEditingProfile] = useState(false);
  const [editableStudent, setEditableStudent] = useState<any | null>(null);
  const [showProgramDetailModal, setShowProgramDetailModal] = useState(false);
  const [selectedProgram, setSelectedProgram] = useState<any | null>(null);
  const [selectedProgramPensum, setSelectedProgramPensum] = useState<any[]>([]);
  const [programSearch, setProgramSearch] = useState('');
  const [searchVal, setSearchVal] = useState('');
  const [programPage, setProgramPage] = useState(1);
  const [totalPrograms, setTotalPrograms] = useState(0);
  const [selectedCity, setSelectedCity] = useState('');
  const [citiesList, setCitiesList] = useState<string[]>([]);
  const [selectedProgramFilter, setSelectedProgramFilter] = useState<any | null>(null);
  const [programsListByCity, setProgramsListByCity] = useState<any[]>([]);
  const [selectedCohorte, setSelectedCohorte] = useState<any | null>(null);
  const [cohorteActas, setCohorteActas] = useState<any[]>([]);
  const [showCohorteDetailModal, setShowCohorteDetailModal] = useState(false);
  const [loadingCohorteActas, setLoadingCohorteActas] = useState(false);
  const [isEditingCohorte, setIsEditingCohorte] = useState(false);
  const [editableCohorte, setEditableCohorte] = useState<any | null>(null);
  const [selectedActaDetail, setSelectedActaDetail] = useState<any | null>(null);
  const [actaNotasDetail, setActaNotasDetail] = useState<any[]>([]);
  const [showActaDetailModal, setShowActaDetailModal] = useState(false);
  const [loadingActaNotas, setLoadingActaNotas] = useState(false);
  const [isEditingActa, setIsEditingActa] = useState(false);
  const [editableActa, setEditableActa] = useState<any | null>(null);
  const [modalProfesoresList, setModalProfesoresList] = useState<any[]>([]);
  const [newStudent, setNewStudent] = useState({
    cedula: '', nombres: '', apellidos: '', email: '', telefono_celular: '', direccion: ''
  });

  const [showCreateCohorte, setShowCreateCohorte] = useState(false);
  const [saceUsers, setSaceUsers] = useState<any[]>([]);
  const [showCreateUserModal, setShowCreateUserModal] = useState(false);
  const [newUserAccount, setNewUserAccount] = useState({ username: '', password: '', role: 5, cedula: '', usuario: '' });
  const [showEditUserModal, setShowEditUserModal] = useState(false);
  const [selectedUserForEdit, setSelectedUserForEdit] = useState<any | null>(null);
  const [userSearchCedula, setUserSearchCedula] = useState('');
  const [searchedUserResult, setSearchedUserResult] = useState<any | null>(null);
  const [searchingUser, setSearchingUser] = useState(false);
  const [studentProfileData, setStudentProfileData] = useState<any | null>(null);
  const [loadingStudentProfile, setLoadingStudentProfile] = useState(false);
  const [cohorteSearch, setCohorteSearch] = useState('');
  const [cohortePage, setCohortePage] = useState(1);
  const [cohorteViewMode, setCohorteViewMode] = useState<'grid' | 'list'>('grid');
  const [actaSearch, setActaSearch] = useState('');
  const [actaPage, setActaPage] = useState(1);
  const [actaViewMode, setActaViewMode] = useState<'grid' | 'list'>('grid');
  const [newCohorte, setNewCohorte] = useState({
    codsede: '', codopest: '', codcohorte: '', periodo_lectivo: '2026-I', fecha_inicio: ''
  });
  const [modalSelectedCity, setModalSelectedCity] = useState('');
  const [modalPrograms, setModalPrograms] = useState<any[]>([]);

  const [showCreateTeacher, setShowCreateTeacher] = useState(false);
  const [teacherSearch, setTeacherSearch] = useState('');
  const [teacherPage, setTeacherPage] = useState(1);
  const [teacherViewMode, setTeacherViewMode] = useState<'grid' | 'list'>('grid');
  const [showTeacherDetailModal, setShowTeacherDetailModal] = useState(false);
  const [selectedTeacher, setSelectedTeacher] = useState<any | null>(null);
  const [isEditingTeacher, setIsEditingTeacher] = useState(false);
  const [editableTeacher, setEditableTeacher] = useState({ apellidos_nombres: '', nombres: '' });
  const [newTeacher, setNewTeacher] = useState({
    cedula_profesor: '', apellidos_nombres: '', nombres: ''
  });
  const [userSearch, setUserSearch] = useState('');
  const [userPage, setUserPage] = useState(1);

  const [showCreateActa, setShowCreateActa] = useState(false);
  const [newActa, setNewActa] = useState({
    codcohorte: '', codasig: '', codacta: '', cedula_profesor: ''
  });
  const [actaSelectedCity, setActaSelectedCity] = useState('');
  const [actaPrograms, setActaPrograms] = useState<any[]>([]);
  const [actaSelectedProgramCode, setActaSelectedProgramCode] = useState('');
  const [actaCohortes, setActaCohortes] = useState<any[]>([]);
  const [actaSubjects, setActaSubjects] = useState<any[]>([]);
  const [suggestedTeacherName, setSuggestedTeacherName] = useState('');

  const [showAddNota, setShowAddNota] = useState(false);
  const [newNota, setNewNota] = useState({
    codacta: '', cedula: '', calificacion: ''
  });

  // Filtros de búsqueda en la sección de evaluaciones
  const [filterCity, setFilterCity] = useState('');
  const [filterPrograms, setFilterPrograms] = useState<any[]>([]);
  const [filterProgramCode, setFilterProgramCode] = useState('');
  const [filterCohortes, setFilterCohortes] = useState<any[]>([]);
  const [filterCohorteCode, setFilterCohorteCode] = useState('');

  // Detect base URL (proxy vs port)
  useEffect(() => {
    if (typeof window !== 'undefined') {
      if (window.location.port === '8080' || window.location.port === '') {
        setApiUrl('/api');
      } else {
        setApiUrl('http://localhost:4005');
      }
    }
  }, []);

  const formatCalificacion = (calif: number | null | undefined): string => {
    if (calif === null || calif === undefined) return 'S/N';
    if (calif === 404) return 'Sin nota';
    if (calif === 99) return 'Reprobado';
    if (calif === 100) return 'Aprobada';
    if (calif === 110) return 'Meritorio';
    if (calif === 120) return 'Excelencia';
    if (calif === 212) return 'Equivalencia';
    return String(calif);
  };

  const getCalificacionColor = (calif: number | null | undefined): string => {
    if (calif === null || calif === undefined || calif === 404) return 'rgba(255,255,255,0.4)';
    if (calif === 99) return '#f87171';
    if (calif >= 10) return '#4ade80';
    return '#f87171';
  };

 
  // Sugerir código de cohorte dinámicamente según la sede, programa y período
  useEffect(() => {
    if (newCohorte.codsede && newCohorte.codopest && newCohorte.periodo_lectivo) {
      const suffix = newCohorte.codopest.includes('-') ? newCohorte.codopest.split('-')[1] : newCohorte.codopest;
      let periodCode = newCohorte.periodo_lectivo;
      const parts = newCohorte.periodo_lectivo.split('-');
      if (parts.length === 2) {
        const yearPart = parts[0];
        const periodPart = parts[1];
        const shortYear = yearPart.length === 4 ? yearPart.substring(2) : yearPart;
        periodCode = `${shortYear}-${periodPart}`;
      }
      const suggested = `${newCohorte.codsede}${suffix}${periodCode}`.toUpperCase().replace(/\s+/g, '');
      
      setNewCohorte(prev => {
        const prevSuffix = prev.codopest.includes('-') ? prev.codopest.split('-')[1] : prev.codopest;
        let prevPeriodCode = prev.periodo_lectivo;
        const prevParts = prev.periodo_lectivo.split('-');
        if (prevParts.length === 2) {
          prevPeriodCode = `${prevParts[0].length === 4 ? prevParts[0].substring(2) : prevParts[0]}-${prevParts[1]}`;
        }
        const lastSuggested = `${prev.codsede}${prevSuffix}${prevPeriodCode}`.toUpperCase().replace(/\s+/g, '');

        if (!prev.codcohorte || prev.codcohorte === lastSuggested) {
          return { ...prev, codcohorte: suggested };
        }
        return prev;
      });
    }
  }, [newCohorte.codsede, newCohorte.codopest, newCohorte.periodo_lectivo]);

  // Set request headers helper
  const getHeaders = (customToken = token) => ({
    'Content-Type': 'application/json',
    ...(customToken ? { Authorization: `Bearer ${customToken}` } : {}),
  });

  // Cargar programas al cambiar la ciudad en el modal de actas
  async function handleActaCityChange(city: string) {
    setActaSelectedCity(city);
    setActaPrograms([]);
    setActaSelectedProgramCode('');
    setActaCohortes([]);
    setActaSubjects([]);
    setSuggestedTeacherName('');
    setNewActa({ codcohorte: '', codasig: '', codacta: '', cedula_profesor: '' });
    
    if (!city) return;
    try {
      const res = await fetch(`${apiUrl}/cohortes/programas-por-ciudad/${encodeURIComponent(city)}`, { headers: getHeaders() });
      const data = await res.json();
      setActaPrograms(Array.isArray(data) ? data : []);
    } catch (e) {
      console.error(e);
    }
  }

  // Cargar cohortes y pensum al seleccionar un programa
  async function handleActaProgramChange(progCode: string) {
    setActaSelectedProgramCode(progCode);
    setActaCohortes([]);
    setActaSubjects([]);
    setSuggestedTeacherName('');
    setNewActa({ codcohorte: '', codasig: '', codacta: '', cedula_profesor: '' });

    const selectedProg = actaPrograms.find(p => p.codopest === progCode);
    if (!selectedProg) return;

    try {
      // 1. Cargar cohortes
      const resCohortes = await fetch(`${apiUrl}/cohortes?codsede=${selectedProg.codsede}&codopest=${selectedProg.codopest}`, { headers: getHeaders() });
      const dataCohortes = await resCohortes.json();
      setActaCohortes(dataCohortes.items || []);

      // 2. Cargar materias del pensum
      const resPensum = await fetch(`${apiUrl}/academico/pensum?codsede=${selectedProg.codsede}&codopest=${selectedProg.codopest}`, { headers: getHeaders() });
      const dataPensum = await resPensum.json();
      setActaSubjects(dataPensum.items || []);
    } catch (e) {
      console.error(e);
    }
  }

  // Sugerir código de acta y profesor al cambiar cohorte o asignatura
  useEffect(() => {
    if (newActa.codcohorte && newActa.codasig && apiUrl) {
      // Sugerir código de acta
      const cleanCohorte = newActa.codcohorte.replace(/-/g, '');
      const digitsMatch = newActa.codasig.match(/\d+/);
      let numSuffix = '01';
      if (digitsMatch) {
        const digits = digitsMatch[0];
        numSuffix = digits.length >= 2 ? digits.substring(digits.length - 2) : '0' + digits;
      } else {
        numSuffix = newActa.codasig.substring(newActa.codasig.length - 2).toUpperCase();
      }
      const suggestedActa = `${cleanCohorte}-${numSuffix}`.toUpperCase();
      setNewActa(prev => ({ ...prev, codacta: suggestedActa }));

      // Consultar sugerencia de profesor en el backend
      fetch(`${apiUrl}/evaluaciones/sugerir-profesor?codasig=${encodeURIComponent(newActa.codasig)}`, { headers: getHeaders() })
        .then(res => res.json())
        .then(data => {
          if (data && data.cedula_profesor) {
            setNewActa(prev => ({ ...prev, cedula_profesor: String(data.cedula_profesor) }));
            setSuggestedTeacherName(data.apellidos_nombres);
          } else {
            setNewActa(prev => ({ ...prev, cedula_profesor: '' }));
            setSuggestedTeacherName('');
          }
        })
        .catch(err => {
          console.error('Error suggesting teacher:', err);
          setSuggestedTeacherName('');
        });
    }
  }, [newActa.codcohorte, newActa.codasig, apiUrl]);

  function getProgramInitials(text: string) {
    if (!text) return '';
    const stopWords = new Set(['de', 'la', 'en', 'y', 'a', 'del', 'el', 'los', 'las', 'un', 'una', 'con', 'para', 'por', 'sobre', 'mencion']);
    const words = text
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9\s]/g, "")
      .split(/\s+/)
      .filter(w => w && !stopWords.has(w));
      
    return words.map(w => w[0].toUpperCase()).join('');
  }

  function suggestProgramCode(codsede: string, tipo: string, mencion: string, titulo: string) {
    if (!codsede) return '';
    const prefix = tipo === 'Doctorado' ? 'DR' : 
                   tipo === 'Maestria' ? 'MC' : 
                   tipo === 'Especializacion' ? 'ESP' : 'DIP';
                   
    const sourceText = (mencion || '').trim() || (titulo || '').trim();
    if (!sourceText) return `${prefix}-`;
    
    const initials = getProgramInitials(sourceText);
    const suggestedCode = `${prefix}-${initials}`;
    
    const exists = programs.some((p: any) => p.codsede === codsede && p.codopest === suggestedCode);
    if (!exists) {
      return suggestedCode;
    }
    
    let count = 2;
    while (programs.some((p: any) => p.codsede === codsede && p.codopest === `${suggestedCode}${count}`)) {
      count++;
    }
    return `${suggestedCode}${count}`;
  }

  function getCityFromSede(codsede: string) {
    const cleanCode = (codsede || '').toUpperCase().trim();
    const match = directorioSedes.find(s => (s.codsede || '').toUpperCase().trim() === cleanCode);
    if (match) return match.ciudad;

    const map: { [key: string]: string } = {
      EV: 'Entorno Virtual',
      COC: 'Barquisimeto',
      COC1: 'Maracay',
      COC2: 'Valencia',
      CPL: 'Los Teques',
      CUM: 'Cumana',
      MAT: 'Maturin',
      OCC: 'Maracaibo',
      OCC1: 'San  Cristóbal',
      ORN: 'Puerto La Cruz',
      PPAL: 'Caracas',
    };
    return map[cleanCode] || cleanCode;
  }

  async function handleGenerateActaFromCohorte() {
    if (!selectedCohorte) return;
    setShowCohorteDetailModal(false);
    const cityName = getCityFromSede(selectedCohorte.codsede);
    setShowCreateActa(true);
    setActaSelectedCity(cityName);
    
    try {
      const resProgs = await fetch(`${apiUrl}/cohortes/programas-por-ciudad/${encodeURIComponent(cityName)}`, { headers: getHeaders() });
      const progs = await resProgs.json();
      const actaProgs = Array.isArray(progs) ? progs : [];
      setActaPrograms(actaProgs);

      const resCohortes = await fetch(`${apiUrl}/cohortes?codsede=${selectedCohorte.codsede}&codopest=${selectedCohorte.codopest}`, { headers: getHeaders() });
      const dataCohortes = await resCohortes.json();
      setActaCohortes(dataCohortes.items || []);

      const resPensum = await fetch(`${apiUrl}/academico/pensum?codsede=${selectedCohorte.codsede}&codopest=${selectedCohorte.codopest}`, { headers: getHeaders() });
      const dataPensum = await resPensum.json();
      setActaSubjects(dataPensum.items || []);

      setActaSelectedProgramCode(selectedCohorte.codopest);
      setNewActa({
        codcohorte: selectedCohorte.codcohorte,
        codasig: '',
        codacta: '',
        cedula_profesor: ''
      });
      setSuggestedTeacherName('');
    } catch (e) {
      console.error('Error pre-filling Create Acta modal from cohorte:', e);
    }
  }

  // Manejadores para filtros de búsqueda en la sección de evaluaciones
  async function handleFilterCityChange(city: string) {
    setFilterCity(city);
    setFilterPrograms([]);
    setFilterProgramCode('');
    setFilterCohortes([]);
    setFilterCohorteCode('');
    setActaPage(1);
    
    if (!city) return;
    try {
      const res = await fetch(`${apiUrl}/cohortes/programas-por-ciudad/${encodeURIComponent(city)}`, { headers: getHeaders() });
      const data = await res.json();
      setFilterPrograms(Array.isArray(data) ? data : []);
    } catch (e) {
      console.error(e);
    }
  }

  async function handleFilterProgramChange(progCode: string) {
    setFilterProgramCode(progCode);
    setFilterCohortes([]);
    setFilterCohorteCode('');
    setActaPage(1);

    const selectedProg = filterPrograms.find(p => p.codopest === progCode);
    if (!selectedProg) return;

    try {
      const res = await fetch(`${apiUrl}/cohortes?codsede=${selectedProg.codsede}&codopest=${selectedProg.codopest}`, { headers: getHeaders() });
      const data = await res.json();
      setFilterCohortes(data.items || []);
    } catch (e) {
      console.error(e);
    }
  }

  function getSedeFromCity(city: string) {
    const cleanCity = (city || '').trim().toLowerCase();
    const match = directorioSedes.find(s => (s.ciudad || '').trim().toLowerCase() === cleanCity);
    if (match) return match.codsede;

    const map: { [key: string]: string } = {
      'entorno virtual': 'EV',
      barquisimeto: 'COC',
      maracay: 'COC1',
      valencia: 'COC2',
      'los teques': 'CPL',
      cumana: 'CUM',
      maturin: 'MAT',
      maracaibo: 'OCC',
      'san  cristóbal': 'OCC1',
      'puerto la cruz': 'ORN',
      caracas: 'PPAL',
    };
    return map[cleanCity] || '';
  }

  function extractSedeFromCohorte(codcohorte: string) {
    const dynamicPrefixes = directorioSedes.map(s => (s.codsede || '').toUpperCase().trim()).filter(Boolean);
    const fallbacks = ['EV', 'COC1', 'COC2', 'OCC1', 'COC', 'CPL', 'CUM', 'MAT', 'OCC', 'ORN', 'PPAL'];
    const prefixes = Array.from(new Set([...dynamicPrefixes, ...fallbacks]));
    
    for (const pref of prefixes) {
      if (codcohorte.toUpperCase().startsWith(pref)) {
        return pref;
      }
    }
    return '';
  }

  // 1. AUTHENTICATION LOGIC
  async function handleLogin(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al iniciar sesión');
      
      setToken(data.access_token);
      
      // Fetch profile
      const profRes = await fetch(`${apiUrl}/auth/profile`, {
        headers: { Authorization: `Bearer ${data.access_token}` },
      });
      const profData = await profRes.json();
      setProfile(profData);
      setMessage({ type: 'success', text: '¡Sesión iniciada correctamente!' });
      
      // Load initial dashboard stats
      loadStats(data.access_token, profData);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message || 'Error de conexión' });
    } finally {
      setLoading(false);
    }
  }

  function handleLogout() {
    setToken(null);
    setProfile(null);
    setUsername('');
    setPassword('');
    setMessage(null);
    setActiveTab('dashboard');
  }

  // 2. DASHBOARD DATA FETCHING
  async function loadStats(t: string, prof: UserProfile) {
    try {
      const hdrs = getHeaders(t);
      
      // Sedes (Todos pueden ver)
      try {
        const sedesRes = await fetch(`${apiUrl}/sedes`, { headers: hdrs });
        const sedesData = await sedesRes.json();
        setDirectorioSedes(Array.isArray(sedesData) ? sedesData : []);
      } catch (errSedes) {
        console.error('Error loading sedes:', errSedes);
      }

      // Programas & Pensum (Todos pueden ver)
      const progRes = await fetch(`${apiUrl}/academico/oportunidades`, { headers: hdrs });
      const progData = await progRes.json();
      const countProg = progData.total || 0;
      setPrograms(progData.items || []);
      loadConfigPrograms();

      const penRes = await fetch(`${apiUrl}/academico/pensum`, { headers: hdrs });
      const penData = await penRes.json();
      setPensum(penData.items || []);

      // Cohortes (Todos pueden ver)
      const cohRes = await fetch(`${apiUrl}/cohortes`, { headers: hdrs });
      const cohData = await cohRes.json();
      const countCoh = cohData.total || 0;
      setCohortes(cohData.items || []);

      // Profesores (Solo admin/coordinador masivo)
      let countProf = 0;
      if (prof.role <= 3) {
        const profRes = await fetch(`${apiUrl}/profesores`, { headers: hdrs });
        const profData = await profRes.json();
        countProf = profData.total || 0;
        setProfesores(profData.items || []);
      }

      // Actas de evaluación
      let countActas = 0;
      if (prof.role <= 4) {
        const actRes = await fetch(`${apiUrl}/evaluaciones/actas`, { headers: hdrs });
        const actData = await actRes.json();
        countActas = actData.total || 0;
        setActas(actData.items || []);
      }

      // Estudiantes (Solo admins/coordinadores)
      let countEstud = 0;
      if (prof.role <= 3) {
        const estudRes = await fetch(`${apiUrl}/datos-personales?take=1`, { headers: hdrs });
        const estudData = await estudRes.json();
        countEstud = estudData.total || 0;
      }

      // Notas del Estudiante y Perfil Completo
      if (prof.role === 5) {
        const noteRes = await fetch(`${apiUrl}/evaluaciones/notas`, { headers: hdrs });
        const noteData = await noteRes.json();
        setNotas(noteData.items || []);

        const studentRes = await fetch(`${apiUrl}/datos-personales/${prof.username}`, { headers: hdrs });
        const studentData = await studentRes.json();
        setStudentProfileData(studentData.error ? null : studentData);
      }

      setStats({
        expedientes: prof.role === 5 ? 1 : countEstud,
        cohortes: countCoh,
        profesores: countProf,
        actas: countActas
      });

    } catch (err) {
      console.error('Error loading dashboard stats:', err);
    }
  }

  // Refresh data based on active tab
  useEffect(() => {
    if (!token || !profile) return;
    const hdrs = getHeaders();

    if (activeTab === 'expedientes') {
      if (profile.role === 5) {
        // Estudiante consulta su propia cédula
        fetch(`${apiUrl}/datos-personales/${profile.username}`, { headers: hdrs })
          .then(res => res.json())
          .then(data => setSingleStudent(data.error ? null : data))
          .catch(e => console.error(e));
      }
    } else if (activeTab === 'cohortes') {
      fetch(`${apiUrl}/cohortes/ciudades`, { headers: hdrs })
        .then(res => res.json())
        .then(data => setCitiesList(Array.isArray(data) ? data : []))
        .catch(e => console.error(e));
    } else if (activeTab === 'profesores' && profile.role <= 4) {
      setTeacherSearch('');
      setTeacherPage(1);
      setSelectedTeacher(null);
      setIsEditingTeacher(false);
      
      if (profile.role === 4) {
        // Cargar ficha de profesor propia
        fetch(`${apiUrl}/profesores/${profile.username}`, { headers: hdrs })
          .then(res => res.json())
          .then(data => {
            setSelectedTeacher(data.error ? null : data);
            setEditableTeacher({
              apellidos_nombres: data.apellidos_nombres || '',
              nombres: data.nombres || ''
            });
          })
          .catch(e => console.error(e));
      } else {
        // Cargar directorio para admins/coordinadores
        fetch(`${apiUrl}/profesores`, { headers: hdrs })
          .then(res => res.json())
          .then(data => setProfesores(data.items || []))
          .catch(e => console.error(e));
      }
    } else if (activeTab === 'usuarios' && profile.role <= 2) {
      fetch(`${apiUrl}/auth/users`, { headers: hdrs })
        .then(res => res.json())
        .then(data => setSaceUsers(Array.isArray(data) ? data : []))
        .catch(e => console.error(e));
    } else if (activeTab === 'evaluaciones') {
      if (profile.role === 5) {
        setLoadingStudentProfile(true);
        fetch(`${apiUrl}/datos-personales/${profile.username}`, { headers: hdrs })
          .then(res => res.json())
          .then(data => setStudentProfileData(data.error ? null : data))
          .catch(e => console.error(e))
          .finally(() => setLoadingStudentProfile(false));
      } else {
        if (profile.role <= 4) {
          fetch(`${apiUrl}/evaluaciones/actas`, { headers: hdrs })
            .then(res => res.json())
            .then(data => setActas(data.items || []))
            .catch(e => console.error(e));
        }
        fetch(`${apiUrl}/evaluaciones/notas`, { headers: hdrs })
          .then(res => res.json())
          .then(data => setNotas(data.items || []))
          .catch(e => console.error(e));

        fetch(`${apiUrl}/cohortes/ciudades`, { headers: hdrs })
          .then(res => res.json())
          .then(data => setCitiesList(Array.isArray(data) ? data : []))
          .catch(e => console.error(e));
      }
    }
  }, [activeTab, token]);

  // Fetch programs list when selectedCity changes
  useEffect(() => {
    if (!selectedCity || !token) {
      setProgramsListByCity([]);
      setSelectedProgramFilter(null);
      return;
    }
    fetch(`${apiUrl}/cohortes/programas-por-ciudad/${encodeURIComponent(selectedCity)}`, { headers: getHeaders() })
      .then(res => res.json())
      .then(data => {
        setProgramsListByCity(Array.isArray(data) ? data : []);
        setSelectedProgramFilter(null);
      })
      .catch(e => console.error(e));
  }, [selectedCity, token]);

  // Fetch cohortes when selectedProgramFilter changes
  useEffect(() => {
    setCohortePage(1);
    setCohorteSearch('');
    if (!selectedProgramFilter || !token) {
      setCohortes([]);
      return;
    }
    const query = new URLSearchParams({
      codsede: selectedProgramFilter.codsede,
      codopest: selectedProgramFilter.codopest,
    });
    fetch(`${apiUrl}/cohortes?${query.toString()}`, { headers: getHeaders() })
      .then(res => res.json())
      .then(data => setCohortes(data.items || []))
      .catch(e => console.error(e));
  }, [selectedProgramFilter, token]);

  // Query individual student by Cedula
  async function handleSearchStudent(e: React.FormEvent) {
    e.preventDefault();
    if (!searchCedula) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/datos-personales/${searchCedula}`, { headers: getHeaders() });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'No se encontró el expediente');
      setSingleStudent(data);
    } catch (err: any) {
      setSingleStudent(null);
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  // Create Student
  async function handleCreateStudent(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/datos-personales`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
          cedula: Number(newStudent.cedula),
          nombres: newStudent.nombres,
          apellidos: newStudent.apellidos,
          email: newStudent.email,
          telefono_celular: newStudent.telefono_celular,
          direccion: newStudent.direccion,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al crear expediente');
      setMessage({ type: 'success', text: `Expediente de C.I. ${newStudent.cedula} creado exitosamente` });
      setSingleStudent(data);
      setShowCreateStudent(false);
      setNewStudent({ cedula: '', nombres: '', apellidos: '', email: '', telefono_celular: '', direccion: '' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleModalCityChange(city: string) {
    setModalSelectedCity(city);
    if (!city) {
      setModalPrograms([]);
      setNewCohorte(prev => ({ ...prev, codsede: '', codopest: '' }));
      return;
    }
    try {
      const res = await fetch(`${apiUrl}/cohortes/programas-por-ciudad/${encodeURIComponent(city)}`, { headers: getHeaders() });
      const data = await res.json();
      const programs = Array.isArray(data) ? data : [];
      setModalPrograms(programs);
      if (programs.length > 0) {
        setNewCohorte(prev => ({
          ...prev,
          codsede: programs[0].codsede,
          codopest: programs[0].codopest
        }));
      } else {
        setNewCohorte(prev => ({ ...prev, codsede: '', codopest: '' }));
      }
    } catch (e) {
      console.error(e);
    }
  }

  // Create Cohorte
  async function handleCreateCohorte(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/cohortes`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
          ...newCohorte,
          fecha_inicio: newCohorte.fecha_inicio ? new Date(newCohorte.fecha_inicio).toISOString() : undefined,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al crear cohorte');
      setMessage({ type: 'success', text: `Cohorte ${newCohorte.codcohorte} creada exitosamente` });
      setCohortes([data, ...cohortes]);
      setShowCreateCohorte(false);
      setNewCohorte({ codsede: '', codopest: '', codcohorte: '', periodo_lectivo: '2026-I', fecha_inicio: '' });
      setModalSelectedCity('');
      setModalPrograms([]);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  // Create Teacher
  async function handleCreateTeacher(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/profesores`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
          cedula_profesor: Number(newTeacher.cedula_profesor),
          apellidos_nombres: newTeacher.apellidos_nombres,
          nombres: newTeacher.nombres
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al registrar profesor');
      setMessage({ type: 'success', text: `Profesor ${newTeacher.apellidos_nombres} registrado exitosamente` });
      setProfesores([data, ...profesores]);
      setShowCreateTeacher(false);
      setNewTeacher({ cedula_profesor: '', apellidos_nombres: '', nombres: '' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  // Create Acta de Evaluación
  async function handleCreateActa(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/evaluaciones/actas`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
          codcohorte: newActa.codcohorte,
          codasig: newActa.codasig,
          codacta: newActa.codacta,
          cedula_profesor: newActa.cedula_profesor ? Number(newActa.cedula_profesor) : undefined
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al crear acta');
      setMessage({ type: 'success', text: `Acta ${newActa.codacta} creada exitosamente` });
      setActas([data, ...actas]);
      setShowCreateActa(false);
      setNewActa({ codcohorte: '', codasig: '', codacta: '', cedula_profesor: '' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  // View details of an Acta & Load its student grades
  async function handleViewActaDetails(acta: any) {
    setSelectedActa(acta);
    setShowActaDetailModal(true);
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/evaluaciones/notas?search=${acta.codacta}`, { headers: getHeaders() });
      const data = await res.json();
      setActaNotas(data.items || []);
    } catch (err) {
      console.error(err);
      setActaNotas([]);
    } finally {
      setLoading(false);
    }
  }

  // Add / Edit Grade
  async function handleSaveNota(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const payload = {
        codacta: newNota.codacta || selectedActa?.codacta,
        cedula: Number(newNota.cedula),
        calificacion: Number(newNota.calificacion),
      };

      const res = await fetch(`${apiUrl}/evaluaciones/notas`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al registrar nota');

      setMessage({ type: 'success', text: `Calificación de ${payload.calificacion} guardada correctamente para C.I. ${payload.cedula}` });
      
      // Reload grades for this acta in both contexts (evaluaciones and cohortes detail view)
      if (selectedActaDetail && selectedActaDetail.codacta === payload.codacta) {
        loadActaNotasDetail(selectedActaDetail.codacta);
      }
      if (selectedActa && selectedActa.codacta === payload.codacta) {
        handleViewActaDetails(selectedActa);
      }
      setShowAddNota(false);
      setNewNota({ codacta: '', cedula: '', calificacion: '' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleDownloadPensumPdf(codsede: string, codopest: string, programName: string) {
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/academico/pensum/pdf?codsede=${codsede}&codopest=${codopest}`, {
        headers: getHeaders()
      });
      if (!res.ok) {
        const errData = await res.json().catch(() => ({}));
        throw new Error(errData.message || 'No se pudo generar el PDF. Asegúrate de que el pensum tenga materias en esta sede.');
      }
      const blob = await res.blob();
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `pensum_${codopest}.pdf`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
      setMessage({ type: 'success', text: `Pensum de "${programName}" descargado correctamente.` });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleDownloadRecordPdf(cedula: number, codcohorte: string, programName: string) {
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/datos-personales/${cedula}/record-notas/pdf?codcohorte=${codcohorte}`, {
        headers: getHeaders()
      });
      if (!res.ok) {
        const errData = await res.json().catch(() => ({}));
        throw new Error(errData.message || 'No se pudo generar el PDF del récord de notas.');
      }
      const blob = await res.blob();
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `record_${cedula}_${codcohorte}.pdf`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);
      setMessage({ type: 'success', text: `Récord de calificaciones de "${programName}" descargado correctamente.` });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleSaveProfileChanges() {
    setLoading(true);
    setMessage(null);
    try {
      const payload = {
        ...editableStudent,
        cedula: undefined,
        nro_grupo_familiar: editableStudent.nro_grupo_familiar ? Number(editableStudent.nro_grupo_familiar) : null,
        ano: editableStudent.ano ? Number(editableStudent.ano) : null,
        fecha_nacimiento: editableStudent.fecha_nacimiento ? new Date(editableStudent.fecha_nacimiento).toISOString() : null,
        fecha_nacimiento_conyuge: editableStudent.fecha_nacimiento_conyuge ? new Date(editableStudent.fecha_nacimiento_conyuge).toISOString() : null,
        especializaciones: undefined,
        notas: undefined,
      };

      const res = await fetch(`${apiUrl}/datos-personales/${singleStudent.cedula}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify(payload),
      });

      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al guardar los cambios del perfil');

      setSingleStudent({
        ...singleStudent,
        ...data,
      });

      setIsEditingProfile(false);
      setMessage({ type: 'success', text: 'Ficha de datos personales actualizada correctamente.' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleSaveTeacherChanges(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedTeacher) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/profesores/${selectedTeacher.cedula_profesor}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify(editableTeacher),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al guardar los cambios del profesor');
      
      setSelectedTeacher(data);
      if (profile) {
        if (profile.role <= 3) {
          setProfesores(profesores.map(p => p.cedula_profesor === data.cedula_profesor ? data : p));
        }
      }
      setIsEditingTeacher(false);
      setMessage({ type: 'success', text: 'Ficha de datos del profesor actualizada correctamente.' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function fetchPrograms(page: number, search: string) {
    if (!token) return;
    setLoading(true);
    try {
      const hdrs = getHeaders();
      const skip = (page - 1) * 10;
      const take = 10;
      const queryParams = new URLSearchParams({
        skip: String(skip),
        take: String(take),
        ...(search ? { search } : {}),
      });
      const res = await fetch(`${apiUrl}/academico/oportunidades?${queryParams.toString()}`, { headers: hdrs });
      const data = await res.json();
      setPrograms(data.items || []);
      setTotalPrograms(data.total || 0);
    } catch (e) {
      console.error('Error fetching programs:', e);
    } finally {
      setLoading(false);
    }
  }

  async function loadProgramPensum(codopest: string) {
    try {
      const res = await fetch(`${apiUrl}/academico/pensum?codopest=${codopest}`, {
        headers: getHeaders(),
      });
      const data = await res.json();
      const uniqueMap = new Map<string, any>();
      (data.items || []).forEach((a: any) => {
        if (!uniqueMap.has(a.codasig)) {
          uniqueMap.set(a.codasig, a);
        }
      });
      setSelectedProgramPensum(Array.from(uniqueMap.values()));
    } catch (e) {
      console.error('Error loading program pensum:', e);
    }
  }

  async function loadCohorteActas(codcohorte: string) {
    setLoadingCohorteActas(true);
    try {
      const res = await fetch(`${apiUrl}/evaluaciones/actas?codcohorte=${encodeURIComponent(codcohorte)}`, {
        headers: getHeaders(),
      });
      const data = await res.json();
      setCohorteActas(data.items || []);
    } catch (e) {
      console.error('Error loading cohorte actas:', e);
      setCohorteActas([]);
    } finally {
      setLoadingCohorteActas(false);
    }
  }

  async function loadActaNotasDetail(codacta: string) {
    setLoadingActaNotas(true);
    try {
      const res = await fetch(`${apiUrl}/evaluaciones/notas?codacta=${encodeURIComponent(codacta)}`, {
        headers: getHeaders(),
      });
      const data = await res.json();
      setActaNotasDetail(data.items || []);
    } catch (e) {
      console.error('Error loading acta notas:', e);
      setActaNotasDetail([]);
    } finally {
      setLoadingActaNotas(false);
    }
  }

  async function handleSaveCohorteChanges() {
    if (!token || !selectedCohorte || !editableCohorte) return;
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/cohortes/${selectedCohorte.codsede}/${selectedCohorte.codopest}/${selectedCohorte.codcohorte}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
          periodo_lectivo: editableCohorte.periodo_lectivo,
          fecha_inicio: editableCohorte.fecha_inicio ? new Date(editableCohorte.fecha_inicio).toISOString() : null,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al actualizar cohorte');

      const updatedCohortes = cohortes.map(c => 
        (c.codsede === selectedCohorte.codsede && c.codopest === selectedCohorte.codopest && c.codcohorte === selectedCohorte.codcohorte)
          ? { ...c, ...data }
          : c
      );
      setCohortes(updatedCohortes);
      setSelectedCohorte({ ...selectedCohorte, ...data });
      setIsEditingCohorte(false);
      setMessage({ type: 'success', text: `Cohorte ${selectedCohorte.codcohorte} actualizada con éxito.` });
    } catch (e: any) {
      setMessage({ type: 'error', text: e.message });
    } finally {
      setLoading(false);
    }
  }

  async function startEditingActa() {
    if (!token) return;
    setIsEditingActa(true);
    try {
      const res = await fetch(`${apiUrl}/profesores`, { headers: getHeaders() });
      const data = await res.json();
      setModalProfesoresList(data.items || []);
    } catch (e) {
      console.error('Error fetching teachers for dropdown:', e);
    }
  }

  async function handleSaveActaChanges() {
    if (!token || !selectedActaDetail || !editableActa) return;
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/evaluaciones/actas/${selectedActaDetail.codcohorte}/${selectedActaDetail.codasig}/${selectedActaDetail.codacta}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
          cedula_profesor: editableActa.cedula_profesor ? Number(editableActa.cedula_profesor) : null,
          fecha_aprobacion: editableActa.fecha_aprobacion ? new Date(editableActa.fecha_aprobacion).toISOString() : null,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al actualizar acta');

      const updatedActas = cohorteActas.map(a => 
        (a.codcohorte === selectedActaDetail.codcohorte && a.codasig === selectedActaDetail.codasig && a.codacta === selectedActaDetail.codacta)
          ? { ...a, ...data }
          : a
      );
      setCohorteActas(updatedActas);
      setSelectedActaDetail({ ...selectedActaDetail, ...data });
      setIsEditingActa(false);
      setMessage({ type: 'success', text: `Acta ${selectedActaDetail.codacta} actualizada con éxito.` });
    } catch (e: any) {
      setMessage({ type: 'error', text: e.message });
    } finally {
      setLoading(false);
    }
  }

  // =========================================================================
  // GESTION DINAMICA DE SEDES Y PROGRAMAS DE ESTUDIO (MODULO 13)
  // =========================================================================
  const [showCreateSedeModal, setShowCreateSedeModal] = useState(false);
  const [showEditSedeModal, setShowEditSedeModal] = useState(false);
  const [selectedSedeForEdit, setSelectedSedeForEdit] = useState<any | null>(null);
  const [newSedeAccount, setNewSedeAccount] = useState({
    codsede: '',
    modalidad: 'Sede' as any,
    director_coordinador: '',
    direccion: '',
    ciudad: '',
    edo_prov: '',
    fax: '',
    email: '',
  });

  const [showCreateProgramModal, setShowCreateProgramModal] = useState(false);
  const [showEditProgramModal, setShowEditProgramModal] = useState(false);
  const [selectedProgramForEdit, setSelectedProgramForEdit] = useState<any | null>(null);
  const [newProgramAccount, setNewProgramAccount] = useState({
    codsede: '',
    codopest: '',
    mencion_especialidad: '',
    titulo_a_otorgar: '',
    tipo: 'Maestria',
    creditos: 0,
  });

  // Chart Expansion States (Módulo 13 - UI Detail)
  const [expandedChart, setExpandedChart] = useState<'sedes' | 'programas' | 'anos' | null>(null);

  // Subject (Asignaturas) CRUD States
  const [showCreateSubjectModal, setShowCreateSubjectModal] = useState(false);
  const [showEditSubjectModal, setShowEditSubjectModal] = useState(false);
  const [selectedSubjectForEdit, setSelectedSubjectForEdit] = useState<any | null>(null);
  const [newSubjectAccount, setNewSubjectAccount] = useState({
    codasig: '',
    codasig_imp: '',
    asignatura: '',
    creditos: 3,
    periodos: 1,
    prelacionesRaw: '',
  });

  // Subject CRUD Handlers
  async function handleCreateSubject(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedProgram) return;
    setLoading(true);
    setMessage(null);
    try {
      const prels = newSubjectAccount.prelacionesRaw
        .split(',')
        .map((p) => p.trim().toUpperCase())
        .filter(Boolean);

      const res = await fetch(`${apiUrl}/academico/pensum`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
          codsede: selectedProgram.codsede,
          codopest: selectedProgram.codopest,
          codasig: newSubjectAccount.codasig.toUpperCase().trim(),
          codasig_imp: newSubjectAccount.codasig_imp.toUpperCase().trim(),
          asignatura: newSubjectAccount.asignatura.trim(),
          creditos: Number(newSubjectAccount.creditos),
          periodos: Number(newSubjectAccount.periodos),
          status: 'Activa',
          prelaciones: prels,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al crear asignatura');
      setMessage({ type: 'success', text: `Asignatura "${newSubjectAccount.asignatura}" creada con éxito.` });
      setShowCreateSubjectModal(false);
      setNewSubjectAccount({
        codasig: '',
        codasig_imp: '',
        asignatura: '',
        creditos: 3,
        periodos: 1,
        prelacionesRaw: '',
      });
      loadProgramPensum(selectedProgram.codopest);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleUpdateSubject(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedProgram || !selectedSubjectForEdit) return;
    setLoading(true);
    setMessage(null);
    try {
      const prels = (selectedSubjectForEdit.prelacionesRaw || '')
        .split(',')
        .map((p: string) => p.trim().toUpperCase())
        .filter(Boolean);

      const res = await fetch(`${apiUrl}/academico/pensum/${selectedProgram.codsede}/${selectedProgram.codopest}/${selectedSubjectForEdit.codasig}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
          codasig_imp: selectedSubjectForEdit.codasig_imp.toUpperCase().trim(),
          asignatura: selectedSubjectForEdit.asignatura.trim(),
          creditos: Number(selectedSubjectForEdit.creditos),
          periodos: Number(selectedSubjectForEdit.periodos),
          prelaciones: prels,
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al actualizar asignatura');
      setMessage({ type: 'success', text: `Asignatura "${selectedSubjectForEdit.asignatura}" actualizada con éxito.` });
      setShowEditSubjectModal(false);
      setSelectedSubjectForEdit(null);
      loadProgramPensum(selectedProgram.codopest);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleDeleteSubject(codsede: string, codopest: string, codasig: string, name: string) {
    if (!confirm(`¿Estás seguro de que deseas eliminar la asignatura "${name}" (${codasig})?`)) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/academico/pensum/${codsede}/${codopest}/${codasig}`, {
        method: 'DELETE',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al eliminar asignatura');
      setMessage({ type: 'success', text: `Asignatura "${name}" eliminada correctamente.` });
      if (selectedProgram) {
        loadProgramPensum(selectedProgram.codopest);
      }
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  // Search & Pagination States for Configuración Académica (Módulo 13)
  const [configSedeSearch, setConfigSedeSearch] = useState('');
  const [configProgramSearch, setConfigProgramSearch] = useState('');
  const [configProgramPage, setConfigProgramPage] = useState(1);
  const [configProgramTotal, setConfigProgramTotal] = useState(0);
  const [configProgramsList, setConfigProgramsList] = useState<any[]>([]);

  async function loadConfigPrograms() {
    try {
      const skip = (configProgramPage - 1) * 10;
      const query = new URLSearchParams({
        skip: String(skip),
        take: '10',
      });
      if (configProgramSearch) {
        query.append('search', configProgramSearch);
      }
      const res = await fetch(`${apiUrl}/academico/oportunidades?${query.toString()}`, {
        headers: getHeaders(),
      });
      const data = await res.json();
      setConfigProgramsList(data.items || []);
      setConfigProgramTotal(data.total || 0);
    } catch (err) {
      console.error('Error loading config programs:', err);
    }
  }

  useEffect(() => {
    if (activeTab === 'configuracion_academica' && token) {
      loadConfigPrograms();
    }
  }, [activeTab, configProgramPage, configProgramSearch, token]);

  // Sedes CRUD Handlers
  async function handleCreateSede(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/sedes`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(newSedeAccount),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al crear sede');
      setMessage({ type: 'success', text: `Sede "${newSedeAccount.ciudad}" creada con éxito.` });
      setShowCreateSedeModal(false);
      setNewSedeAccount({
        codsede: '',
        modalidad: 'Sede' as any,
        director_coordinador: '',
        direccion: '',
        ciudad: '',
        edo_prov: '',
        fax: '',
        email: '',
      });
      const sedesRes = await fetch(`${apiUrl}/sedes`, { headers: getHeaders() });
      const sedesData = await sedesRes.json();
      setDirectorioSedes(Array.isArray(sedesData) ? sedesData : []);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleUpdateSede(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedSedeForEdit) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/sedes/${selectedSedeForEdit.codsede}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify(selectedSedeForEdit),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al actualizar sede');
      setMessage({ type: 'success', text: `Sede "${selectedSedeForEdit.ciudad}" actualizada con éxito.` });
      setShowEditSedeModal(false);
      setSelectedSedeForEdit(null);
      const sedesRes = await fetch(`${apiUrl}/sedes`, { headers: getHeaders() });
      const sedesData = await sedesRes.json();
      setDirectorioSedes(Array.isArray(sedesData) ? sedesData : []);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleDeleteSede(codsede: string, ciudad: string) {
    if (!confirm(`¿Estás seguro de que deseas eliminar la sede "${ciudad}"?`)) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/sedes/${codsede}`, {
        method: 'DELETE',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al eliminar sede');
      setMessage({ type: 'success', text: `Sede "${ciudad}" eliminada correctamente.` });
      const sedesRes = await fetch(`${apiUrl}/sedes`, { headers: getHeaders() });
      const sedesData = await sedesRes.json();
      setDirectorioSedes(Array.isArray(sedesData) ? sedesData : []);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  // Programs CRUD Handlers
  async function handleCreateProgram(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/academico/oportunidades`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify({
          ...newProgramAccount,
          creditos: Number(newProgramAccount.creditos),
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al crear programa');
      setMessage({ type: 'success', text: `Programa "${newProgramAccount.titulo_a_otorgar}" creado con éxito.` });
      setShowCreateProgramModal(false);
      setNewProgramAccount({
        codsede: '',
        codopest: '',
        mencion_especialidad: '',
        titulo_a_otorgar: '',
        tipo: 'Maestria',
        creditos: 0,
      });
      const progRes = await fetch(`${apiUrl}/academico/oportunidades`, { headers: getHeaders() });
      const progData = await progRes.json();
      setPrograms(progData.items || []);
      loadConfigPrograms();
      loadConfigPrograms();
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleUpdateProgram(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedProgramForEdit) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/academico/oportunidades/${selectedProgramForEdit.codsede}/${selectedProgramForEdit.codopest}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify({
          ...selectedProgramForEdit,
          creditos: Number(selectedProgramForEdit.creditos),
        }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al actualizar programa');
      setMessage({ type: 'success', text: `Programa "${selectedProgramForEdit.titulo_a_otorgar}" actualizado con éxito.` });
      setShowEditProgramModal(false);
      setSelectedProgramForEdit(null);
      const progRes = await fetch(`${apiUrl}/academico/oportunidades`, { headers: getHeaders() });
      const progData = await progRes.json();
      setPrograms(progData.items || []);
      loadConfigPrograms();
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleDeleteProgram(codsede: string, codopest: string, titulo: string) {
    if (!confirm(`¿Estás seguro de que deseas eliminar el programa "${titulo}" de la sede "${getCityFromSede(codsede)}"?`)) return;
    setLoading(true);
    setMessage(null);
    try {
      const res = await fetch(`${apiUrl}/academico/oportunidades/${codsede}/${codopest}`, {
        method: 'DELETE',
        headers: getHeaders(),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al eliminar programa');
      setMessage({ type: 'success', text: `Programa "${titulo}" eliminado correctamente.` });
      const progRes = await fetch(`${apiUrl}/academico/oportunidades`, { headers: getHeaders() });
      const progData = await progRes.json();
      setPrograms(progData.items || []);
      loadConfigPrograms();
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleUpdateUser(e: React.FormEvent) {
    e.preventDefault();
    if (!selectedUserForEdit) return;
    setLoading(true);
    try {
      const payload: any = {
        username: selectedUserForEdit.username,
        role: Number(selectedUserForEdit.role),
        usuario: selectedUserForEdit.usuario,
      };
      if (selectedUserForEdit.cedula !== undefined && selectedUserForEdit.cedula !== '') {
        payload.cedula = Number(selectedUserForEdit.cedula);
      } else {
        payload.cedula = null;
      }
      if (selectedUserForEdit.password && selectedUserForEdit.password.trim()) {
        payload.password = selectedUserForEdit.password.trim();
      }

      const res = await fetch(`${apiUrl}/auth/users/${selectedUserForEdit.id}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al actualizar usuario');

      setMessage({ type: 'success', text: `Usuario "${payload.username}" actualizado exitosamente.` });
      
      const uRes = await fetch(`${apiUrl}/auth/users`, { headers: getHeaders() });
      const uData = await uRes.json();
      setSaceUsers(Array.isArray(uData) ? uData : []);
      
      setShowEditUserModal(false);
      setSelectedUserForEdit(null);
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleCreateUser(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    try {
      const payload: any = {
        username: newUserAccount.username,
        password: newUserAccount.password,
        role: Number(newUserAccount.role),
      };
      if (newUserAccount.cedula) payload.cedula = Number(newUserAccount.cedula);
      if (newUserAccount.usuario) payload.usuario = newUserAccount.usuario;

      const res = await fetch(`${apiUrl}/auth/register`, {
        method: 'POST',
        headers: getHeaders(),
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.message || 'Error al registrar usuario');

      setMessage({ type: 'success', text: `Usuario "${payload.username}" registrado exitosamente.` });
      
      const uRes = await fetch(`${apiUrl}/auth/users`, { headers: getHeaders() });
      const uData = await uRes.json();
      setSaceUsers(Array.isArray(uData) ? uData : []);
      
      setShowCreateUserModal(false);
      setNewUserAccount({ username: '', password: '', role: 5, cedula: '', usuario: '' });
    } catch (err: any) {
      setMessage({ type: 'error', text: err.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleDeleteUser(id: number, username: string) {
    if (!window.confirm(`¿Está seguro de que desea eliminar el acceso al usuario "${username}"?`)) return;
    setLoading(true);
    try {
      const res = await fetch(`${apiUrl}/auth/users/${id}`, {
        method: 'DELETE',
        headers: getHeaders(),
      });
      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.message || 'Error al eliminar usuario');
      }
      setMessage({ type: 'success', text: `Usuario "${username}" eliminado con éxito.` });
      setSaceUsers(saceUsers.filter(u => u.id !== id));
    } catch (e: any) {
      setMessage({ type: 'error', text: e.message });
    } finally {
      setLoading(false);
    }
  }

  async function handleSearchUserByCedula() {
    if (!userSearchCedula) return;
    setSearchingUser(true);
    setSearchedUserResult(null);
    try {
      const res = await fetch(`${apiUrl}/auth/check-cedula/${userSearchCedula}`, { headers: getHeaders() });
      if (!res.ok) throw new Error('Error al verificar la cédula en el servidor.');
      const data = await res.json();
      setSearchedUserResult(data);
    } catch (e: any) {
      setMessage({ type: 'error', text: e.message });
    } finally {
      setSearchingUser(false);
    }
  }

  useEffect(() => {
    if (!token || !profile) return;
    if (activeTab === 'academico') {
      fetchPrograms(programPage, programSearch);
    }
  }, [activeTab, programPage, programSearch, token]);

  return (
    <div style={{
      minHeight: '100vh',
      background: 'linear-gradient(135deg, #0b091a, #161233, #0d0a21)',
      fontFamily: "'Inter', 'Segoe UI', sans-serif",
      color: '#fff',
      display: 'flex',
      flexDirection: 'column',
    }}>
      {/* 1. LOGIN SCREEN */}
      {!profile ? (
        <div style={{
          flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '20px'
        }}>
          <div style={{
            width: '100%', maxWidth: '440px',
            background: 'rgba(255,255,255,0.03)',
            backdropFilter: 'blur(25px)',
            borderRadius: '24px',
            border: '1px solid rgba(255,255,255,0.08)',
            boxShadow: '0 30px 60px rgba(0,0,0,0.6)',
            overflow: 'hidden',
          }}>
            <div style={{
              padding: '40px 40px 30px', textAlign: 'center',
              background: 'linear-gradient(180deg, rgba(99,102,241,0.12), transparent)',
              borderBottom: '1px solid rgba(255,255,255,0.05)',
            }}>
              <img src="/logo.png" alt="Logo CIPPSV" style={{
                width: '140px', height: '140px', margin: '0 auto 20px', display: 'block',
                filter: 'invert(1) brightness(1.8)'
              }} />
              <h1 style={{ fontSize: '28px', fontWeight: 800, margin: 0, letterSpacing: '-0.5px' }}>SACE</h1>
              <p style={{ color: 'rgba(255,255,255,0.5)', fontSize: '13px', marginTop: '6px' }}>
                Sistema de Administración y Control de Estudios
              </p>
            </div>

            <div style={{ padding: '30px 40px 40px' }}>
              {message && (
                <div style={{
                  padding: '12px 16px', borderRadius: '12px', marginBottom: '20px',
                  fontSize: '13.5px', fontWeight: 500,
                  background: message.type === 'success' ? 'rgba(34,197,94,0.1)' : 'rgba(239,68,68,0.1)',
                  border: `1px solid ${message.type === 'success' ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)'}`,
                  color: message.type === 'success' ? '#4ade80' : '#f87171',
                }}>
                  {message.type === 'success' ? '✅' : '❌'} {message.text}
                </div>
              )}

              <form onSubmit={handleLogin}>
                <div style={{ marginBottom: '20px' }}>
                  <label style={labelStyle}>Cédula de Identidad / Usuario</label>
                  <input
                    id="username"
                    type="text"
                    placeholder="Ej: 14999888"
                    value={username}
                    onChange={(e) => setUsername(e.target.value)}
                    required
                    style={inputStyle}
                  />
                </div>

                <div style={{ marginBottom: '24px' }}>
                  <label style={labelStyle}>Contraseña</label>
                  <input
                    id="password"
                    type="password"
                    placeholder="••••••••"
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required
                    style={inputStyle}
                  />
                </div>

                <button
                  type="submit"
                  disabled={loading}
                  style={{
                    width: '100%', padding: '14px', borderRadius: '12px',
                    border: 'none', cursor: 'pointer', fontWeight: 700,
                    fontSize: '16px', color: '#fff',
                    background: 'linear-gradient(135deg, #6366f1, #8b5cf6)',
                    boxShadow: '0 4px 20px rgba(99,102,241,0.3)',
                    transition: 'transform 0.2s, opacity 0.2s',
                    opacity: loading ? 0.7 : 1,
                  }}
                >
                  {loading ? '⏳ Iniciando sesión...' : 'Ingresar al SACE'}
                </button>
              </form>

              <div style={{ marginTop: '24px', textAlign: 'center', fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                Usa tus credenciales asignadas de Administrador, Profesor o Estudiante.
              </div>
            </div>
          </div>
        </div>
      ) : (
        // 2. DASHBOARD VIEW (AUTHENTICATED)
        <div style={{ display: 'flex', flex: 1, minHeight: '100vh' }}>
          
          {/* Left Sidebar */}
          <div 
            className="hide-scrollbar"
            style={{
              width: '260px',
              background: 'rgba(15,12,38,0.5)',
              borderRight: '1px solid rgba(255,255,255,0.06)',
              padding: '24px',
              display: 'flex',
              flexDirection: 'column',
              justifyContent: 'space-between',
              position: 'sticky',
              top: 0,
              height: '100vh',
              overflowY: 'auto'
            }}
          >
            <div>
              {/* Brand logo */}
              <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '32px' }}>
                <img src="/logo.png" alt="Logo CIPPSV" style={{
                  width: '32px', height: '32px',
                  filter: 'invert(1) brightness(1.8)'
                }} />
                <div>
                  <h3 style={{ margin: 0, fontWeight: 800, fontSize: '20px', letterSpacing: '-0.5px' }}>SACE</h3>
                  <span style={{ fontSize: '10px', color: 'rgba(255,255,255,0.4)' }}>Control de Estudios</span>
                </div>
              </div>

              {/* Navigation Menu */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                <button
                  onClick={() => { setActiveTab('dashboard'); setMessage(null); }}
                  style={navItemStyle(activeTab === 'dashboard')}
                >
                  📊 Resumen General
                </button>
                <button
                  onClick={() => { setActiveTab('expedientes'); setMessage(null); }}
                  style={navItemStyle(activeTab === 'expedientes')}
                >
                  👤 {profile.role === 5 ? 'Mis Datos Personales' : 'Estudiantes'}
                </button>
                <button
                  onClick={() => { setActiveTab('academico'); setMessage(null); }}
                  style={navItemStyle(activeTab === 'academico')}
                >
                  📚 Oferta y Pensum
                </button>
                {profile.role <= 4 && (
                  <button
                    onClick={() => { setActiveTab('cohortes'); setMessage(null); }}
                    style={navItemStyle(activeTab === 'cohortes')}
                  >
                    📅 Cohortes Académicas
                  </button>
                )}
                {profile.role <= 4 && (
                  <button
                    onClick={() => { setActiveTab('profesores'); setMessage(null); }}
                    style={navItemStyle(activeTab === 'profesores')}
                  >
                    👨‍🏫 {profile.role === 4 ? 'Mi Ficha de Profesor' : 'Profesores'}
                  </button>
                )}
                {profile.role <= 2 && (
                  <button
                    onClick={() => { setActiveTab('usuarios'); setMessage(null); }}
                    style={navItemStyle(activeTab === 'usuarios')}
                  >
                    👥 Gestión de Usuarios
                  </button>
                )}
                <button
                  onClick={() => { setActiveTab('evaluaciones'); setMessage(null); }}
                  style={navItemStyle(activeTab === 'evaluaciones')}
                >
                  📝 Evaluaciones y Notas
                </button>
                {profile.role <= 3 && (
                  <button
                    onClick={() => { setActiveTab('estadisticas'); setMessage(null); }}
                    style={navItemStyle(activeTab === 'estadisticas')}
                  >
                    📈 Estadísticas SACE
                  </button>
                )}
                {profile.role <= 2 && (
                  <button
                    onClick={() => { setActiveTab('configuracion_academica'); setMessage(null); }}
                    style={navItemStyle(activeTab === 'configuracion_academica')}
                  >
                    ⚙️ Configuración Académica
                  </button>
                )}
              </div>
            </div>

            {/* User Session card */}
            <div style={{
              background: 'rgba(255,255,255,0.03)',
              borderRadius: '16px',
              padding: '16px',
              border: '1px solid rgba(255,255,255,0.05)'
            }}>
              <div style={{ display: 'flex', alignItems: 'center', gap: '10px', marginBottom: '12px' }}>
                <div style={{
                  width: '36px', height: '36px', borderRadius: '50%',
                  background: 'linear-gradient(135deg, #6366f1, #8b5cf6)',
                  display: 'flex', alignItems: 'center', justifyContent: 'center',
                  fontSize: '16px'
                }}>👤</div>
                <div>
                  <div style={{ fontSize: '13px', fontWeight: 600, color: '#fff' }}>C.I. {profile.username}</div>
                  <div style={{ fontSize: '11px', color: '#a78bfa' }}>{ROLES[profile.role]} (Rol: {profile.role}, Tipo: {typeof profile.role})</div>
                </div>
              </div>
              <button
                onClick={handleLogout}
                style={{
                  width: '100%', padding: '8px', borderRadius: '8px',
                  border: 'none', cursor: 'pointer', background: 'rgba(239,68,68,0.15)',
                  color: '#f87171', fontSize: '12px', fontWeight: 600,
                  transition: 'background 0.2s',
                }}
                onMouseOver={(e) => e.currentTarget.style.background = 'rgba(239,68,68,0.25)'}
                onMouseOut={(e) => e.currentTarget.style.background = 'rgba(239,68,68,0.15)'}
              >
                Cerrar Sesión
              </button>
            </div>
          </div>

          {/* Main Content Area */}
          <div style={{ flex: 1, padding: '40px', overflowY: 'auto', display: 'flex', flexDirection: 'column', gap: '30px' }}>
            
            {/* Header / Message banner */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
              <div>
                <h1 style={{ fontSize: '28px', fontWeight: 800, margin: 0, textTransform: 'capitalize' }}>
                  {activeTab === 'dashboard' ? 'Panel de Control' : activeTab}
                </h1>
                <p style={{ color: 'rgba(255,255,255,0.4)', fontSize: '14px', margin: '4px 0 0' }}>
                  SACE v2.0 • Autenticación segura y control de registros
                </p>
              </div>

              {/* Status Indicator */}
              <div style={{
                background: 'rgba(34,197,94,0.1)', border: '1px solid rgba(34,197,94,0.3)',
                padding: '8px 16px', borderRadius: '20px', color: '#4ade80', fontSize: '12px', fontWeight: 600
              }}>
                ● Sistema Conectado
              </div>
            </div>

            {message && (
              <div style={{
                padding: '16px', borderRadius: '12px',
                fontSize: '14px', fontWeight: 500,
                background: message.type === 'success' ? 'rgba(34,197,94,0.1)' : 'rgba(239,68,68,0.1)',
                border: `1px solid ${message.type === 'success' ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)'}`,
                color: message.type === 'success' ? '#4ade80' : '#f87171',
                display: 'flex', justifyContent: 'space-between', alignItems: 'center'
              }}>
                <span>{message.type === 'success' ? '✅' : '❌'} {message.text}</span>
                <button onClick={() => setMessage(null)} style={{ background: 'none', border: 'none', color: 'inherit', cursor: 'pointer', fontWeight: 700 }}>✕</button>
              </div>
            )}

            {/* TAB VIEW RENDERING */}
            
            {/* TAB 1: DASHBOARD STATS */}
            {activeTab === 'dashboard' && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                {profile.role === 5 ? (
                  // Student Personalized Dashboard
                  <>
                    {/* Stats cards for student */}
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: '20px' }}>
                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>📚</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Materias Aprobadas</div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>
                            {studentProfileData?.notas?.filter((n: any) => n.calificacion !== null && n.calificacion >= 10 && n.calificacion !== 404).length || 0}
                          </div>
                        </div>
                      </div>

                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>⚡</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Créditos Aprobados</div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px', color: '#60a5fa' }}>
                            {studentProfileData?.notas?.filter((n: any) => n.calificacion !== null && n.calificacion >= 10 && n.calificacion !== 404 && n.creditos)
                              .reduce((sum: number, n: any) => sum + Number(n.creditos), 0) || 0} U.C.
                          </div>
                        </div>
                      </div>

                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>📈</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Promedio General</div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px', color: '#4ade80' }}>
                            {(() => {
                              const validGrades = studentProfileData?.notas?.filter((n: any) => n.calificacion !== null && n.calificacion !== 404) || [];
                              return validGrades.length > 0 
                                ? (validGrades.reduce((sum: number, n: any) => sum + n.calificacion, 0) / validGrades.length).toFixed(2)
                                : 'S/N';
                            })()}
                          </div>
                        </div>
                      </div>

                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>🎓</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Estatus Académico</div>
                          <div style={{ fontSize: '20px', fontWeight: 800, marginTop: '10px', color: '#34d399' }}>
                            {studentProfileData?.especializaciones?.[0]?.status || 'Activo / Regular'}
                          </div>
                        </div>
                      </div>
                    </div>

                    {/* Student Welcome Card */}
                    <div style={{
                      background: 'linear-gradient(135deg, rgba(99,102,241,0.1), rgba(139,92,246,0.1))',
                      borderRadius: '20px',
                      border: '1px solid rgba(255,255,255,0.06)',
                      padding: '30px',
                    }}>
                      <h3 style={{ fontSize: '20px', fontWeight: 700, margin: '0 0 10px' }}>
                        ¡Bienvenido, {profile.nombre_display || (studentProfileData ? `${studentProfileData.nombres} ${studentProfileData.apellidos}` : profile.username)}!
                      </h3>
                      <p style={{ color: 'rgba(255,255,255,0.6)', lineHeight: 1.6, fontSize: '14.5px', margin: '0 0 20px' }}>
                        Has ingresado exitosamente al SACE. A continuación se presentan tus datos generales del expediente. Puedes navegar a la pestaña **"Evaluaciones y Notas"** para ver tu récord oficial completo por asignaturas.
                      </p>

                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '20px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px' }}>
                        <div>
                          <span style={detailLabelStyle}>Cédula de Identidad</span>
                          <div style={detailValueStyle}>{profile.username}</div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Correo Electrónico</span>
                          <div style={detailValueStyle}>{studentProfileData?.email || 'No registrado'}</div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Teléfono Celular</span>
                          <div style={detailValueStyle}>{studentProfileData?.telefono_celular || 'No registrado'}</div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Programa de Estudio</span>
                          <div style={{ ...detailValueStyle, color: '#a78bfa' }}>
                            {studentProfileData?.especializaciones?.[0]?.programa || 'Cargando programa...'}
                          </div>
                        </div>
                      </div>
                    </div>
                  </>
                ) : (
                  // Admin / Professor Dashboard
                  <>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: '20px' }}>
                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>📂</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>
                            {profile.role === 4 ? 'Alumnos Inscritos' : 'Estudiantes Registrados'}
                          </div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>
                            {profile.role === 4 ? new Set(notas.map(n => n.cedula)).size : stats.expedientes}
                          </div>
                        </div>
                      </div>

                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>📅</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>
                            {profile.role === 4 ? 'Cohortes Asignadas' : 'Cohortes Activas'}
                          </div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>
                            {profile.role === 4 ? new Set(actas.map(a => a.codcohorte)).size : stats.cohortes}
                          </div>
                        </div>
                      </div>

                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>👨‍🏫</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>
                            {profile.role === 4 ? 'Materias a Cargo' : 'Profesores Activos'}
                          </div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>
                            {profile.role === 4 ? new Set(actas.map(a => a.codasig)).size : stats.profesores}
                          </div>
                        </div>
                      </div>

                      <div style={statCardStyle}>
                        <span style={{ fontSize: '28px' }}>📝</span>
                        <div>
                          <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>
                            {profile.role === 4 ? 'Actas Registradas' : 'Actas de Evaluación'}
                          </div>
                          <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>
                            {profile.role === 4 ? actas.length : stats.actas}
                          </div>
                        </div>
                      </div>
                    </div>

                    <div style={{
                      background: 'linear-gradient(135deg, rgba(99,102,241,0.1), rgba(139,92,246,0.1))',
                      borderRadius: '20px',
                      border: '1px solid rgba(255,255,255,0.06)',
                      padding: '30px',
                    }}>
                      <h3 style={{ fontSize: '20px', fontWeight: 700, margin: '0 0 10px' }}>
                        ¡Bienvenido al panel, {profile.nombre_display || profile.username}!
                      </h3>
                      <p style={{ color: 'rgba(255,255,255,0.6)', lineHeight: 1.6, fontSize: '14.5px', margin: 0 }}>
                        {profile.role === 4
                          ? `Como Profesor del CIPPSV, aquí se muestra el resumen de tus alumnos a cargo, materias dictadas y actas bajo tu responsabilidad. Puedes calificar las asignaturas ingresando al módulo "Evaluaciones y Notas".`
                          : `Desde este panel puedes consultar expedientes académicos, revisar cohortes y especialidades vigentes, y calificar actas de evaluación escolar según tus privilegios de ${ROLES[profile.role]}.`}
                      </p>
                    </div>
                  </>
                )}
              </div>
            )}

            {/* TAB 2: EXPEDIENTES (DATOS PERSONALES) */}
            {activeTab === 'expedientes' && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                
                {profile.role <= 3 ? (
                  // Admin search and list
                  <div style={panelCardStyle}>
                    <h3 style={{ margin: '0 0 16px', fontSize: '18px', fontWeight: 700 }}>Buscar Expediente de Estudiante</h3>
                    <form onSubmit={handleSearchStudent} style={{ display: 'flex', gap: '12px' }}>
                      <input
                        type="text"
                        placeholder="Ingresa cédula (Ej: 24123456)"
                        value={searchCedula}
                        onChange={(e) => setSearchCedula(e.target.value)}
                        style={{ ...inputStyle, flex: 1 }}
                      />
                      <button type="submit" style={btnStyleSecondary}>Buscar</button>
                      {profile.role <= 2 && (
                        <button type="button" onClick={() => setShowCreateStudent(true)} style={btnStylePrimary}>
                          + Nuevo Expediente
                        </button>
                      )}
                    </form>
                  </div>
                ) : (
                  <div style={panelCardStyle}>
                    <h3 style={{ margin: '0 0 10px', fontSize: '18px', fontWeight: 700 }}>Tu Expediente Personal</h3>
                    <p style={{ color: 'rgba(255,255,255,0.5)', fontSize: '13px' }}>
                      Los estudiantes solo están autorizados a ver sus propios datos personales.
                    </p>
                  </div>
                )}

                {/* Display Single Student Record */}
                {singleStudent && (
                  <div style={panelCardStyle}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.06)', paddingBottom: '16px', marginBottom: '20px', flexWrap: 'wrap', gap: '12px' }}>
                      <h4 style={{ margin: 0, fontSize: '18px', color: '#a78bfa' }}>
                        Expediente de {singleStudent.nombres} {singleStudent.apellidos}
                      </h4>
                      <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                        <button
                          onClick={() => {
                            setShowFullDataModal(true);
                            setIsEditingProfile(false);
                            setEditableStudent({ ...singleStudent });
                          }}
                          style={{
                            background: 'rgba(59,130,246,0.15)', border: '1px solid rgba(59,130,246,0.3)',
                            borderRadius: '8px', color: '#60a5fa', fontSize: '12px', padding: '6px 12px',
                            cursor: 'pointer', fontWeight: 600, transition: 'all 0.2s'
                          }}
                        >
                          🔍 Ver Ficha Completa
                        </button>
                        <span style={{ fontSize: '14px', background: 'rgba(255,255,255,0.05)', padding: '4px 12px', borderRadius: '12px' }}>
                          C.I. {singleStudent.cedula}
                        </span>
                      </div>
                    </div>

                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))', gap: '20px' }}>
                      <div>
                        <span style={detailLabelStyle}>Nombres</span>
                        <div style={detailValueStyle}>{singleStudent.nombres || 'No registrado'}</div>
                      </div>
                      <div>
                        <span style={detailLabelStyle}>Apellidos</span>
                        <div style={detailValueStyle}>{singleStudent.apellidos || 'No registrado'}</div>
                      </div>
                      <div>
                        <span style={detailLabelStyle}>Correo Electrónico</span>
                        <div style={detailValueStyle}>{singleStudent.email || 'No registrado'}</div>
                      </div>
                      <div>
                        <span style={detailLabelStyle}>Teléfono Celular</span>
                        <div style={detailValueStyle}>{singleStudent.telefono_celular || 'No registrado'}</div>
                      </div>
                      <div style={{ gridColumn: 'span 2' }}>
                        <span style={detailLabelStyle}>Dirección de Habitación</span>
                        <div style={detailValueStyle}>{singleStudent.direccion || 'No registrado'}</div>
                      </div>
                    </div>
                    
                    {/* Especializaciones y sus Notas agrupadas por Período */}
                    {singleStudent.especializaciones && singleStudent.especializaciones.length > 0 ? (
                      <div style={{ display: 'flex', flexDirection: 'column', gap: '24px', marginTop: '20px' }}>
                        <h4 style={{ margin: '0 0 -8px', fontSize: '16px', color: '#a78bfa', fontWeight: 700 }}>
                          📚 Récord de Notas por Programa Académico
                        </h4>
                        {singleStudent.especializaciones.map((esp: any, i: number) => {
                          const normalize = (code: string) => (code || '').replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                          const espNotes = (singleStudent.notas || []).filter((n: any) => 
                            normalize(n.codcohorte) === normalize(esp.codcohorte)
                          );

                          // Agrupar por período
                          const periodsMap: { [key: string]: any[] } = {};
                          espNotes.forEach((n: any) => {
                            const pKey = n.periodo !== null ? `Período ${n.periodo}` : 'Sin Período Definido';
                            if (!periodsMap[pKey]) periodsMap[pKey] = [];
                            periodsMap[pKey].push(n);
                          });

                          // Ordenar períodos
                          const sortedPeriods = Object.keys(periodsMap).sort((a, b) => {
                            if (a.includes('Definido')) return 1;
                            if (b.includes('Definido')) return -1;
                            const numA = parseInt(a.replace(/[^0-9]/g, ''), 10);
                            const numB = parseInt(b.replace(/[^0-9]/g, ''), 10);
                            return numA - numB;
                          });

                          return (
                            <div key={i} style={{ ...panelCardStyle, borderLeft: '4px solid #6366f1', marginTop: '12px' }}>
                              {/* Cabecera del Programa */}
                              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: '16px', borderBottom: '1px solid rgba(255,255,255,0.06)', paddingBottom: '16px', marginBottom: '20px' }}>
                                <div>
                                  <span style={{ fontSize: '11px', color: '#a78bfa', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '1px' }}>
                                    {esp.tipo}
                                  </span>
                                  <h5 style={{ margin: '4px 0 0', fontSize: '17px', fontWeight: 700, color: '#fff' }}>
                                    {esp.programa}
                                  </h5>
                                  <p style={{ margin: '4px 0 0', fontSize: '13px', color: 'rgba(255,255,255,0.5)' }}>
                                    Mención: {esp.mencion}
                                  </p>
                                </div>
                                <div style={{ textAlign: 'right', display: 'flex', flexDirection: 'column', alignItems: 'flex-end', gap: '6px' }}>
                                  <span style={{
                                    fontSize: '11px', fontWeight: 700, padding: '4px 10px', borderRadius: '8px',
                                    background: esp.status === 'Activo' || esp.status === 'Egresado' ? 'rgba(74,222,128,0.15)' : 'rgba(248,113,113,0.15)',
                                    color: esp.status === 'Activo' || esp.status === 'Egresado' ? '#4ade80' : '#f87171',
                                    display: 'inline-block',
                                  }}>
                                    {esp.status}
                                  </span>
                                  <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                                    Cohorte: {esp.codcohorte}
                                  </div>
                                  <button
                                    onClick={() => handleDownloadRecordPdf(singleStudent.cedula, esp.codcohorte, esp.programa)}
                                    style={{
                                      background: 'rgba(139,92,246,0.15)', border: '1px solid rgba(139,92,246,0.3)',
                                      borderRadius: '8px', color: '#c084fc', fontSize: '11px', padding: '5px 10px',
                                      cursor: 'pointer', fontWeight: 600, transition: 'all 0.2s', marginTop: '4px'
                                    }}
                                  >
                                    📄 Imprimir PDF
                                  </button>
                                </div>
                              </div>

                              {/* Periodos y Notas */}
                              {espNotes.length === 0 ? (
                                <p style={{ color: 'rgba(255,255,255,0.4)', fontSize: '13px', margin: '10px 0' }}>
                                  No hay calificaciones registradas para este programa.
                                </p>
                              ) : (
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                                  {sortedPeriods.map((period) => (
                                    <div key={period} style={{ background: 'rgba(255,255,255,0.01)', border: '1px solid rgba(255,255,255,0.03)', borderRadius: '12px', padding: '16px' }}>
                                      <h6 style={{ margin: '0 0 12px', fontSize: '13.5px', color: '#a78bfa', fontWeight: 700 }}>
                                        📅 {period}
                                      </h6>
                                      <div style={{ overflowX: 'auto' }}>
                                        <table style={tableStyle}>
                                          <thead>
                                            <tr>
                                              <th style={thStyle}>Código</th>
                                              <th style={thStyle}>Asignatura</th>
                                              <th style={thStyle}>Acta</th>
                                              <th style={{ ...thStyle, width: '120px', textAlign: 'center' }}>F. Aprobación</th>
                                              <th style={{ ...thStyle, width: '80px', textAlign: 'center' }}>Créditos</th>
                                              <th style={thStyle}>Calificación</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            {periodsMap[period].map((n: any, idx: number) => (
                                              <tr key={idx} style={trStyle}>
                                                <td style={{ ...tdStyle, width: '100px' }}>{n.codasig}</td>
                                                <td style={tdStyle}>{n.asignatura}</td>
                                                <td style={{ ...tdStyle, width: '150px' }}>{n.codacta}</td>
                                                <td style={{ ...tdStyle, width: '120px', textAlign: 'center', color: 'rgba(255,255,255,0.5)' }}>
                                                  {n.fecha_aprobacion ? new Date(n.fecha_aprobacion).toLocaleDateString('es-VE') : '-'}
                                                </td>
                                                <td style={{ ...tdStyle, width: '80px', textAlign: 'center', color: '#60a5fa' }}>{n.creditos || '-'}</td>
                                                <td style={{
                                                  ...tdStyle,
                                                  width: '120px',
                                                  fontWeight: 700,
                                                  color: getCalificacionColor(n.calificacion)
                                                }}>
                                                  {formatCalificacion(n.calificacion)}
                                                </td>
                                              </tr>
                                            ))}
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>
                                  ))}
                                </div>
                              )}
                            </div>
                          );
                        })}
                      </div>
                    ) : (
                      singleStudent.notas && singleStudent.notas.length > 0 && (
                        <div style={{ ...panelCardStyle, marginTop: '20px' }}>
                          <h4 style={{ margin: '0 0 16px', fontSize: '16px', color: '#a78bfa', fontWeight: 700 }}>
                            📝 Calificaciones Registradas (Sin Programa Asociado)
                          </h4>
                          <div style={{ overflowX: 'auto' }}>
                            <table style={tableStyle}>
                              <thead>
                                <tr>
                                  <th style={thStyle}>Período</th>
                                  <th style={thStyle}>Código</th>
                                  <th style={thStyle}>Asignatura</th>
                                  <th style={thStyle}>Acta</th>
                                  <th style={{ ...thStyle, width: '120px', textAlign: 'center' }}>F. Aprobación</th>
                                  <th style={{ ...thStyle, width: '80px', textAlign: 'center' }}>Créditos</th>
                                  <th style={thStyle}>Calificación</th>
                                </tr>
                              </thead>
                              <tbody>
                                {singleStudent.notas.map((n: any, i: number) => (
                                  <tr key={i} style={trStyle}>
                                    <td style={tdStyle}>{n.periodo !== null ? `Período ${n.periodo}` : 'S/P'}</td>
                                    <td style={tdStyle}>{n.codasig}</td>
                                    <td style={tdStyle}>{n.asignatura}</td>
                                    <td style={tdStyle}>{n.codacta}</td>
                                    <td style={{ ...tdStyle, width: '120px', textAlign: 'center', color: 'rgba(255,255,255,0.5)' }}>
                                      {n.fecha_aprobacion ? new Date(n.fecha_aprobacion).toLocaleDateString('es-VE') : '-'}
                                    </td>
                                    <td style={{ ...tdStyle, width: '80px', textAlign: 'center', color: '#60a5fa' }}>{n.creditos || '-'}</td>
                                    <td style={{
                                      ...tdStyle,
                                      fontWeight: 700,
                                      color: getCalificacionColor(n.calificacion)
                                    }}>
                                      {formatCalificacion(n.calificacion)}
                                    </td>
                                  </tr>
                                ))}
                              </tbody>
                            </table>
                          </div>
                        </div>
                      )
                    )
                  }

                    {/* Calificaciones Huérfanas (que no cruzan con especializaciones activas) */}
                    {(() => {
                      const normalize = (code: string) => (code || '').replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
                      const matchedNoteIds = new Set<string>();
                      (singleStudent.especializaciones || []).forEach((esp: any) => {
                        (singleStudent.notas || []).forEach((n: any) => {
                          if (normalize(n.codcohorte) === normalize(esp.codcohorte)) {
                            matchedNoteIds.add(`${n.codacta}-${n.codasig}`);
                          }
                        });
                      });
                      const unmatchedNotes = (singleStudent.notas || []).filter(
                        (n: any) => !matchedNoteIds.has(`${n.codacta}-${n.codasig}`)
                      );

                      if (singleStudent.especializaciones && singleStudent.especializaciones.length > 0 && unmatchedNotes.length > 0) {
                        return (
                          <div style={{ ...panelCardStyle, marginTop: '20px', borderLeft: '4px solid #f59e0b' }}>
                            <h4 style={{ margin: '0 0 16px', fontSize: '16px', color: '#f59e0b', fontWeight: 700 }}>
                              ⚠️ Otras Calificaciones Registradas (Sin Programa Activo Coincidente)
                            </h4>
                            <div style={{ overflowX: 'auto' }}>
                              <table style={tableStyle}>
                                <thead>
                                  <tr>
                                    <th style={thStyle}>Cohorte Acta</th>
                                    <th style={thStyle}>Código</th>
                                    <th style={thStyle}>Asignatura</th>
                                    <th style={thStyle}>Acta</th>
                                    <th style={{ ...thStyle, width: '120px', textAlign: 'center' }}>F. Aprobación</th>
                                    <th style={{ ...thStyle, width: '80px', textAlign: 'center' }}>Créditos</th>
                                    <th style={thStyle}>Calificación</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {unmatchedNotes.map((n: any, idx: number) => (
                                    <tr key={idx} style={trStyle}>
                                      <td style={tdStyle}>{n.codcohorte}</td>
                                      <td style={tdStyle}>{n.codasig}</td>
                                      <td style={tdStyle}>{n.asignatura}</td>
                                      <td style={tdStyle}>{n.codacta}</td>
                                      <td style={{ ...tdStyle, width: '120px', textAlign: 'center', color: 'rgba(255,255,255,0.5)' }}>
                                        {n.fecha_aprobacion ? new Date(n.fecha_aprobacion).toLocaleDateString('es-VE') : '-'}
                                      </td>
                                      <td style={{ ...tdStyle, width: '80px', textAlign: 'center', color: '#60a5fa' }}>{n.creditos || '-'}</td>
                                      <td style={{
                                        ...tdStyle,
                                        fontWeight: 700,
                                        color: getCalificacionColor(n.calificacion)
                                      }}>
                                        {formatCalificacion(n.calificacion)}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </div>
                          </div>
                        );
                      }
                      return null;
                    })()}

                  </div>
                )}

                {/* Create Student Modal */}
                {showCreateStudent && (
                  <div style={modalBackdropStyle}>
                    <div style={modalContentStyle}>
                      <h3 style={{ margin: '0 0 20px', fontSize: '18px', fontWeight: 700 }}>Crear Expediente de Estudiante</h3>
                      <form onSubmit={handleCreateStudent} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Cédula</label>
                            <input
                              type="number"
                              required
                              value={newStudent.cedula}
                              onChange={(e) => setNewStudent({ ...newStudent, cedula: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Correo Electrónico</label>
                            <input
                              type="email"
                              value={newStudent.email}
                              onChange={(e) => setNewStudent({ ...newStudent, email: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Nombres</label>
                            <input
                              type="text"
                              required
                              value={newStudent.nombres}
                              onChange={(e) => setNewStudent({ ...newStudent, nombres: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Apellidos</label>
                            <input
                              type="text"
                              required
                              value={newStudent.apellidos}
                              onChange={(e) => setNewStudent({ ...newStudent, apellidos: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div>
                          <label style={labelStyle}>Teléfono Celular</label>
                          <input
                            type="text"
                            value={newStudent.telefono_celular}
                            onChange={(e) => setNewStudent({ ...newStudent, telefono_celular: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Dirección</label>
                          <textarea
                            value={newStudent.direccion}
                            onChange={(e) => setNewStudent({ ...newStudent, direccion: e.target.value })}
                            style={{ ...inputStyle, height: '80px', resize: 'none' }}
                          />
                        </div>

                        <div style={{ display: 'flex', justifySelf: 'flex-end', gap: '12px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowCreateStudent(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>Crear</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* TAB 3: ACADEMICO (PROGRAMAS & PENSUM) */}
            {activeTab === 'academico' && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700 }}>Programas de Estudio (Postgrados y Diplomados)</h3>
                </div>

                {/* Buscador de Programas */}
                <form
                  onSubmit={(e) => {
                    e.preventDefault();
                    setProgramSearch(searchVal);
                    setProgramPage(1);
                  }}
                  style={{ display: 'flex', gap: '12px', marginBottom: '8px', maxWidth: '450px' }}
                >
                  <input
                    type="text"
                    placeholder="Buscar por código, título o mención..."
                    value={searchVal}
                    onChange={(e) => setSearchVal(e.target.value)}
                    style={{ ...inputStyle, flex: 1, padding: '10px 16px' }}
                  />
                  <button type="submit" style={{ ...btnStylePrimary, padding: '10px 20px', borderRadius: '12px' }}>
                    🔍 Buscar
                  </button>
                  {programSearch && (
                    <button
                      type="button"
                      onClick={() => {
                        setSearchVal('');
                        setProgramSearch('');
                        setProgramPage(1);
                      }}
                      style={{ ...btnStyleSecondary, padding: '10px 16px', borderRadius: '12px' }}
                    >
                      Limpiar
                    </button>
                  )}
                </form>

                {programs.length === 0 ? (
                  <div style={{ padding: '40px', textAlign: 'center', color: 'rgba(255,255,255,0.4)' }}>
                    No se encontraron programas de postgrado que coincidan con la búsqueda.
                  </div>
                ) : (
                  <>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: '20px' }}>
                      {programs.map((p) => (
                        <div
                          key={`${p.codsede}-${p.codopest}`}
                          onClick={() => {
                            setSelectedProgram(p);
                            setSelectedProgramPensum([]);
                            setShowProgramDetailModal(true);
                            loadProgramPensum(p.codopest);
                          }}
                          style={{
                            background: 'rgba(255,255,255,0.02)',
                            border: '1px solid rgba(255,255,255,0.05)',
                            borderRadius: '20px',
                            padding: '24px',
                            cursor: 'pointer',
                            transition: 'all 0.2s',
                            display: 'flex',
                            flexDirection: 'column',
                            justifyContent: 'space-between',
                            gap: '16px'
                          }}
                          onMouseEnter={(e) => {
                            e.currentTarget.style.transform = 'translateY(-2px)';
                            e.currentTarget.style.borderColor = 'rgba(99,102,241,0.3)';
                            e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                          }}
                          onMouseLeave={(e) => {
                            e.currentTarget.style.transform = 'translateY(0)';
                            e.currentTarget.style.borderColor = 'rgba(255,255,255,0.05)';
                            e.currentTarget.style.background = 'rgba(255,255,255,0.02)';
                          }}
                        >
                          <div style={{ display: 'flex', gap: '16px', alignItems: 'flex-start' }}>
                            <div style={{
                              background: 'rgba(99,102,241,0.1)',
                              color: '#a78bfa',
                              width: '44px',
                              height: '44px',
                              borderRadius: '12px',
                              display: 'flex',
                              alignItems: 'center',
                              justifyContent: 'center',
                              fontSize: '20px',
                              flexShrink: 0
                            }}>
                              🎓
                            </div>
                            <div>
                              <div style={{ fontWeight: 700, fontSize: '15.5px', color: '#fff', lineHeight: 1.3 }}>{p.titulo_a_otorgar}</div>
                              <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.5)', marginTop: '6px' }}>
                                Mención/Especialidad: {p.mencion_especialidad || 'No registrada'}
                              </div>
                            </div>
                          </div>

                          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderTop: '1px solid rgba(255,255,255,0.05)', paddingTop: '12px', marginTop: '4px' }}>
                            <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase', fontWeight: 600 }}>
                              Código: {p.codopest}
                            </span>
                            <span style={{
                              background: 'rgba(99,102,241,0.15)',
                              color: '#a78bfa',
                              borderRadius: '8px',
                              fontSize: '11px',
                              padding: '4px 8px',
                              fontWeight: 700
                            }}>
                              {p.tipo}
                            </span>
                          </div>
                        </div>
                      ))}
                    </div>

                    {/* Paginación */}
                    {totalPrograms > 10 && (
                      <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', gap: '16px', marginTop: '30px' }}>
                        <button
                          disabled={programPage === 1}
                          onClick={() => setProgramPage(p => Math.max(1, p - 1))}
                          style={{
                            ...btnStyleSecondary,
                            padding: '8px 16px',
                            borderRadius: '10px',
                            opacity: programPage === 1 ? 0.4 : 1,
                            cursor: programPage === 1 ? 'not-allowed' : 'pointer'
                          }}
                        >
                          ◀ Anterior
                        </button>
                        <span style={{ fontSize: '14px', color: 'rgba(255,255,255,0.6)', fontWeight: 600 }}>
                          Página {programPage} de {Math.ceil(totalPrograms / 10)}
                        </span>
                        <button
                          disabled={programPage >= Math.ceil(totalPrograms / 10)}
                          onClick={() => setProgramPage(p => Math.min(Math.ceil(totalPrograms / 10), p + 1))}
                          style={{
                            ...btnStyleSecondary,
                            padding: '8px 16px',
                            borderRadius: '10px',
                            opacity: programPage >= Math.ceil(totalPrograms / 10) ? 0.4 : 1,
                            cursor: programPage >= Math.ceil(totalPrograms / 10) ? 'not-allowed' : 'pointer'
                          }}
                        >
                          Siguiente ▶
                        </button>
                      </div>
                    )}
                  </>
                )}
              </div>
            )}

            {/* TAB 4: COHORTES */}
            {activeTab === 'cohortes' && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700 }}>Cohortes Académicas Activas</h3>
                  {profile.role <= 2 && selectedProgramFilter && (
                    <button
                      onClick={() => {
                        setNewCohorte({
                          codsede: selectedProgramFilter.codsede,
                          codopest: selectedProgramFilter.codopest,
                          codcohorte: '',
                          periodo_lectivo: '2026-I',
                          fecha_inicio: ''
                        });
                        setModalSelectedCity(selectedCity);
                        setModalPrograms(programsListByCity);
                        setShowCreateCohorte(true);
                      }}
                      style={btnStylePrimary}
                    >
                      + Crear Nueva Cohorte
                    </button>
                  )}
                </div>

                {/* Filtros en cascada */}
                <div style={{ ...panelCardStyle, display: 'flex', gap: '20px', flexWrap: 'wrap', padding: '20px' }}>
                  <div style={{ flex: 1, minWidth: '220px' }}>
                    <label style={labelStyle}>Seleccione Ciudad</label>
                    <select
                      value={selectedCity}
                      onChange={(e) => setSelectedCity(e.target.value)}
                      style={{ ...inputStyle, padding: '10px 16px', color: '#fff', background: '#120f30' }}
                    >
                      <option value="">Seleccione una ciudad</option>
                      {citiesList.map(city => (
                        <option key={city} value={city}>{city}</option>
                      ))}
                    </select>
                  </div>

                  <div style={{ flex: 1, minWidth: '250px' }}>
                    <label style={labelStyle}>Seleccione Programa de Estudio</label>
                    <select
                      disabled={!selectedCity}
                      value={selectedProgramFilter ? JSON.stringify(selectedProgramFilter) : ''}
                      onChange={(e) => {
                        const val = e.target.value;
                        setSelectedProgramFilter(val ? JSON.parse(val) : null);
                      }}
                      style={{
                        ...inputStyle,
                        padding: '10px 16px',
                        color: '#fff',
                        background: '#120f30',
                        opacity: selectedCity ? 1 : 0.5,
                        cursor: selectedCity ? 'default' : 'not-allowed'
                      }}
                    >
                      <option value="">Seleccione un programa</option>
                      {programsListByCity.map(p => (
                        <option key={`${p.codsede}-${p.codopest}`} value={JSON.stringify(p)}>
                          {p.titulo_a_otorgar} ({p.codopest})
                        </option>
                      ))}
                    </select>
                  </div>
                </div>

                {!selectedCity || !selectedProgramFilter ? (
                  <div style={{ ...panelCardStyle, padding: '40px', textAlign: 'center', color: 'rgba(255,255,255,0.4)', fontSize: '14.5px' }}>
                    Por favor, elija una ciudad y el programa de estudio para consultar sus cohortes asociadas.
                  </div>
                ) : cohortes.length === 0 ? (
                  <div style={{ ...panelCardStyle, padding: '40px', textAlign: 'center', color: 'rgba(255,255,255,0.4)', fontSize: '14.5px' }}>
                    No se encontraron cohortes registradas para este programa en la ciudad seleccionada.
                  </div>
                ) : (
                  (() => {
                    const filtered = cohortes
                      .filter((c) => {
                        const q = cohorteSearch.toLowerCase();
                        return (
                          c.codcohorte.toLowerCase().includes(q) ||
                          c.periodo_lectivo.toLowerCase().includes(q) ||
                          c.codopest.toLowerCase().includes(q)
                        );
                      })
                      .sort((a, b) => a.codcohorte.localeCompare(b.codcohorte));

                    const ITEMS_PER_PAGE = 6;
                    const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
                    const paginated = filtered.slice((cohortePage - 1) * ITEMS_PER_PAGE, cohortePage * ITEMS_PER_PAGE);

                    return (
                      <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                        {/* Buscador de Cohortes y Modo de Vista */}
                        <div style={{ ...panelCardStyle, padding: '16px', display: 'flex', gap: '16px', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap' }}>
                          <input
                            type="text"
                            placeholder="🔍 Buscar cohorte por código, período lectivo..."
                            value={cohorteSearch}
                            onChange={(e) => {
                              setCohorteSearch(e.target.value);
                              setCohortePage(1);
                            }}
                            style={{ ...inputStyle, flex: 1, minWidth: '200px', margin: 0 }}
                          />
                          <div style={{ display: 'flex', gap: '8px', background: 'rgba(255,255,255,0.03)', padding: '4px', borderRadius: '10px', border: '1px solid rgba(255,255,255,0.05)' }}>
                            <button
                              onClick={() => setCohorteViewMode('grid')}
                              style={{
                                border: 'none',
                                background: cohorteViewMode === 'grid' ? 'rgba(99,102,241,0.2)' : 'transparent',
                                color: cohorteViewMode === 'grid' ? '#a78bfa' : 'rgba(255,255,255,0.6)',
                                cursor: 'pointer',
                                fontSize: '13px',
                                fontWeight: 700,
                                padding: '6px 12px',
                                borderRadius: '8px',
                                transition: 'all 0.2s',
                              }}
                            >
                              🎴 Tarjetas
                            </button>
                            <button
                              onClick={() => setCohorteViewMode('list')}
                              style={{
                                border: 'none',
                                background: cohorteViewMode === 'list' ? 'rgba(99,102,241,0.2)' : 'transparent',
                                color: cohorteViewMode === 'list' ? '#a78bfa' : 'rgba(255,255,255,0.6)',
                                cursor: 'pointer',
                                fontSize: '13px',
                                fontWeight: 700,
                                padding: '6px 12px',
                                borderRadius: '8px',
                                transition: 'all 0.2s',
                              }}
                            >
                              📋 Lista
                            </button>
                          </div>
                        </div>

                        {filtered.length === 0 ? (
                          <div style={{ ...panelCardStyle, padding: '40px', textAlign: 'center', color: 'rgba(255,255,255,0.4)', fontSize: '14.5px' }}>
                            No se encontraron cohortes que coincidan con la búsqueda.
                          </div>
                        ) : (
                          <>
                            {cohorteViewMode === 'grid' ? (
                              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: '20px' }}>
                                {paginated.map((c) => (
                                  <div
                                    key={`${c.codsede}-${c.codopest}-${c.codcohorte}`}
                                    onClick={() => {
                                      setSelectedCohorte(c);
                                      setEditableCohorte({
                                        ...c,
                                        fecha_inicio: c.fecha_inicio ? c.fecha_inicio.substring(0, 10) : ''
                                      });
                                      setIsEditingCohorte(false);
                                      setCohorteActas([]);
                                      setShowCohorteDetailModal(true);
                                      loadCohorteActas(c.codcohorte);
                                    }}
                                    style={{
                                      background: 'rgba(255,255,255,0.02)',
                                      border: '1px solid rgba(255,255,255,0.05)',
                                      padding: '16px',
                                      borderRadius: '16px',
                                      cursor: 'pointer',
                                      transition: 'all 0.2s'
                                    }}
                                    onMouseEnter={(e) => {
                                      e.currentTarget.style.transform = 'translateY(-2px)';
                                      e.currentTarget.style.borderColor = 'rgba(99,102,241,0.3)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                                    }}
                                    onMouseLeave={(e) => {
                                      e.currentTarget.style.transform = 'translateY(0)';
                                      e.currentTarget.style.borderColor = 'rgba(255,255,255,0.05)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.02)';
                                    }}
                                  >
                                    <div style={{ fontSize: '16px', fontWeight: 700, color: '#a78bfa' }}>
                                      {c.fecha_inicio ? `Inicio: ${new Date(c.fecha_inicio).toLocaleDateString('es-VE')}` : 'Sin fecha de inicio'}
                                    </div>
                                    <div style={{ fontSize: '13px', marginTop: '6px', color: 'rgba(255,255,255,0.6)' }}>
                                      Programa: {c.codopest} | Período: {c.periodo_lectivo}
                                    </div>
                                    <div style={{ fontSize: '11px', marginTop: '10px', color: 'rgba(255,255,255,0.4)' }}>
                                      Sede: {c.codsede} | Código Cohorte: {c.codcohorte}
                                    </div>
                                  </div>
                                ))}
                              </div>
                            ) : (
                              <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                                {paginated.map((c) => (
                                  <div
                                    key={`${c.codsede}-${c.codopest}-${c.codcohorte}`}
                                    onClick={() => {
                                      setSelectedCohorte(c);
                                      setEditableCohorte({
                                        ...c,
                                        fecha_inicio: c.fecha_inicio ? c.fecha_inicio.substring(0, 10) : ''
                                      });
                                      setIsEditingCohorte(false);
                                      setCohorteActas([]);
                                      setShowCohorteDetailModal(true);
                                      loadCohorteActas(c.codcohorte);
                                    }}
                                    style={{
                                      background: 'rgba(255,255,255,0.02)',
                                      border: '1px solid rgba(255,255,255,0.05)',
                                      padding: '12px 20px',
                                      borderRadius: '12px',
                                      cursor: 'pointer',
                                      transition: 'all 0.2s',
                                      display: 'flex',
                                      justifyContent: 'space-between',
                                      alignItems: 'center',
                                      flexWrap: 'wrap',
                                      gap: '12px'
                                    }}
                                    onMouseEnter={(e) => {
                                      e.currentTarget.style.borderColor = 'rgba(99,102,241,0.3)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                                    }}
                                    onMouseLeave={(e) => {
                                      e.currentTarget.style.borderColor = 'rgba(255,255,255,0.05)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.02)';
                                    }}
                                  >
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
                                      <div style={{ fontSize: '15px', fontWeight: 700, color: '#a78bfa', minWidth: '150px' }}>
                                        {c.fecha_inicio ? `Inicio: ${new Date(c.fecha_inicio).toLocaleDateString('es-VE')}` : 'Sin fecha de inicio'}
                                      </div>
                                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.8)' }}>
                                        Programa: <span style={{ fontWeight: 600, color: '#fff' }}>{c.codopest}</span>
                                      </div>
                                    </div>
                                    <div style={{ display: 'flex', gap: '20px', alignItems: 'center' }}>
                                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.6)' }}>
                                        Período: <span style={{ color: 'rgba(255,255,255,0.9)' }}>{c.periodo_lectivo}</span>
                                      </div>
                                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.6)' }}>
                                        Sede: <span style={{ color: 'rgba(255,255,255,0.9)' }}>{c.codsede}</span>
                                      </div>
                                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.5)' }}>
                                        Código: {c.codcohorte}
                                      </div>
                                      <div style={{ color: '#6366f1', fontSize: '14px', fontWeight: 'bold' }}>➔</div>
                                    </div>
                                  </div>
                                ))}
                              </div>
                            )}

                            {/* Paginación */}
                            {totalPages > 1 && (
                              <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', gap: '16px', marginTop: '10px' }}>
                                <button
                                  disabled={cohortePage === 1}
                                  onClick={() => setCohortePage(cohortePage - 1)}
                                  style={{
                                    ...btnStyleSecondary,
                                    opacity: cohortePage === 1 ? 0.4 : 1,
                                    cursor: cohortePage === 1 ? 'not-allowed' : 'pointer'
                                  }}
                                >
                                  ◀ Anterior
                                </button>
                                <span style={{ fontSize: '14px', color: 'rgba(255,255,255,0.6)' }}>
                                  Página {cohortePage} de {totalPages} ({filtered.length} total)
                                </span>
                                <button
                                  disabled={cohortePage === totalPages}
                                  onClick={() => setCohortePage(cohortePage + 1)}
                                  style={{
                                    ...btnStyleSecondary,
                                    opacity: cohortePage === totalPages ? 0.4 : 1,
                                    cursor: cohortePage === totalPages ? 'not-allowed' : 'pointer'
                                  }}
                                >
                                  Siguiente ▶
                                </button>
                              </div>
                            )}
                          </>
                        )}
                      </div>
                    );
                  })()
                )}

                {/* Create Cohorte Modal */}
                {showCreateCohorte && (
                  <div style={modalBackdropStyle}>
                    <div style={modalContentStyle}>
                      <h3 style={{ margin: '0 0 20px', fontSize: '18px', fontWeight: 700 }}>Crear Nueva Cohorte</h3>
                      <form onSubmit={handleCreateCohorte} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div>
                          <label style={labelStyle}>Código de Cohorte</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: MATSGEg2026-I"
                            value={newCohorte.codcohorte}
                            onChange={(e) => setNewCohorte({ ...newCohorte, codcohorte: e.target.value })}
                            style={inputStyle}
                          />
                          <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', marginTop: '4px', display: 'block' }}>
                            Código auto-sugerido basado en la nomenclatura histórica de SACE. Puedes modificarlo libremente.
                          </span>
                        </div>

                         <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Sede (Ciudad)</label>
                            <select
                              required
                              value={modalSelectedCity}
                              onChange={(e) => handleModalCityChange(e.target.value)}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value="" disabled>Seleccione Sede...</option>
                              {citiesList.map(city => (
                                <option key={city} value={city}>{city}</option>
                              ))}
                            </select>
                          </div>
                          <div>
                            <label style={labelStyle}>Programa de Estudio</label>
                            <select
                              required
                              value={newCohorte.codopest}
                              onChange={(e) => {
                                const selectedProg = modalPrograms.find(p => p.codopest === e.target.value);
                                setNewCohorte({
                                  ...newCohorte,
                                  codopest: e.target.value,
                                  codsede: selectedProg ? selectedProg.codsede : newCohorte.codsede
                                });
                              }}
                              style={{ ...inputStyle, background: '#120f30' }}
                              disabled={!modalSelectedCity}
                            >
                              <option value="" disabled>Seleccione Programa...</option>
                              {modalPrograms.map(prog => (
                                <option key={prog.codopest} value={prog.codopest}>
                                  {prog.codopest} - {prog.mencion_especialidad} ({prog.tipo})
                                </option>
                              ))}
                            </select>
                          </div>
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Periodo Lectivo</label>
                            <input
                              type="text"
                              required
                              value={newCohorte.periodo_lectivo}
                              onChange={(e) => setNewCohorte({ ...newCohorte, periodo_lectivo: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Fecha de Inicio</label>
                            <input
                              type="date"
                              value={newCohorte.fecha_inicio}
                              onChange={(e) => setNewCohorte({ ...newCohorte, fecha_inicio: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div style={{ display: 'flex', justifySelf: 'flex-end', gap: '12px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowCreateCohorte(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>Crear Cohorte</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* TAB 5: PROFESORES */}
            {activeTab === 'profesores' && profile.role <= 4 && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                
                {profile.role === 4 ? (
                  // Teacher View: View/Edit own profile sheet directly
                  <div style={{ maxWidth: '600px', margin: '0 auto', width: '100%' }}>
                    <div style={panelCardStyle}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.06)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 700, color: '#a78bfa' }}>
                          👨‍🏫 Mi Ficha de Profesor
                        </h3>
                        {!isEditingTeacher && (
                          <button
                            onClick={() => {
                              setEditableTeacher({
                                apellidos_nombres: selectedTeacher?.apellidos_nombres || '',
                                nombres: selectedTeacher?.nombres || ''
                              });
                              setIsEditingTeacher(true);
                            }}
                            style={btnStylePrimary}
                          >
                            ✏️ Editar Datos
                          </button>
                        )}
                      </div>

                      {selectedTeacher ? (
                        isEditingTeacher ? (
                          <form onSubmit={handleSaveTeacherChanges} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                            <div>
                              <label style={labelStyle}>Cédula</label>
                              <input type="text" disabled value={selectedTeacher.cedula_profesor} style={{ ...inputStyle, opacity: 0.6, cursor: 'not-allowed' }} />
                            </div>
                            <div>
                              <label style={labelStyle}>Apellidos y Nombres (Formato completo)</label>
                              <input
                                type="text"
                                required
                                value={editableTeacher.apellidos_nombres}
                                onChange={(e) => setEditableTeacher({ ...editableTeacher, apellidos_nombres: e.target.value })}
                                style={inputStyle}
                              />
                            </div>
                            <div>
                              <label style={labelStyle}>Nombre Principal</label>
                              <input
                                type="text"
                                required
                                value={editableTeacher.nombres}
                                onChange={(e) => setEditableTeacher({ ...editableTeacher, nombres: e.target.value })}
                                style={inputStyle}
                              />
                            </div>
                            <div style={{ display: 'flex', gap: '12px', marginTop: '10px' }}>
                              <button type="submit" style={btnStylePrimary}>Guardar Cambios</button>
                              <button type="button" onClick={() => setIsEditingTeacher(false)} style={btnStyleSecondary}>Cancelar</button>
                            </div>
                          </form>
                        ) : (
                          <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '120px 1fr', gap: '10px', fontSize: '15px' }}>
                              <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>Cédula:</span>
                              <span style={{ fontWeight: 700, color: '#fff' }}>{selectedTeacher.cedula_profesor}</span>
                              
                              <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>Apellidos y Nombres:</span>
                              <span style={{ color: '#fff' }}>{selectedTeacher.apellidos_nombres}</span>

                              <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>Nombre Principal:</span>
                              <span style={{ color: '#fff' }}>{selectedTeacher.nombres}</span>

                              {selectedTeacher.cid && (
                                <>
                                  <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>ID Docente:</span>
                                  <span style={{ color: '#fff' }}>{selectedTeacher.cid}</span>
                                </>
                              )}
                            </div>
                          </div>
                        )
                      ) : (
                        <div style={{ textAlign: 'center', padding: '30px', color: 'rgba(255,255,255,0.4)' }}>
                          No se encontraron tus datos de profesor en el sistema.
                        </div>
                      )}
                    </div>
                  </div>
                ) : (
                  // Admin/Coordinator View: Full Width List
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                    {/* Directory Panel */}
                    <div style={panelCardStyle}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700 }}>Directorio de Profesores</h3>
                        {profile.role <= 2 && (
                          <button onClick={() => setShowCreateTeacher(true)} style={{ ...btnStylePrimary, padding: '8px 12px', fontSize: '12px' }}>
                            + Registrar Profesor
                          </button>
                        )}
                      </div>

                      {/* Buscador y Selector de Vista de Profesores */}
                      <div style={{ display: 'flex', gap: '16px', alignItems: 'center', marginBottom: '16px', flexWrap: 'wrap' }}>
                        <input
                          type="text"
                          placeholder="🔍 Buscar profesor por nombre, apellido o cédula..."
                          value={teacherSearch}
                          onChange={(e) => {
                            setTeacherSearch(e.target.value);
                            setTeacherPage(1);
                          }}
                          style={{ ...inputStyle, flex: 1, minWidth: '200px', margin: 0 }}
                        />
                        <div style={{ display: 'flex', gap: '8px', background: 'rgba(255,255,255,0.03)', padding: '4px', borderRadius: '10px', border: '1px solid rgba(255,255,255,0.05)' }}>
                          <button
                            onClick={() => setTeacherViewMode('grid')}
                            style={{
                              border: 'none',
                              background: teacherViewMode === 'grid' ? 'rgba(99,102,241,0.2)' : 'transparent',
                              color: teacherViewMode === 'grid' ? '#a78bfa' : 'rgba(255,255,255,0.6)',
                              cursor: 'pointer',
                              fontSize: '13px',
                              fontWeight: 700,
                              padding: '6px 12px',
                              borderRadius: '8px',
                              transition: 'all 0.2s',
                            }}
                          >
                            🎴 Tarjetas
                          </button>
                          <button
                            onClick={() => setTeacherViewMode('list')}
                            style={{
                              border: 'none',
                              background: teacherViewMode === 'list' ? 'rgba(99,102,241,0.2)' : 'transparent',
                              color: teacherViewMode === 'list' ? '#a78bfa' : 'rgba(255,255,255,0.6)',
                              cursor: 'pointer',
                              fontSize: '13px',
                              fontWeight: 700,
                              padding: '6px 12px',
                              borderRadius: '8px',
                              transition: 'all 0.2s',
                            }}
                          >
                            📋 Lista
                          </button>
                        </div>
                      </div>

                      {(() => {
                        const filtered = profesores
                          .filter((p) => {
                            const q = teacherSearch.toLowerCase();
                            return (
                              String(p.cedula_profesor).includes(q) ||
                              p.apellidos_nombres.toLowerCase().includes(q) ||
                              p.nombres.toLowerCase().includes(q)
                            );
                          })
                          .sort((a, b) => a.apellidos_nombres.localeCompare(b.apellidos_nombres));

                        const ITEMS_PER_PAGE = 6;
                        const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
                        const paginated = filtered.slice((teacherPage - 1) * ITEMS_PER_PAGE, teacherPage * ITEMS_PER_PAGE);

                        if (filtered.length === 0) {
                          return (
                            <div style={{ color: 'rgba(255,255,255,0.4)', textAlign: 'center', padding: '30px' }}>
                              No se encontraron profesores.
                            </div>
                          );
                        }

                        return (
                          <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                            {teacherViewMode === 'grid' ? (
                              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: '20px' }}>
                                {paginated.map((p) => (
                                  <div
                                    key={p.cedula_profesor}
                                    onClick={() => {
                                      setSelectedTeacher(p);
                                      setEditableTeacher({
                                        apellidos_nombres: p.apellidos_nombres || '',
                                        nombres: p.nombres || ''
                                      });
                                      setIsEditingTeacher(false);
                                      setShowTeacherDetailModal(true);
                                    }}
                                    style={{
                                      background: 'rgba(255,255,255,0.02)',
                                      border: '1px solid rgba(255,255,255,0.05)',
                                      padding: '18px',
                                      borderRadius: '16px',
                                      cursor: 'pointer',
                                      transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                                      display: 'flex',
                                      flexDirection: 'column',
                                      gap: '12px',
                                      boxShadow: '0 4px 15px rgba(0,0,0,0.1)'
                                    }}
                                    onMouseEnter={(e) => {
                                      e.currentTarget.style.transform = 'translateY(-4px)';
                                      e.currentTarget.style.borderColor = 'rgba(99,102,241,0.3)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                                      e.currentTarget.style.boxShadow = '0 8px 25px rgba(99,102,241,0.08)';
                                    }}
                                    onMouseLeave={(e) => {
                                      e.currentTarget.style.transform = 'translateY(0)';
                                      e.currentTarget.style.borderColor = 'rgba(255,255,255,0.05)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.02)';
                                      e.currentTarget.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                                    }}
                                  >
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                      <span style={{ fontSize: '11px', textTransform: 'uppercase', color: '#818cf8', fontWeight: 700 }}>
                                        Docente CIPPSV
                                      </span>
                                      <span style={{ fontSize: '11px', background: 'rgba(167,139,250,0.1)', color: '#a78bfa', padding: '2px 8px', borderRadius: '6px', fontWeight: 600 }}>
                                        C.I. {p.cedula_profesor}
                                      </span>
                                    </div>
                                    <h4 style={{ margin: 0, fontSize: '16px', fontWeight: 700, color: '#fff', lineHeight: 1.3 }}>
                                      {p.apellidos_nombres}
                                    </h4>
                                    <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', marginTop: '4px', borderTop: '1px solid rgba(255,255,255,0.05)', paddingTop: '8px' }}>
                                      Nombre: {p.nombres} {p.cid ? `| CID: ${p.cid}` : ''}
                                    </div>
                                  </div>
                                ))}
                              </div>
                            ) : (
                              <div style={{ overflowX: 'auto' }}>
                                <table style={tableStyle}>
                                  <thead>
                                    <tr>
                                      <th style={thStyle}>Cédula</th>
                                      <th style={thStyle}>Nombre y Apellido</th>
                                      <th style={{ ...thStyle, textAlign: 'center' }}>Acción</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    {paginated.map((p) => (
                                      <tr
                                        key={p.cedula_profesor}
                                        style={trStyle}
                                      >
                                        <td style={{ ...tdStyle, fontWeight: 700 }}>{p.cedula_profesor}</td>
                                        <td style={tdStyle}>{p.apellidos_nombres}</td>
                                        <td style={{ ...tdStyle, textAlign: 'center' }}>
                                          <button
                                            onClick={() => {
                                              setSelectedTeacher(p);
                                              setEditableTeacher({
                                                apellidos_nombres: p.apellidos_nombres || '',
                                                nombres: p.nombres || ''
                                              });
                                              setIsEditingTeacher(false);
                                              setShowTeacherDetailModal(true);
                                            }}
                                            style={{ ...btnStyleSecondary, padding: '4px 8px', fontSize: '11px' }}
                                          >
                                            Ver Ficha
                                          </button>
                                        </td>
                                      </tr>
                                    ))}
                                  </tbody>
                                </table>
                              </div>
                            )}

                            {/* Paginación */}
                            {totalPages > 1 && (
                              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '10px' }}>
                                <button
                                  disabled={teacherPage === 1}
                                  onClick={() => setTeacherPage(teacherPage - 1)}
                                  style={{
                                    ...btnStyleSecondary,
                                    padding: '4px 8px',
                                    fontSize: '11px',
                                    opacity: teacherPage === 1 ? 0.4 : 1,
                                    cursor: teacherPage === 1 ? 'not-allowed' : 'pointer'
                                  }}
                                >
                                  ◀ Ant.
                                </button>
                                <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.5)' }}>
                                  {teacherPage} / {totalPages} ({filtered.length})
                                </span>
                                <button
                                  disabled={teacherPage === totalPages}
                                  onClick={() => setTeacherPage(teacherPage + 1)}
                                  style={{
                                    ...btnStyleSecondary,
                                    padding: '4px 8px',
                                    fontSize: '11px',
                                    opacity: teacherPage === totalPages ? 0.4 : 1,
                                    cursor: teacherPage === totalPages ? 'not-allowed' : 'pointer'
                                  }}
                                >
                                  Sig. ▶
                                </button>
                              </div>
                            )}
                          </div>
                        );
                      })()}
                    </div>
                  </div>
                )}

            {/* Teacher Detail Modal */}
            {showTeacherDetailModal && selectedTeacher && (
              <div style={modalBackdropStyle}>
                <div style={{ ...modalContentStyle, maxWidth: '600px' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                    <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 800, color: '#a78bfa' }}>
                      👨‍🏫 Ficha del Docente
                    </h3>
                    <button
                      onClick={() => setShowTeacherDetailModal(false)}
                      style={{
                        background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                        width: '36px', height: '36px', display: 'flex', alignItems: 'center',
                        justifyContent: 'center', cursor: 'pointer', color: '#fff', fontSize: '16px'
                      }}
                    >
                      ✕
                    </button>
                  </div>

                  {isEditingTeacher ? (
                    <form onSubmit={handleSaveTeacherChanges} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                      <div>
                        <label style={labelStyle}>Cédula Profesor</label>
                        <input type="text" disabled value={selectedTeacher.cedula_profesor} style={{ ...inputStyle, opacity: 0.6, cursor: 'not-allowed' }} />
                      </div>
                      <div>
                        <label style={labelStyle}>Apellidos y Nombres (Formato completo)</label>
                        <input
                          type="text"
                          required
                          value={editableTeacher.apellidos_nombres}
                          onChange={(e) => setEditableTeacher({ ...editableTeacher, apellidos_nombres: e.target.value })}
                          style={inputStyle}
                        />
                      </div>
                      <div>
                        <label style={labelStyle}>Nombre Principal</label>
                        <input
                          type="text"
                          required
                          value={editableTeacher.nombres}
                          onChange={(e) => setEditableTeacher({ ...editableTeacher, nombres: e.target.value })}
                          style={inputStyle}
                        />
                      </div>
                      <div style={{ display: 'flex', gap: '12px', marginTop: '10px' }}>
                        <button type="submit" style={btnStylePrimary}>Guardar Cambios</button>
                        <button type="button" onClick={() => setIsEditingTeacher(false)} style={btnStyleSecondary}>Cancelar</button>
                      </div>
                    </form>
                  ) : (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                      <div style={{ display: 'grid', gridTemplateColumns: '150px 1fr', gap: '12px 10px', fontSize: '15px' }}>
                        <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>Cédula:</span>
                        <span style={{ fontWeight: 700, color: '#fff' }}>{selectedTeacher.cedula_profesor}</span>
                        
                        <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>Apellidos y Nombres:</span>
                        <span style={{ color: '#fff' }}>{selectedTeacher.apellidos_nombres}</span>

                        <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>Nombre Principal:</span>
                        <span style={{ color: '#fff' }}>{selectedTeacher.nombres}</span>

                        {selectedTeacher.cid && (
                          <>
                            <span style={{ color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>ID Docente (CID):</span>
                            <span style={{ color: '#fff' }}>{selectedTeacher.cid}</span>
                          </>
                        )}
                      </div>

                      {profile.role <= 2 && (
                        <div style={{ display: 'flex', justifyContent: 'flex-end', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '16px', marginTop: '10px' }}>
                          <button
                            onClick={() => {
                              setEditableTeacher({
                                apellidos_nombres: selectedTeacher.apellidos_nombres || '',
                                nombres: selectedTeacher.nombres || ''
                              });
                              setIsEditingTeacher(true);
                            }}
                            style={btnStylePrimary}
                          >
                            ✏️ Editar Ficha
                          </button>
                        </div>
                      )}
                    </div>
                  )}
                </div>
              </div>
            )}

                {/* Create Teacher Modal */}
                {showCreateTeacher && (
                  <div style={modalBackdropStyle}>
                    <div style={modalContentStyle}>
                      <h3 style={{ margin: '0 0 20px', fontSize: '18px', fontWeight: 700 }}>Registrar Nuevo Profesor</h3>
                      <form onSubmit={handleCreateTeacher} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div>
                          <label style={labelStyle}>Cédula Profesor</label>
                          <input
                            type="number"
                            required
                            value={newTeacher.cedula_profesor}
                            onChange={(e) => setNewTeacher({ ...newTeacher, cedula_profesor: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Apellidos y Nombres (Formato completo)</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: Gomez Maria"
                            value={newTeacher.apellidos_nombres}
                            onChange={(e) => setNewTeacher({ ...newTeacher, apellidos_nombres: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Nombres</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: Maria"
                            value={newTeacher.nombres}
                            onChange={(e) => setNewTeacher({ ...newTeacher, nombres: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'flex', justifySelf: 'flex-end', gap: '12px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowCreateTeacher(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>Registrar Profesor</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* TAB: GESTIÓN DE USUARIOS */}
            {activeTab === 'usuarios' && profile.role <= 2 && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                  <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700 }}>Gestión de Usuarios SACE</h3>
                  <button onClick={() => setShowCreateUserModal(true)} style={btnStylePrimary}>
                    + Registrar Nuevo Usuario
                  </button>
                </div>

                {/* Panel de Búsqueda y Verificación por Cédula */}
                <div style={{ ...panelCardStyle, display: 'flex', flexDirection: 'column', gap: '16px' }}>
                  <h4 style={{ margin: 0, fontSize: '15px', color: '#60a5fa', fontWeight: 700 }}>
                    🔍 Buscador y Validador de Accesos por Cédula
                  </h4>
                  <div style={{ display: 'flex', gap: '12px', flexWrap: 'wrap' }}>
                    <div style={{ flex: 1, minWidth: '200px' }}>
                      <input
                        type="number"
                        placeholder="Ingrese número de Cédula de Identidad (ej: 14999888)"
                        value={userSearchCedula}
                        onChange={(e) => setUserSearchCedula(e.target.value)}
                        style={inputStyle}
                      />
                    </div>
                    <button
                      onClick={handleSearchUserByCedula}
                      disabled={searchingUser || !userSearchCedula}
                      style={{ ...btnStylePrimary, padding: '0 24px', display: 'flex', alignItems: 'center', justifyContent: 'center' }}
                    >
                      {searchingUser ? 'Verificando...' : 'Verificar Cédula'}
                    </button>
                    {searchedUserResult && (
                      <button
                        onClick={() => { setSearchedUserResult(null); setUserSearchCedula(''); }}
                        style={btnStyleSecondary}
                      >
                        Limpiar Resultado
                      </button>
                    )}
                  </div>

                  {/* Resultados de la Búsqueda */}
                  {searchedUserResult && (
                    <div style={{
                      marginTop: '10px', padding: '20px', borderRadius: '16px',
                      background: 'rgba(255,255,255,0.01)', border: '1px solid rgba(255,255,255,0.06)'
                    }}>
                      {/* Caso 1: El usuario YA tiene acceso */}
                      {searchedUserResult.existsInUsuariosSace ? (
                        <div>
                          <div style={{
                            display: 'flex', alignItems: 'center', gap: '10px', color: '#34d399',
                            background: 'rgba(52,211,153,0.1)', border: '1px solid rgba(52,211,153,0.2)',
                            padding: '12px 16px', borderRadius: '12px', marginBottom: '16px', fontSize: '14px'
                          }}>
                            <span>✅</span>
                            <div>
                              <strong>Usuario Encontrado:</strong> Esta persona ya cuenta con acceso registrado en el sistema con el rol <strong>{ROLES[searchedUserResult.usuarioSace.rol]}</strong>.
                            </div>
                          </div>
                          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
                            <div>
                              <span style={detailLabelStyle}>Nombre de Usuario (Login)</span>
                              <div style={detailValueStyle}>{searchedUserResult.usuarioSace.user}</div>
                            </div>
                            <div>
                              <span style={detailLabelStyle}>Nombre Visible</span>
                              <div style={detailValueStyle}>{searchedUserResult.usuarioSace.usuario || 'No registrado'}</div>
                            </div>
                            <div>
                              <span style={detailLabelStyle}>Cédula Asociada</span>
                              <div style={detailValueStyle}>{searchedUserResult.usuarioSace.cedula || 'No registrada'}</div>
                            </div>
                            <div>
                              <span style={detailLabelStyle}>Rol Asignado</span>
                              <div style={{ ...detailValueStyle, color: '#a78bfa' }}>
                                {ROLES[searchedUserResult.usuarioSace.rol] || 'Sin Rol'}
                              </div>
                            </div>
                          </div>
                        </div>
                      ) : searchedUserResult.existsInDatosPersonales ? (
                        /* Caso 2: Tiene expediente pero NO tiene usuario */
                        <div>
                          <div style={{
                            display: 'flex', alignItems: 'center', gap: '10px', color: '#60a5fa',
                            background: 'rgba(96,165,250,0.1)', border: '1px solid rgba(96,165,250,0.2)',
                            padding: '12px 16px', borderRadius: '12px', marginBottom: '20px', fontSize: '14px'
                          }}>
                            <span>📋</span>
                            <div>
                              <strong>Expediente Localizado:</strong> Se encontró la ficha de <strong>{searchedUserResult.datosPersonales.nombres} {searchedUserResult.datosPersonales.apellidos}</strong>. No tiene un usuario asignado todavía.
                            </div>
                          </div>
                          
                          {/* Formulario rápido para asignar usuario */}
                          <div style={{ background: 'rgba(255,255,255,0.01)', padding: '16px', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.03)' }}>
                            <h5 style={{ margin: '0 0 16px', fontSize: '14px', color: '#a78bfa' }}>Asignar Usuario y Contraseña</h5>
                            <form onSubmit={(e) => {
                              e.preventDefault();
                              const form = e.currentTarget;
                              const userVal = (form.elements.namedItem('usr_username') as HTMLInputElement).value;
                              const passVal = (form.elements.namedItem('usr_password') as HTMLInputElement).value;
                              const roleVal = Number((form.elements.namedItem('usr_role') as HTMLSelectElement).value);
                              
                              setLoading(true);
                              fetch(`${apiUrl}/auth/register`, {
                                method: 'POST',
                                headers: getHeaders(),
                                body: JSON.stringify({
                                  username: userVal,
                                  password: passVal,
                                  role: roleVal,
                                  cedula: searchedUserResult.datosPersonales.cedula,
                                  usuario: `${searchedUserResult.datosPersonales.nombres} ${searchedUserResult.datosPersonales.apellidos}`
                                })
                              })
                              .then(async (res) => {
                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Error al asignar usuario');
                                setMessage({ type: 'success', text: `Usuario "${userVal}" asignado correctamente a ${searchedUserResult.datosPersonales.nombres}.` });
                                setSearchedUserResult(null);
                                setUserSearchCedula('');
                                return fetch(`${apiUrl}/auth/users`, { headers: getHeaders() });
                              })
                              .then(res => res && res.json())
                              .then(data => data && setSaceUsers(data))
                              .catch(err => setMessage({ type: 'error', text: err.message }))
                              .finally(() => setLoading(false));
                            }} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                                <div>
                                  <label style={labelStyle}>Usuario (Login)</label>
                                  <input type="text" name="usr_username" required defaultValue={searchedUserResult.datosPersonales.cedula} style={inputStyle} />
                                </div>
                                <div>
                                  <label style={labelStyle}>Contraseña Inicial</label>
                                  <input type="password" name="usr_password" required minLength={6} placeholder="Min. 6 caracteres" style={inputStyle} />
                                </div>
                                <div>
                                  <label style={labelStyle}>Rol del Usuario</label>
                                  <select name="usr_role" defaultValue={5} style={{ ...inputStyle, background: '#120f30' }}>
                                    <option value={5}>Estudiante</option>
                                    <option value={4}>Profesor</option>
                                    <option value={3}>Coordinador Control Estudios</option>
                                    <option value={2}>Administrador</option>
                                    <option value={1}>Super Usuario</option>
                                  </select>
                                </div>
                              </div>
                              <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: '10px' }}>
                                <button type="submit" style={btnStylePrimary}>
                                  Asignar Credenciales y Crear Acceso
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      ) : (
                        /* Caso 3: Cédula no existe en ningún lado */
                        <div>
                          <div style={{
                            display: 'flex', alignItems: 'center', gap: '10px', color: '#f59e0b',
                            background: 'rgba(245,158,11,0.1)', border: '1px solid rgba(245,158,11,0.2)',
                            padding: '12px 16px', borderRadius: '12px', marginBottom: '20px', fontSize: '14px'
                          }}>
                            <span>⚠️</span>
                            <div>
                              <strong>Sin registros:</strong> La cédula <strong>{userSearchCedula}</strong> no posee un expediente de datos personales ni cuenta de usuario en el SACE.
                            </div>
                          </div>

                          {/* Formulario rápido para crear y registrar todo */}
                          <div style={{ background: 'rgba(255,255,255,0.01)', padding: '16px', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.03)' }}>
                            <h5 style={{ margin: '0 0 16px', fontSize: '14px', color: '#a78bfa' }}>Registrar Nuevo Usuario Completo</h5>
                            <form onSubmit={(e) => {
                              e.preventDefault();
                              const form = e.currentTarget;
                              const userVal = (form.elements.namedItem('new_username') as HTMLInputElement).value;
                              const passVal = (form.elements.namedItem('new_password') as HTMLInputElement).value;
                              const nameVal = (form.elements.namedItem('new_displayname') as HTMLInputElement).value;
                              const cedulaVal = Number((form.elements.namedItem('new_cedula') as HTMLInputElement).value);
                              const roleVal = Number((form.elements.namedItem('new_role') as HTMLSelectElement).value);

                              setLoading(true);
                              fetch(`${apiUrl}/auth/register`, {
                                method: 'POST',
                                headers: getHeaders(),
                                body: JSON.stringify({
                                  username: userVal,
                                  password: passVal,
                                  role: roleVal,
                                  cedula: cedulaVal || null,
                                  usuario: nameVal
                                })
                              })
                              .then(async (res) => {
                                const data = await res.json();
                                if (!res.ok) throw new Error(data.message || 'Error al registrar usuario');
                                setMessage({ type: 'success', text: `Usuario "${userVal}" registrado exitosamente.` });
                                setSearchedUserResult(null);
                                setUserSearchCedula('');
                                return fetch(`${apiUrl}/auth/users`, { headers: getHeaders() });
                              })
                              .then(res => res && res.json())
                              .then(data => data && setSaceUsers(data))
                              .catch(err => setMessage({ type: 'error', text: err.message }))
                              .finally(() => setLoading(false));
                            }} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                                <div>
                                  <label style={labelStyle}>Usuario (Login)</label>
                                  <input type="text" name="new_username" required defaultValue={userSearchCedula} style={inputStyle} />
                                </div>
                                <div>
                                  <label style={labelStyle}>Contraseña Inicial</label>
                                  <input type="password" name="new_password" required minLength={6} placeholder="Min. 6 caracteres" style={inputStyle} />
                                </div>
                                <div>
                                  <label style={labelStyle}>Nombre Completo (Visible)</label>
                                  <input type="text" name="new_displayname" required placeholder="Ej: Juan Perez" style={inputStyle} />
                                </div>
                                <div>
                                  <label style={labelStyle}>Cédula Asociada (Opcional)</label>
                                  <input type="number" name="new_cedula" defaultValue={userSearchCedula} style={inputStyle} />
                                </div>
                                <div>
                                  <label style={labelStyle}>Rol del Usuario</label>
                                  <select name="new_role" defaultValue={5} style={{ ...inputStyle, background: '#120f30' }}>
                                    <option value={5}>Estudiante</option>
                                    <option value={4}>Profesor</option>
                                    <option value={3}>Coordinador Control Estudios</option>
                                    <option value={2}>Administrador</option>
                                    <option value={1}>Super Usuario</option>
                                  </select>
                                </div>
                              </div>
                              <div style={{ display: 'flex', justifyContent: 'flex-end', marginTop: '10px' }}>
                                <button type="submit" style={btnStylePrimary}>
                                  Registrar y Crear Acceso
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      )}
                    </div>
                  )}
                </div>

                <div style={panelCardStyle}>
                  {/* Search users input */}
                  <div style={{ marginBottom: '16px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '12px' }}>
                    <h4 style={{ margin: 0, fontSize: '16px', fontWeight: 700 }}>Listado de Usuarios</h4>
                    <input
                      type="text"
                      placeholder="🔍 Buscar por nombre, login o cédula..."
                      value={userSearch}
                      onChange={(e) => {
                        setUserSearch(e.target.value);
                        setUserPage(1);
                      }}
                      style={{ ...inputStyle, width: '300px', padding: '8px 12px', fontSize: '13px' }}
                    />
                  </div>

                  {(() => {
                    const filtered = saceUsers.filter((u) => {
                      const q = userSearch.toLowerCase();
                      return (
                        String(u.id).includes(q) ||
                        (u.user && u.user.toLowerCase().includes(q)) ||
                        (u.usuario && u.usuario.toLowerCase().includes(q)) ||
                        (u.cedula && String(u.cedula).includes(q)) ||
                        (ROLES[u.rol] && ROLES[u.rol].toLowerCase().includes(q))
                      );
                    });

                    const ITEMS_PER_PAGE = 8;
                    const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
                    const paginated = filtered.slice((userPage - 1) * ITEMS_PER_PAGE, userPage * ITEMS_PER_PAGE);

                    if (filtered.length === 0) {
                      return (
                        <div style={{ color: 'rgba(255,255,255,0.4)', textAlign: 'center', padding: '30px' }}>
                          No se encontraron usuarios.
                        </div>
                      );
                    }

                    return (
                      <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div style={{ overflowX: 'auto' }}>
                          <table style={tableStyle}>
                            <thead>
                              <tr>
                                <th style={{ ...thStyle, width: '80px', textAlign: 'center' }}>ID</th>
                                <th style={thStyle}>Login / Usuario</th>
                                <th style={thStyle}>Nombre Visible</th>
                                <th style={thStyle}>Cédula Vinculada</th>
                                <th style={thStyle}>Rol de Sistema</th>
                                <th style={{ ...thStyle, width: '120px', textAlign: 'center' }}>Acciones</th>
                              </tr>
                            </thead>
                            <tbody>
                              {paginated.map((u) => (
                                <tr key={u.id} style={trStyle}>
                                  <td style={{ ...tdStyle, textAlign: 'center', color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>{u.id}</td>
                                  <td style={{ ...tdStyle, fontWeight: 700, color: '#a78bfa' }}>{u.user}</td>
                                  <td style={tdStyle}>{u.usuario}</td>
                                  <td style={tdStyle}>{u.cedula || '-'}</td>
                                  <td style={tdStyle}>
                                    <span style={{
                                      background: u.rol === 1 ? 'rgba(239,68,68,0.1)' : u.rol === 2 ? 'rgba(245,158,11,0.1)' : u.rol === 3 ? 'rgba(59,130,246,0.1)' : u.rol === 4 ? 'rgba(16,185,129,0.1)' : 'rgba(139,92,246,0.1)',
                                      color: u.rol === 1 ? '#ef4444' : u.rol === 2 ? '#f59e0b' : u.rol === 3 ? '#3b82f6' : u.rol === 4 ? '#10b981' : '#a78bfa',
                                      padding: '4px 8px', borderRadius: '8px', fontSize: '12px', fontWeight: 600, border: `1px solid ${u.rol === 1 ? 'rgba(239,68,68,0.2)' : u.rol === 2 ? 'rgba(245,158,11,0.2)' : u.rol === 3 ? 'rgba(59,130,246,0.2)' : u.rol === 4 ? 'rgba(16,185,129,0.2)' : 'rgba(139,92,246,0.2)'}`
                                    }}>
                                      {ROLES[u.rol] || 'Desconocido'}
                                    </span>
                                  </td>
                                  <td style={{ ...tdStyle, textAlign: 'center' }}>
                                    <div style={{ display: 'flex', gap: '8px', justifyContent: 'center' }}>
                                      <button
                                        onClick={() => {
                                          setSelectedUserForEdit({
                                            id: u.id,
                                            username: u.user,
                                            usuario: u.usuario,
                                            cedula: u.cedula || '',
                                            role: u.rol,
                                            password: '',
                                          });
                                          setShowEditUserModal(true);
                                        }}
                                        style={{
                                          background: 'rgba(99,102,241,0.1)', border: '1px solid rgba(99,102,241,0.2)',
                                          color: '#a78bfa', padding: '6px 12px', borderRadius: '8px', cursor: 'pointer',
                                          fontSize: '12px', fontWeight: 600
                                        }}
                                      >
                                        Editar
                                      </button>
                                      <button
                                        onClick={() => handleDeleteUser(u.id, u.user)}
                                        disabled={u.id === profile.userId}
                                        style={{
                                          background: 'rgba(239,68,68,0.1)', border: '1px solid rgba(239,68,68,0.2)',
                                          color: '#f87171', padding: '6px 12px', borderRadius: '8px', cursor: u.id === profile.userId ? 'not-allowed' : 'pointer',
                                          fontSize: '12px', fontWeight: 600, opacity: u.id === profile.userId ? 0.4 : 1
                                        }}
                                      >
                                        Eliminar
                                      </button>
                                    </div>
                                  </td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>

                        {/* Paginación */}
                        {totalPages > 1 && (
                          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '10px' }}>
                            <button
                              disabled={userPage === 1}
                              onClick={() => setUserPage(userPage - 1)}
                              style={{
                                ...btnStyleSecondary,
                                padding: '6px 12px',
                                fontSize: '12px',
                                opacity: userPage === 1 ? 0.4 : 1,
                                cursor: userPage === 1 ? 'not-allowed' : 'pointer'
                              }}
                            >
                              ◀ Anterior
                            </button>
                            <span style={{ fontSize: '13px', color: 'rgba(255,255,255,0.5)' }}>
                              Página {userPage} de {totalPages} ({filtered.length} usuarios)
                            </span>
                            <button
                              disabled={userPage === totalPages}
                              onClick={() => setUserPage(userPage + 1)}
                              style={{
                                ...btnStyleSecondary,
                                padding: '6px 12px',
                                fontSize: '12px',
                                opacity: userPage === totalPages ? 0.4 : 1,
                                cursor: userPage === totalPages ? 'not-allowed' : 'pointer'
                              }}
                            >
                              Siguiente ▶
                            </button>
                          </div>
                        )}
                      </div>
                    );
                  })()}
                </div>

                {/* Registrar Nuevo Usuario Modal */}
                {showCreateUserModal && (
                  <div style={modalBackdropStyle}>
                    <div style={{ ...modalContentStyle, maxWidth: '500px' }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                          👤 Registrar Nuevo Usuario
                        </h3>
                        <button
                          onClick={() => setShowCreateUserModal(false)}
                          style={{
                            background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                            width: '32px', height: '32px', display: 'flex', alignItems: 'center',
                            justifyContent: 'center', cursor: 'pointer', color: '#fff'
                          }}
                        >
                          ✕
                        </button>
                      </div>

                      <form onSubmit={handleCreateUser} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div>
                          <label style={labelStyle}>Nombre de Usuario (Login)</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: mcarmen (o cédula del alumno/profesor)"
                            value={newUserAccount.username}
                            onChange={(e) => setNewUserAccount({ ...newUserAccount, username: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Contraseña inicial</label>
                          <input
                            type="password"
                            required
                            minLength={6}
                            placeholder="Mínimo 6 caracteres"
                            value={newUserAccount.password}
                            onChange={(e) => setNewUserAccount({ ...newUserAccount, password: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Nombre Visible (Display Name)</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: Maria del Carmen"
                            value={newUserAccount.usuario}
                            onChange={(e) => setNewUserAccount({ ...newUserAccount, usuario: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Cédula (Opcional)</label>
                            <input
                              type="number"
                              placeholder="Ej: 14999888"
                              value={newUserAccount.cedula}
                              onChange={(e) => setNewUserAccount({ ...newUserAccount, cedula: e.target.value })}
                              style={inputStyle}
                            />
                            <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', marginTop: '4px', display: 'block' }}>
                              Para vincular perfil
                            </span>
                          </div>
                          <div>
                            <label style={labelStyle}>Rol del Usuario</label>
                            <select
                              value={newUserAccount.role}
                              onChange={(e) => setNewUserAccount({ ...newUserAccount, role: Number(e.target.value) })}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value={5}>Estudiante</option>
                              <option value={4}>Profesor</option>
                              <option value={3}>Coordinador Control Estudios</option>
                              <option value={2}>Administrador</option>
                              <option value={1}>Super Usuario</option>
                            </select>
                          </div>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowCreateUserModal(false)} style={btnStyleSecondary}>
                            Cancelar
                          </button>
                          <button type="submit" style={btnStylePrimary}>
                            Registrar Usuario
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}

                {/* Editar Usuario Modal */}
                {showEditUserModal && selectedUserForEdit && (
                  <div style={modalBackdropStyle}>
                    <div style={{ ...modalContentStyle, maxWidth: '500px' }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                          ✏️ Editar Usuario: {selectedUserForEdit.username}
                        </h3>
                        <button
                          onClick={() => { setShowEditUserModal(false); setSelectedUserForEdit(null); }}
                          style={{
                            background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                            width: '32px', height: '32px', display: 'flex', alignItems: 'center',
                            justifyContent: 'center', cursor: 'pointer', color: '#fff'
                          }}
                        >
                          ✕
                        </button>
                      </div>

                      <form onSubmit={handleUpdateUser} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div>
                          <label style={labelStyle}>Nombre de Usuario (Login)</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: mcarmen"
                            value={selectedUserForEdit.username}
                            onChange={(e) => setSelectedUserForEdit({ ...selectedUserForEdit, username: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Nueva Contraseña (Dejar vacío para conservar la actual)</label>
                          <input
                            type="password"
                            placeholder="Dejar vacío o mínimo 6 caracteres"
                            value={selectedUserForEdit.password}
                            onChange={(e) => setSelectedUserForEdit({ ...selectedUserForEdit, password: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Nombre Visible (Display Name)</label>
                          <input
                            type="text"
                            required
                            placeholder="Ej: Maria del Carmen"
                            value={selectedUserForEdit.usuario}
                            onChange={(e) => setSelectedUserForEdit({ ...selectedUserForEdit, usuario: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Cédula (Opcional)</label>
                            <input
                              type="number"
                              placeholder="Ej: 14999888"
                              value={selectedUserForEdit.cedula}
                              onChange={(e) => setSelectedUserForEdit({ ...selectedUserForEdit, cedula: e.target.value })}
                              style={inputStyle}
                            />
                            <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', marginTop: '4px', display: 'block' }}>
                              Para vincular perfil
                            </span>
                          </div>
                          <div>
                            <label style={labelStyle}>Rol del Usuario</label>
                            <select
                              value={selectedUserForEdit.role}
                              onChange={(e) => setSelectedUserForEdit({ ...selectedUserForEdit, role: Number(e.target.value) })}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value={5}>Estudiante</option>
                              <option value={4}>Profesor</option>
                              <option value={3}>Coordinador Control Estudios</option>
                              <option value={2}>Administrador</option>
                              <option value={1}>Super Usuario</option>
                            </select>
                          </div>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                          <button type="button" onClick={() => { setShowEditUserModal(false); setSelectedUserForEdit(null); }} style={btnStyleSecondary}>
                            Cancelar
                          </button>
                          <button type="submit" style={btnStylePrimary}>
                            {loading ? 'Guardando...' : 'Guardar Cambios'}
                          </button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* TAB 6: EVALUACIONES Y NOTAS */}
            {activeTab === 'evaluaciones' && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                
                {profile.role === 5 ? (
                  // Student View: Beautiful Grouped Grades Report
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                      <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 700 }}>
                        📋 Reporte de Expediente Académico y Calificaciones
                      </h3>
                      {studentProfileData && (
                        <button
                          onClick={() => window.print()}
                          style={{ ...btnStyleSecondary, display: 'flex', alignItems: 'center', gap: '8px' }}
                        >
                          🖨️ Imprimir Reporte
                        </button>
                      )}
                    </div>

                    {loadingStudentProfile ? (
                      <div style={{ textAlign: 'center', padding: '40px', color: 'rgba(255,255,255,0.6)' }}>
                        <div style={{ fontSize: '24px', marginBottom: '10px' }}>⏳</div>
                        Cargando expediente académico completo...
                      </div>
                    ) : !studentProfileData || !studentProfileData.notas || studentProfileData.notas.length === 0 ? (
                      <div style={panelCardStyle}>
                        <div style={{ color: 'rgba(255,255,255,0.4)', textAlign: 'center', padding: '40px' }}>
                          No posees expedientes ni calificaciones registradas en el sistema.
                        </div>
                      </div>
                    ) : (
                      Object.entries(
                        studentProfileData.notas.reduce((acc: any, nota: any) => {
                          const cohCode = nota.codcohorte || 'No asignada';
                          if (!acc[cohCode]) acc[cohCode] = [];
                          acc[cohCode].push(nota);
                          return acc;
                        }, {})
                      ).map(([cohCode, cohNotas]: [string, any]) => {
                        const espec = studentProfileData.especializaciones?.find((e: any) => e.codcohorte === cohCode);
                        
                        // Calculate stats
                        const validGrades = cohNotas.filter((n: any) => n.calificacion !== null && n.calificacion !== 404);
                        const avg = validGrades.length > 0 
                          ? (validGrades.reduce((sum: number, n: any) => sum + n.calificacion, 0) / validGrades.length).toFixed(2)
                          : 'S/N';
                        
                        const approvedCredits = cohNotas.filter((n: any) => n.calificacion !== null && n.calificacion >= 10 && n.calificacion !== 404 && n.creditos)
                          .reduce((sum: number, n: any) => sum + Number(n.creditos), 0);

                        return (
                          <div key={cohCode} style={{ ...panelCardStyle, display: 'flex', flexDirection: 'column', gap: '20px' }}>
                            {/* Header metadata */}
                            <div style={{
                              display: 'flex', flexDirection: 'column', gap: '8px', 
                              borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px'
                            }}>
                              <span style={{ fontSize: '11px', textTransform: 'uppercase', color: '#818cf8', fontWeight: 600 }}>
                                {espec?.tipo || 'Postgrado'}
                              </span>
                              <h4 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#fff' }}>
                                {espec?.programa || 'Programa Académico No Especificado'}
                              </h4>
                              {espec?.mencion && espec.mencion !== 'No registrada' && (
                                <span style={{ fontSize: '14px', color: 'rgba(255,255,255,0.6)' }}>
                                  Mención: {espec.mencion}
                                </span>
                              )}
                              
                              <div style={{
                                display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', 
                                gap: '12px', marginTop: '8px', fontSize: '13px', color: 'rgba(255,255,255,0.5)'
                              }}>
                                <div>
                                  <strong>Cohorte:</strong> <span style={{ color: '#c084fc', fontWeight: 600 }}>{cohCode}</span>
                                </div>
                                <div>
                                  <strong>Fecha de Inicio:</strong> {espec?.fecha_inicio ? new Date(espec.fecha_inicio).toLocaleDateString('es-VE') : 'No asignada'}
                                </div>
                                <div>
                                  <strong>Sede:</strong> {espec?.codsede || 'Principal'}
                                </div>
                                <div>
                                  <strong>Estado Académico:</strong>{' '}
                                  <span style={{
                                    color: espec?.status === 'Activo' ? '#4ade80' : '#f87171',
                                    background: espec?.status === 'Activo' ? 'rgba(74,222,128,0.1)' : 'rgba(248,113,113,0.1)',
                                    padding: '2px 6px', borderRadius: '4px', fontSize: '11px', fontWeight: 600
                                  }}>
                                    {espec?.status || 'Activo'}
                                  </span>
                                </div>
                              </div>
                            </div>

                            {/* Table of grades */}
                            <div style={{ overflowX: 'auto' }}>
                              <table style={tableStyle}>
                                <thead>
                                  <tr>
                                    <th style={{ ...thStyle, width: '120px' }}>Código Asig.</th>
                                    <th style={thStyle}>Asignatura / Materia</th>
                                    <th style={{ ...thStyle, width: '100px', textAlign: 'center' }}>Período</th>
                                    <th style={{ ...thStyle, width: '80px', textAlign: 'center' }}>Créditos</th>
                                    <th style={{ ...thStyle, width: '120px' }}>Nro. Acta</th>
                                    <th style={{ ...thStyle, width: '100px', textAlign: 'center' }}>Calificación</th>
                                    <th style={{ ...thStyle, width: '110px', textAlign: 'center' }}>Equivalencia</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {cohNotas.map((n: any) => (
                                    <tr key={`${n.codacta}-${n.codasig}`} style={trStyle}>
                                      <td style={{ ...tdStyle, color: 'rgba(255,255,255,0.6)', fontWeight: 600 }}>{n.codasig}</td>
                                      <td style={{ ...tdStyle, fontWeight: 600 }}>{n.asignatura}</td>
                                      <td style={{ ...tdStyle, textAlign: 'center' }}>{n.periodo || '-'}</td>
                                      <td style={{ ...tdStyle, textAlign: 'center', color: '#60a5fa' }}>{n.creditos || '-'}</td>
                                      <td style={{ ...tdStyle, color: 'rgba(255,255,255,0.4)', fontSize: '12px' }}>{n.codacta}</td>
                                      <td style={{
                                        ...tdStyle, textAlign: 'center', fontWeight: 700,
                                        color: getCalificacionColor(n.calificacion)
                                      }}>
                                        {formatCalificacion(n.calificacion)}
                                      </td>
                                      <td style={{ ...tdStyle, textAlign: 'center' }}>
                                        {n.codeq ? (
                                          <span style={{
                                            background: 'rgba(167,139,250,0.1)', color: '#c084fc',
                                            padding: '2px 6px', borderRadius: '4px', fontSize: '11px', fontWeight: 600
                                          }}>
                                            {n.codeq}
                                          </span>
                                        ) : '-'}
                                      </td>
                                    </tr>
                                  ))}
                                </tbody>
                              </table>
                            </div>

                            {/* Summary footer */}
                            <div style={{
                              display: 'flex', justifyContent: 'flex-end', gap: '24px', flexWrap: 'wrap',
                              background: 'rgba(255,255,255,0.01)', border: '1px solid rgba(255,255,255,0.03)',
                              padding: '12px 20px', borderRadius: '12px', fontSize: '13px'
                            }}>
                              <div>
                                <span style={{ color: 'rgba(255,255,255,0.5)' }}>Materias cursadas: </span>
                                <strong>{cohNotas.length}</strong>
                              </div>
                              <div>
                                <span style={{ color: 'rgba(255,255,255,0.5)' }}>Créditos aprobados: </span>
                                <strong style={{ color: '#60a5fa' }}>{approvedCredits} U.C.</strong>
                              </div>
                              <div>
                                <span style={{ color: 'rgba(255,255,255,0.5)' }}>Promedio Aritmético: </span>
                                <strong style={{ color: '#4ade80', fontSize: '14px' }}>{avg}</strong>
                              </div>
                            </div>
                          </div>
                        );
                      })
                    )}
                  </div>
                ) : (
                  // Teacher / Admin View: List of actas (Full Width List)
                  <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                    
                    {/* Actas List */}
                    <div style={panelCardStyle}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700 }}>Actas de Evaluación</h3>
                        {profile.role <= 2 && (
                          <button onClick={() => setShowCreateActa(true)} style={{ ...btnStylePrimary, padding: '8px 12px', fontSize: '12px' }}>
                            + Nueva Acta
                          </button>
                        )}
                      </div>

                      {/* Filtros Avanzados por Sede, Programa y Cohorte */}
                      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '10px', marginBottom: '16px', background: 'rgba(255,255,255,0.01)', padding: '12px', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.03)' }}>
                        <div>
                          <label style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase', fontWeight: 600, display: 'block', marginBottom: '4px' }}>
                            Sede
                          </label>
                          <select
                            value={filterCity}
                            onChange={(e) => handleFilterCityChange(e.target.value)}
                            style={{ ...inputStyle, padding: '6px 10px', fontSize: '12px', background: '#120f30', height: '36px' }}
                          >
                            <option value="">Todas</option>
                            {citiesList.map(city => (
                              <option key={city} value={city}>{city}</option>
                            ))}
                          </select>
                        </div>
                        <div>
                          <label style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase', fontWeight: 600, display: 'block', marginBottom: '4px' }}>
                            Programa
                          </label>
                          <select
                            value={filterProgramCode}
                            onChange={(e) => handleFilterProgramChange(e.target.value)}
                            style={{ ...inputStyle, padding: '6px 10px', fontSize: '12px', background: '#120f30', height: '36px' }}
                            disabled={!filterCity}
                          >
                            <option value="">Todos</option>
                            {filterPrograms.map(prog => (
                              <option key={prog.codopest} value={prog.codopest}>
                                {prog.codopest}
                              </option>
                            ))}
                          </select>
                        </div>
                        <div>
                          <label style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase', fontWeight: 600, display: 'block', marginBottom: '4px' }}>
                            Cohorte
                          </label>
                          <select
                            value={filterCohorteCode}
                            onChange={(e) => {
                              setFilterCohorteCode(e.target.value);
                              setActaPage(1);
                            }}
                            style={{ ...inputStyle, padding: '6px 10px', fontSize: '12px', background: '#120f30', height: '36px' }}
                            disabled={!filterProgramCode}
                          >
                            <option value="">Todas</option>
                            {filterCohortes.map(c => (
                              <option key={c.codcohorte} value={c.codcohorte}>
                                {c.codcohorte}
                              </option>
                            ))}
                          </select>
                        </div>
                      </div>

                      {/* Buscador y Selector de Vista de Actas */}
                      <div style={{ display: 'flex', gap: '16px', alignItems: 'center', marginBottom: '16px', flexWrap: 'wrap' }}>
                        <input
                          type="text"
                          placeholder="🔍 Buscar acta, materia, cohorte o profesor..."
                          value={actaSearch}
                          onChange={(e) => {
                            setActaSearch(e.target.value);
                            setActaPage(1);
                          }}
                          style={{ ...inputStyle, flex: 1, minWidth: '200px', margin: 0 }}
                        />
                        <div style={{ display: 'flex', gap: '8px', background: 'rgba(255,255,255,0.03)', padding: '4px', borderRadius: '10px', border: '1px solid rgba(255,255,255,0.05)' }}>
                          <button
                            onClick={() => setActaViewMode('grid')}
                            style={{
                              border: 'none',
                              background: actaViewMode === 'grid' ? 'rgba(99,102,241,0.2)' : 'transparent',
                              color: actaViewMode === 'grid' ? '#a78bfa' : 'rgba(255,255,255,0.6)',
                              cursor: 'pointer',
                              fontSize: '13px',
                              fontWeight: 700,
                              padding: '6px 12px',
                              borderRadius: '8px',
                              transition: 'all 0.2s',
                            }}
                          >
                            🎴 Tarjetas
                          </button>
                          <button
                            onClick={() => setActaViewMode('list')}
                            style={{
                              border: 'none',
                              background: actaViewMode === 'list' ? 'rgba(99,102,241,0.2)' : 'transparent',
                              color: actaViewMode === 'list' ? '#a78bfa' : 'rgba(255,255,255,0.6)',
                              cursor: 'pointer',
                              fontSize: '13px',
                              fontWeight: 700,
                              padding: '6px 12px',
                              borderRadius: '8px',
                              transition: 'all 0.2s',
                            }}
                          >
                            📋 Lista
                          </button>
                        </div>
                      </div>
                      
                      {(() => {
                        const filtered = actas
                          .filter((a) => {
                            // 1. Filtrar por búsqueda de texto
                            const q = actaSearch.toLowerCase();
                            const matchesSearch = (
                              a.codacta.toLowerCase().includes(q) ||
                              a.codasig.toLowerCase().includes(q) ||
                              a.codcohorte.toLowerCase().includes(q) ||
                              (a.cedula_profesor && String(a.cedula_profesor).includes(q))
                            );
                            if (!matchesSearch) return false;

                            // 2. Filtrar por Sede/Ciudad
                            if (filterCity) {
                              const citySede = getSedeFromCity(filterCity);
                              if (citySede) {
                                const cohSede = extractSedeFromCohorte(a.codcohorte);
                                if (cohSede !== citySede) return false;
                              }
                            }

                            // 3. Filtrar por Programa
                            if (filterProgramCode) {
                              const suffix = filterProgramCode.includes('-') ? filterProgramCode.split('-')[1] : filterProgramCode;
                              const cleanCohorte = a.codcohorte.toUpperCase();
                              const cohSede = extractSedeFromCohorte(a.codcohorte);
                              const suffixStart = cleanCohorte.indexOf(cohSede) === 0 ? cohSede.length : 0;
                              const suffixPart = cleanCohorte.substring(suffixStart);
                              if (!suffixPart.startsWith(suffix.toUpperCase())) return false;
                            }

                            // 4. Filtrar por Cohorte
                            if (filterCohorteCode) {
                              if (a.codcohorte !== filterCohorteCode) return false;
                            }

                            return true;
                          })
                          .sort((a, b) => b.codacta.localeCompare(a.codacta));

                        const ITEMS_PER_PAGE = 8;
                        const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
                        const paginated = filtered.slice((actaPage - 1) * ITEMS_PER_PAGE, actaPage * ITEMS_PER_PAGE);

                        if (filtered.length === 0) {
                          return (
                            <div style={{ color: 'rgba(255,255,255,0.4)', textAlign: 'center', padding: '20px' }}>
                              No se encontraron actas de evaluación.
                            </div>
                          );
                        }

                        return (
                          <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                            {actaViewMode === 'grid' ? (
                              <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: '20px' }}>
                                {paginated.map((a) => (
                                  <div
                                    key={`${a.codcohorte}-${a.codasig}-${a.codacta}`}
                                    onClick={() => {
                                      setSelectedActaDetail(a);
                                      setEditableActa({
                                        ...a,
                                        fecha_aprobacion: a.fecha_aprobacion ? a.fecha_aprobacion.substring(0, 10) : ''
                                      });
                                      setIsEditingActa(false);
                                      setActaNotasDetail([]);
                                      setShowActaDetailModal(true);
                                      loadActaNotasDetail(a.codacta);
                                    }}
                                    style={{
                                      background: 'rgba(255,255,255,0.02)',
                                      border: '1px solid rgba(255,255,255,0.05)',
                                      padding: '18px',
                                      borderRadius: '16px',
                                      cursor: 'pointer',
                                      transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
                                      display: 'flex',
                                      flexDirection: 'column',
                                      gap: '12px',
                                      boxShadow: '0 4px 15px rgba(0,0,0,0.1)'
                                    }}
                                    onMouseEnter={(e) => {
                                      e.currentTarget.style.transform = 'translateY(-4px)';
                                      e.currentTarget.style.borderColor = 'rgba(99,102,241,0.3)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                                      e.currentTarget.style.boxShadow = '0 8px 25px rgba(99,102,241,0.08)';
                                    }}
                                    onMouseLeave={(e) => {
                                      e.currentTarget.style.transform = 'translateY(0)';
                                      e.currentTarget.style.borderColor = 'rgba(255,255,255,0.05)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.02)';
                                      e.currentTarget.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
                                    }}
                                  >
                                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                      <span style={{ fontSize: '11px', textTransform: 'uppercase', color: '#818cf8', fontWeight: 700 }}>
                                        Acta de Evaluación
                                      </span>
                                      <span style={{ fontSize: '11px', background: 'rgba(96,165,250,0.1)', color: '#60a5fa', padding: '2px 8px', borderRadius: '6px', fontWeight: 600 }}>
                                        {a.codacta}
                                      </span>
                                    </div>
                                    <h4 style={{ margin: 0, fontSize: '15px', fontWeight: 700, color: '#fff', lineHeight: 1.3 }}>
                                      {a.asignatura_nombre}
                                    </h4>
                                    <div style={{ fontSize: '12.5px', color: 'rgba(255,255,255,0.5)', display: 'flex', flexDirection: 'column', gap: '4px', borderTop: '1px solid rgba(255,255,255,0.05)', paddingTop: '10px' }}>
                                      <div>📅 <span style={{ color: '#fff', fontWeight: 500 }}>{a.fecha_creacion ? new Date(a.fecha_creacion).toLocaleDateString('es-VE') : 'Sin fecha'}</span></div>
                                      <div>👥 Cohorte: <span style={{ color: 'rgba(255,255,255,0.8)' }}>{a.codcohorte}</span></div>
                                      <div>👨‍🏫 Profesor: <span style={{ color: 'rgba(255,255,255,0.8)' }}>{a.profesor || 'No asignado'}</span></div>
                                    </div>
                                  </div>
                                ))}
                              </div>
                            ) : (
                              <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                                {paginated.map((a) => (
                                  <div
                                    key={`${a.codcohorte}-${a.codasig}-${a.codacta}`}
                                    onClick={() => {
                                      setSelectedActaDetail(a);
                                      setEditableActa({
                                        ...a,
                                        fecha_aprobacion: a.fecha_aprobacion ? a.fecha_aprobacion.substring(0, 10) : ''
                                      });
                                      setIsEditingActa(false);
                                      setActaNotasDetail([]);
                                      setShowActaDetailModal(true);
                                      loadActaNotasDetail(a.codacta);
                                    }}
                                    style={{
                                      background: 'rgba(255,255,255,0.02)',
                                      border: '1px solid rgba(255,255,255,0.05)',
                                      padding: '12px 20px',
                                      borderRadius: '12px',
                                      cursor: 'pointer',
                                      transition: 'all 0.2s',
                                      display: 'flex',
                                      justifyContent: 'space-between',
                                      alignItems: 'center',
                                      flexWrap: 'wrap',
                                      gap: '12px'
                                    }}
                                    onMouseEnter={(e) => {
                                      e.currentTarget.style.borderColor = 'rgba(99,102,241,0.3)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                                    }}
                                    onMouseLeave={(e) => {
                                      e.currentTarget.style.borderColor = 'rgba(255,255,255,0.05)';
                                      e.currentTarget.style.background = 'rgba(255,255,255,0.02)';
                                    }}
                                  >
                                    <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
                                      <div style={{ fontSize: '15px', fontWeight: 700, color: '#fff', minWidth: '150px' }}>
                                        {a.fecha_creacion ? `Fecha: ${new Date(a.fecha_creacion).toLocaleDateString('es-VE')}` : 'Sin fecha'}
                                      </div>
                                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.8)' }}>
                                        Asignatura: <span style={{ fontWeight: 600, color: '#a78bfa' }}>{a.asignatura_nombre} ({a.codasig})</span>
                                      </div>
                                    </div>
                                    <div style={{ display: 'flex', gap: '20px', alignItems: 'center' }}>
                                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.6)' }}>
                                        Cohorte: <span style={{ color: 'rgba(255,255,255,0.9)' }}>{a.codcohorte}</span>
                                      </div>
                                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.6)' }}>
                                        Código Acta: <span style={{ color: 'rgba(255,255,255,0.9)' }}>{a.codacta}</span>
                                      </div>
                                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.5)', background: 'rgba(255,255,255,0.08)', padding: '2px 8px', borderRadius: '6px' }}>
                                        Profesor: {a.profesor || 'No asignado'}
                                      </div>
                                      <div style={{ color: '#6366f1', fontSize: '14px', fontWeight: 'bold' }}>➔</div>
                                    </div>
                                  </div>
                                ))}
                              </div>
                            )}

                            {/* Controles de Paginación */}
                            {totalPages > 1 && (
                              <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '10px' }}>
                                <button
                                  disabled={actaPage === 1}
                                  onClick={() => setActaPage(actaPage - 1)}
                                  style={{
                                    ...btnStyleSecondary,
                                    padding: '4px 8px',
                                    fontSize: '11px',
                                    opacity: actaPage === 1 ? 0.4 : 1,
                                    cursor: actaPage === 1 ? 'not-allowed' : 'pointer'
                                  }}
                                >
                                  ◀ Ant.
                                </button>
                                <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.5)' }}>
                                  {actaPage} / {totalPages} ({filtered.length})
                                </span>
                                <button
                                  disabled={actaPage === totalPages}
                                  onClick={() => setActaPage(actaPage + 1)}
                                  style={{
                                    ...btnStyleSecondary,
                                    padding: '4px 8px',
                                    fontSize: '11px',
                                    opacity: actaPage === totalPages ? 0.4 : 1,
                                    cursor: actaPage === totalPages ? 'not-allowed' : 'pointer'
                                  }}
                                >
                                  Sig. ▶
                                </button>
                              </div>
                            )}
                          </div>
                        );
                      })()}
                    </div>



                  </div>
                )}

              </div>
            )}

            {/* Create Acta Modal */}
            {showCreateActa && (
              <div style={modalBackdropStyle}>
                <div style={modalContentStyle}>
                  <h3 style={{ margin: '0 0 20px', fontSize: '18px', fontWeight: 700 }}>Crear Acta de Evaluación</h3>
                  <form onSubmit={handleCreateActa} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    {/* Ciudad / Sede */}
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                      <div>
                        <label style={labelStyle}>Sede (Ciudad)</label>
                        <select
                          required
                          value={actaSelectedCity}
                          onChange={(e) => handleActaCityChange(e.target.value)}
                          style={{ ...inputStyle, background: '#120f30' }}
                        >
                          <option value="" disabled>Seleccione Sede...</option>
                          {citiesList.map(city => (
                            <option key={city} value={city}>{city}</option>
                          ))}
                        </select>
                      </div>
                      <div>
                        <label style={labelStyle}>Programa de Estudio</label>
                        <select
                          required
                          value={actaSelectedProgramCode}
                          onChange={(e) => handleActaProgramChange(e.target.value)}
                          style={{ ...inputStyle, background: '#120f30' }}
                          disabled={!actaSelectedCity}
                        >
                          <option value="" disabled>Seleccione Programa...</option>
                          {actaPrograms.map(prog => (
                            <option key={prog.codopest} value={prog.codopest}>
                              {prog.codopest} - {prog.mencion_especialidad} ({prog.tipo})
                            </option>
                          ))}
                        </select>
                      </div>
                    </div>

                    {/* Cohorte y Materia */}
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                      <div>
                        <label style={labelStyle}>Cohorte</label>
                        <select
                          required
                          value={newActa.codcohorte}
                          onChange={(e) => setNewActa(prev => ({ ...prev, codcohorte: e.target.value }))}
                          style={{ ...inputStyle, background: '#120f30' }}
                          disabled={!actaSelectedProgramCode}
                        >
                          <option value="" disabled>Seleccione Cohorte...</option>
                          {actaCohortes.map(c => (
                            <option key={c.codcohorte} value={c.codcohorte}>
                              {c.codcohorte} (Lectivo: {c.periodo_lectivo})
                            </option>
                          ))}
                        </select>
                      </div>
                      <div>
                        <label style={labelStyle}>Asignatura (Materia)</label>
                        <select
                          required
                          value={newActa.codasig}
                          onChange={(e) => setNewActa(prev => ({ ...prev, codasig: e.target.value }))}
                          style={{ ...inputStyle, background: '#120f30' }}
                          disabled={!actaSelectedProgramCode}
                        >
                          <option value="" disabled>Seleccione Asignatura...</option>
                          {actaSubjects.map(s => (
                            <option key={s.codasig} value={s.codasig}>
                              {s.codasig_imp || s.codasig} - {s.asignatura} ({s.creditos} U.C.)
                            </option>
                          ))}
                        </select>
                      </div>
                    </div>

                    {/* Código del Acta */}
                    <div>
                      <label style={labelStyle}>Código de Acta</label>
                      <input
                        type="text"
                        required
                        placeholder="Se autogenera al seleccionar cohorte y materia..."
                        value={newActa.codacta}
                        onChange={(e) => setNewActa({ ...newActa, codacta: e.target.value })}
                        style={inputStyle}
                      />
                      <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', marginTop: '4px', display: 'block' }}>
                        Código auto-sugerido. Puedes modificarlo si es necesario.
                      </span>
                    </div>

                    {/* Profesor */}
                    <div>
                      <label style={labelStyle}>Cédula Profesor Asignado (Opcional)</label>
                      <input
                        type="number"
                        placeholder="Se autocompleta con el último docente de esta materia..."
                        value={newActa.cedula_profesor}
                        onChange={(e) => {
                          setNewActa({ ...newActa, cedula_profesor: e.target.value });
                          setSuggestedTeacherName(''); // Limpiar si escribe manualmente
                        }}
                        style={inputStyle}
                      />
                      {suggestedTeacherName && (
                        <span style={{ fontSize: '11px', color: '#a78bfa', marginTop: '4px', display: 'block', fontWeight: 600 }}>
                          👤 Docente sugerido: {suggestedTeacherName}
                        </span>
                      )}
                    </div>

                    <div style={{ display: 'flex', justifySelf: 'flex-end', gap: '12px', marginTop: '10px' }}>
                      <button type="button" onClick={() => setShowCreateActa(false)} style={btnStyleSecondary}>Cancelar</button>
                      <button type="submit" style={btnStylePrimary}>Crear Acta</button>
                    </div>
                  </form>
                </div>
              </div>
            )}

            {/* Add Nota Modal */}
            {showAddNota && (
              <div style={modalBackdropStyle}>
                <div style={modalContentStyle}>
                  <h3 style={{ margin: '0 0 20px', fontSize: '18px', fontWeight: 700 }}>Cargar Calificación en {newNota.codacta || selectedActa?.codacta}</h3>
                  <form onSubmit={handleSaveNota} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    <div>
                      <label style={labelStyle}>Cédula de Estudiante</label>
                      <input
                        type="number"
                        required
                        value={newNota.cedula}
                        onChange={(e) => setNewNota({ ...newNota, cedula: e.target.value })}
                        style={inputStyle}
                      />
                    </div>

                    <div>
                      <label style={labelStyle}>Calificación (Escala 0 - 20)</label>
                      <input
                        type="number"
                        required
                        min={0}
                        max={20}
                        placeholder="Ej: 18"
                        value={newNota.calificacion}
                        onChange={(e) => setNewNota({ ...newNota, calificacion: e.target.value })}
                        style={inputStyle}
                      />
                    </div>

                    <div style={{ display: 'flex', justifySelf: 'flex-end', gap: '12px', marginTop: '10px' }}>
                      <button type="button" onClick={() => setShowAddNota(false)} style={btnStyleSecondary}>Cancelar</button>
                      <button type="submit" style={btnStylePrimary}>Cargar Nota</button>
                    </div>
                  </form>
                </div>
              </div>
            )}

            {/* Full Student Data Modal */}
            {showFullDataModal && singleStudent && (
              <div style={modalBackdropStyle}>
                <div style={{ ...modalContentStyle, maxWidth: '850px', maxHeight: '85vh', overflowY: 'auto' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                    <div>
                      <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 800, color: '#a78bfa' }}>
                        📋 Ficha de Datos Personales Completa
                      </h3>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Expediente C.I. {singleStudent.cedula}
                      </span>
                    </div>
                    <button
                      onClick={() => setShowFullDataModal(false)}
                      style={{
                        background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                        width: '36px', height: '36px', display: 'flex', alignItems: 'center',
                        justifyContent: 'center', cursor: 'pointer', color: '#fff', fontSize: '16px'
                      }}
                    >
                      ✕
                    </button>
                  </div>

                  <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    {/* Seccion 1: Identificacion y Basicos */}
                    <div>
                      <h4 style={{ margin: '0 0 12px', fontSize: '14px', color: '#60a5fa', borderBottom: '1px solid rgba(96,165,250,0.2)', paddingBottom: '6px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                        Datos de Identificación y Personales
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Cédula (No editable)</span>
                          <div style={detailValueStyle}>{singleStudent.cedula}</div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Nacionalidad</span>
                          {isEditingProfile ? (
                            <select
                              value={editableStudent.nacionalidad || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, nacionalidad: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            >
                              <option value="">Seleccione</option>
                              <option value="Venezolana">Venezolana</option>
                              <option value="Extranjera">Extranjera</option>
                            </select>
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.nacionalidad || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Nombres</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.nombres || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, nombres: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.nombres || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Apellidos</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.apellidos || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, apellidos: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.apellidos || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Sexo</span>
                          {isEditingProfile ? (
                            <select
                              value={editableStudent.sexo || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, sexo: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            >
                              <option value="">Seleccione</option>
                              <option value="Masculino">Masculino</option>
                              <option value="Femenino">Femenino</option>
                            </select>
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.sexo || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Estado Civil</span>
                          {isEditingProfile ? (
                            <select
                              value={editableStudent.estado_civil || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, estado_civil: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            >
                              <option value="">Seleccione</option>
                              <option value="Soltero">Soltero</option>
                              <option value="Casado">Casado</option>
                              <option value="Divorciado">Divorciado</option>
                              <option value="Viudo">Viudo</option>
                            </select>
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.estado_civil || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Fecha de Nacimiento</span>
                          {isEditingProfile ? (
                            <input
                              type="date"
                              value={editableStudent.fecha_nacimiento ? new Date(editableStudent.fecha_nacimiento).toISOString().split('T')[0] : ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, fecha_nacimiento: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>
                              {singleStudent.fecha_nacimiento ? new Date(singleStudent.fecha_nacimiento).toLocaleDateString('es-VE') : 'No registrado'}
                            </div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Lugar de Nacimiento</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.lugar_nacimiento || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, lugar_nacimiento: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.lugar_nacimiento || 'No registrado'}</div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Seccion 2: Contacto y Habitacion */}
                    <div>
                      <h4 style={{ margin: '0 0 12px', fontSize: '14px', color: '#60a5fa', borderBottom: '1px solid rgba(96,165,250,0.2)', paddingBottom: '6px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                        Contacto y Dirección
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Correo Electrónico</span>
                          {isEditingProfile ? (
                            <input
                              type="email"
                              value={editableStudent.email || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, email: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.email || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Teléfono Celular</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.telefono_celular || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, telefono_celular: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.telefono_celular || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Teléfono de Habitación</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.telefono_habitacion || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, telefono_habitacion: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.telefono_habitacion || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Fax</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.fax || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, fax: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.fax || 'No registrado'}</div>
                          )}
                        </div>
                        <div style={{ gridColumn: 'span 2' }}>
                          <span style={detailLabelStyle}>Dirección de Habitación</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.direccion || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, direccion: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.direccion || 'No registrado'}</div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Seccion 3: Ocupacional / Laboral */}
                    <div>
                      <h4 style={{ margin: '0 0 12px', fontSize: '14px', color: '#60a5fa', borderBottom: '1px solid rgba(96,165,250,0.2)', paddingBottom: '6px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                        Información Académica y Ocupacional
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Grado de Instrucción</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.grado_instruccion || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, grado_instruccion: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.grado_instruccion || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Profesión u Oficio</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.profesion_oficio || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, profesion_oficio: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.profesion_oficio || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Institución</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.institucion || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, institucion: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.institucion || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Empleado en</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.empleado_en || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, empleado_en: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.empleado_en || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Cargo que Desempeña</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.cargo_desempena || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, cargo_desempena: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.cargo_desempena || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Sueldo o Salario</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.sueldo_salario || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, sueldo_salario: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.sueldo_salario || 'No registrado'}</div>
                          )}
                        </div>
                        <div style={{ gridColumn: 'span 2' }}>
                          <span style={detailLabelStyle}>Dirección y Teléfono del Trabajo</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.direccion_telefono || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, direccion_telefono: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.direccion_telefono || 'No registrado'}</div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Seccion 4: Datos del Conyuge */}
                    <div>
                      <h4 style={{ margin: '0 0 12px', fontSize: '14px', color: '#60a5fa', borderBottom: '1px solid rgba(96,165,250,0.2)', paddingBottom: '6px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                        Información del Cónyuge
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Cédula Cónyuge</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.cid_conyuge || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, cid_conyuge: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.cid_conyuge || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Nacionalidad Cónyuge</span>
                          {isEditingProfile ? (
                            <select
                              value={editableStudent.nacionalidad_conyuge || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, nacionalidad_conyuge: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            >
                              <option value="">Seleccione</option>
                              <option value="Venezolana">Venezolana</option>
                              <option value="Extranjera">Extranjera</option>
                            </select>
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.nacionalidad_conyuge || 'No registrado'}</div>
                          )}
                        </div>
                        <div style={{ gridColumn: 'span 2' }}>
                          <span style={detailLabelStyle}>Nombre del Cónyuge</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.apellidos_nombres_conyuge || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, apellidos_nombres_conyuge: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.apellidos_nombres_conyuge || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Fecha Nacimiento Cónyuge</span>
                          {isEditingProfile ? (
                            <input
                              type="date"
                              value={editableStudent.fecha_nacimiento_conyuge ? new Date(editableStudent.fecha_nacimiento_conyuge).toISOString().split('T')[0] : ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, fecha_nacimiento_conyuge: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>
                              {singleStudent.fecha_nacimiento_conyuge ? new Date(singleStudent.fecha_nacimiento_conyuge).toLocaleDateString('es-VE') : 'No registrado'}
                            </div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Profesión u Ocupación</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.profesion_ocupacion || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, profesion_ocupacion: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.profesion_ocupacion || 'No registrado'}</div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Seccion 5: Entorno Familiar y Vivienda */}
                    <div>
                      <h4 style={{ margin: '0 0 12px', fontSize: '14px', color: '#60a5fa', borderBottom: '1px solid rgba(96,165,250,0.2)', paddingBottom: '6px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                        Entorno Familiar y Vivienda
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Nro. Grupo Familiar</span>
                          {isEditingProfile ? (
                            <input
                              type="number"
                              value={editableStudent.nro_grupo_familiar !== null && editableStudent.nro_grupo_familiar !== undefined ? editableStudent.nro_grupo_familiar : ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, nro_grupo_familiar: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.nro_grupo_familiar || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Ingreso Familiar</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.ingreso_familiar || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, ingreso_familiar: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.ingreso_familiar || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Tipo de Vivienda</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.tipo_vivienda || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, tipo_vivienda: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.tipo_vivienda || 'No registrado'}</div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Seccion 6: Vehiculo y Varios */}
                    <div>
                      <h4 style={{ margin: '0 0 12px', fontSize: '14px', color: '#60a5fa', borderBottom: '1px solid rgba(96,165,250,0.2)', paddingBottom: '6px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.5px' }}>
                        Datos del Vehículo y Varios
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Vehículo Propio</span>
                          {isEditingProfile ? (
                            <select
                              value={editableStudent.vehiculo || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, vehiculo: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            >
                              <option value="">Seleccione</option>
                              <option value="si">Sí</option>
                              <option value="no">No</option>
                            </select>
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.vehiculo || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Marca Vehículo</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.marca_vehiculo || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, marca_vehiculo: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.marca_vehiculo || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Modelo Vehículo</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.modelo_vehiculo || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, modelo_vehiculo: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.modelo_vehiculo || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Año Vehículo</span>
                          {isEditingProfile ? (
                            <input
                              type="number"
                              value={editableStudent.ano !== null && editableStudent.ano !== undefined ? editableStudent.ano : ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, ano: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.ano || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Licencia Nro</span>
                          {isEditingProfile ? (
                            <input
                              type="text"
                              value={editableStudent.licencia_nro || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, licencia_nro: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.licencia_nro || 'No registrado'}</div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Documentos Originales</span>
                          {isEditingProfile ? (
                            <select
                              value={editableStudent.original || ''}
                              onChange={(e) => setEditableStudent({ ...editableStudent, original: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            >
                              <option value="">Seleccione</option>
                              <option value="si">Sí</option>
                              <option value="no">No</option>
                            </select>
                          ) : (
                            <div style={detailValueStyle}>{singleStudent.original || 'No registrado'}</div>
                          )}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div style={{ display: 'flex', justifyContent: 'flex-end', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '30px', gap: '12px' }}>
                    {isEditingProfile ? (
                      <>
                        <button onClick={() => { setIsEditingProfile(false); setEditableStudent({ ...singleStudent }); }} style={btnStyleSecondary}>
                          Cancelar
                        </button>
                        <button onClick={handleSaveProfileChanges} style={btnStylePrimary}>
                          {loading ? 'Guardando...' : 'Guardar Cambios'}
                        </button>
                      </>
                    ) : (
                      <>
                        <button onClick={() => setShowFullDataModal(false)} style={btnStyleSecondary}>
                          Cerrar Ficha
                        </button>
                        {profile.role <= 3 && (
                          <button onClick={() => setIsEditingProfile(true)} style={btnStylePrimary}>
                            ✏️ Editar Ficha
                          </button>
                        )}
                      </>
                    )}
                  </div>
                </div>
              </div>
            )}

            {/* Program Detail Modal */}
            {showProgramDetailModal && selectedProgram && (
              <div style={modalBackdropStyle}>
                <div style={{ ...modalContentStyle, maxWidth: '850px', maxHeight: '85vh', overflowY: 'auto' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                    <div>
                      <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 800, color: '#a78bfa' }}>
                        🎓 Programa de Postgrado
                      </h3>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Detalles de Oferta de Estudios y Pensum
                      </span>
                    </div>
                    <button
                      onClick={() => setShowProgramDetailModal(false)}
                      style={{
                        background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                        width: '36px', height: '36px', display: 'flex', alignItems: 'center',
                        justifyContent: 'center', cursor: 'pointer', color: '#fff', fontSize: '16px'
                      }}
                    >
                      ✕
                    </button>
                  </div>

                  <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(300px, 1fr))', gap: '30px', alignItems: 'flex-start' }}>
                    {/* Left Column: Metadata */}
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                      <div>
                        <span style={detailLabelStyle}>Título a Otorgar</span>
                        <div style={{ ...detailValueStyle, fontSize: '15px', lineHeight: '1.4' }}>
                          {selectedProgram.titulo_a_otorgar || 'No registrado'}
                        </div>
                      </div>

                      <div>
                        <span style={detailLabelStyle}>Mención / Especialidad</span>
                        <div style={detailValueStyle}>
                          {selectedProgram.mencion_especialidad || 'No registrada'}
                        </div>
                      </div>

                      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Código de Programa</span>
                          <div style={detailValueStyle}>
                            {selectedProgram.codopest || 'No registrado'}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Tipo de Postgrado</span>
                          <div style={detailValueStyle}>
                            {selectedProgram.tipo || 'No registrado'}
                          </div>
                        </div>
                      </div>

                      <div>
                        <span style={detailLabelStyle}>Créditos Requeridos</span>
                        <div style={detailValueStyle}>
                          {selectedProgram.creditos !== null && selectedProgram.creditos !== undefined ? `${selectedProgram.creditos} U.C.` : 'No registrados'}
                        </div>
                      </div>

                      <div style={{ display: 'flex', gap: '12px', marginTop: '20px' }}>
                        <button onClick={() => setShowProgramDetailModal(false)} style={{ ...btnStyleSecondary, flex: 1, padding: '10px 16px' }}>
                          Cerrar
                        </button>
                        <button
                          onClick={() => {
                            handleDownloadPensumPdf(selectedProgram.codsede, selectedProgram.codopest, selectedProgram.mencion_especialidad || selectedProgram.titulo_a_otorgar);
                          }}
                          style={{ ...btnStylePrimary, flex: 1.5, display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px', padding: '10px 16px' }}
                        >
                          🖨️ Imprimir Pensum
                        </button>
                      </div>
                    </div>

                    {/* Right Column: Complete Pensum list */}
                    <div style={{
                      background: 'rgba(255,255,255,0.01)',
                      border: '1px solid rgba(255,255,255,0.05)',
                      borderRadius: '16px',
                      padding: '20px',
                      maxHeight: '55vh',
                      overflowY: 'auto'
                    }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px', borderBottom: '1px solid rgba(96,165,250,0.15)', paddingBottom: '8px' }}>
                        <h4 style={{ margin: 0, fontSize: '15px', color: '#60a5fa', fontWeight: 700 }}>
                          📚 Plan de Estudios (Asignaturas)
                        </h4>
                        {profile.role <= 2 && (
                          <button
                            onClick={() => setShowCreateSubjectModal(true)}
                            style={{ ...btnStylePrimary, padding: '6px 12px', fontSize: '12px', borderRadius: '8px' }}
                          >
                            ➕ Agregar Asignatura
                          </button>
                        )}
                      </div>

                      {selectedProgramPensum.length === 0 ? (
                        <div style={{ textAlign: 'center', color: 'rgba(255,255,255,0.3)', padding: '20px', fontSize: '13px' }}>
                          Cargando asignaturas del pensum...
                        </div>
                      ) : (
                        (() => {
                          // Group by period
                          const periodsMap: { [key: string]: any[] } = {};
                          selectedProgramPensum.forEach((a) => {
                            const pKey = a.periodos !== null ? `Período ${a.periodos}` : 'Sin Período Definido';
                            if (!periodsMap[pKey]) periodsMap[pKey] = [];
                            periodsMap[pKey].push(a);
                          });

                          const sortedPeriods = Object.keys(periodsMap).sort((a, b) => {
                            if (a.includes('Definido')) return 1;
                            if (b.includes('Definido')) return -1;
                            const numA = parseInt(a.replace(/[^0-9]/g, ''), 10);
                            const numB = parseInt(b.replace(/[^0-9]/g, ''), 10);
                            return numA - numB;
                          });

                          return (
                            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                              {sortedPeriods.map((period) => (
                                <div key={period}>
                                  <div style={{ fontSize: '12px', fontWeight: 800, color: '#a78bfa', textTransform: 'uppercase', letterSpacing: '0.5px', marginBottom: '8px' }}>
                                    {period}
                                  </div>
                                  <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                                    {periodsMap[period].map((a) => (
                                      <div key={a.codasig} style={{
                                        background: 'rgba(255,255,255,0.02)',
                                        border: '1px solid rgba(255,255,255,0.04)',
                                        padding: '10px 12px',
                                        borderRadius: '8px',
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        gap: '12px'
                                      }}>
                                        <div style={{ flex: 1 }}>
                                          <div style={{ fontSize: '13.5px', fontWeight: 600, color: 'rgba(255,255,255,0.9)' }}>
                                            {a.asignatura}
                                          </div>
                                          <div style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)', marginTop: '2px', display: 'flex', flexWrap: 'wrap', gap: '12px' }}>
                                            <span>Código: {a.codasig_imp || a.codasig}</span>
                                            {a.prelaciones && a.prelaciones.length > 0 && (
                                              <span style={{ color: '#fca5a5', fontWeight: 600 }}>
                                                ⚠️ Prela: {a.prelaciones.join(', ')}
                                              </span>
                                            )}
                                          </div>
                                        </div>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                                          <div style={{
                                            fontSize: '11px',
                                            background: 'rgba(255,255,255,0.06)',
                                            color: 'rgba(255,255,255,0.7)',
                                            padding: '3px 6px',
                                            borderRadius: '6px',
                                            fontWeight: 600,
                                            whiteSpace: 'nowrap'
                                          }}>
                                            {a.creditos} U.C.
                                          </div>
                                          {profile.role <= 2 && (
                                            <div style={{ display: 'flex', gap: '4px' }}>
                                              <button
                                                onClick={() => {
                                                  setSelectedSubjectForEdit({
                                                    ...a,
                                                    prelacionesRaw: (a.prelaciones || []).join(', ')
                                                  });
                                                  setShowEditSubjectModal(true);
                                                }}
                                                title="Editar asignatura"
                                                style={{ background: 'rgba(255,255,255,0.08)', border: 'none', color: '#a78bfa', padding: '4px 6px', borderRadius: '4px', cursor: 'pointer', fontSize: '11px' }}
                                              >
                                                ✏️
                                              </button>
                                              <button
                                                onClick={() => handleDeleteSubject(a.codsede, a.codopest, a.codasig, a.asignatura)}
                                                title="Eliminar asignatura"
                                                style={{ background: 'rgba(239,68,68,0.1)', border: 'none', color: '#f87171', padding: '4px 6px', borderRadius: '4px', cursor: 'pointer', fontSize: '11px' }}
                                              >
                                                ✕
                                              </button>
                                            </div>
                                          )}
                                        </div>
                                      </div>
                                    ))}
                                  </div>
                                </div>
                              ))}
                            </div>
                          );
                        })()
                      )}
                    </div>
                  </div>
                  
                                    {/* MODAL CREAR ASIGNATURA */}
                  {showCreateSubjectModal && (
                    <div style={modalBackdropStyle}>
                      <div style={{ ...modalContentStyle, maxWidth: '550px', zIndex: 1000 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                          <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                            ➕ Agregar Asignatura al Pensum
                          </h3>
                          <button onClick={() => setShowCreateSubjectModal(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '18px', cursor: 'pointer' }}>✕</button>
                        </div>
                        <form onSubmit={handleCreateSubject} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                            <div>
                              <label style={labelStyle}>Código de Materia</label>
                              <input
                                type="text" required placeholder="Ej: PP-101"
                                value={newSubjectAccount.codasig}
                                onChange={(e) => setNewSubjectAccount({ ...newSubjectAccount, codasig: e.target.value })}
                                style={inputStyle}
                              />
                            </div>
                            <div>
                              <label style={labelStyle}>Código Imprimible</label>
                              <input
                                type="text" required placeholder="Ej: PP101"
                                value={newSubjectAccount.codasig_imp}
                                onChange={(e) => setNewSubjectAccount({ ...newSubjectAccount, codasig_imp: e.target.value })}
                                style={inputStyle}
                              />
                            </div>
                          </div>

                          <div>
                            <label style={labelStyle}>Nombre de la Asignatura</label>
                            <input
                              type="text" required placeholder="Ej: Metodología de la Investigación I"
                              value={newSubjectAccount.asignatura}
                              onChange={(e) => setNewSubjectAccount({ ...newSubjectAccount, asignatura: e.target.value })}
                              style={inputStyle}
                            />
                          </div>

                          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                            <div>
                              <label style={labelStyle}>Unidades de Crédito</label>
                              <input
                                type="number" required min={0}
                                value={newSubjectAccount.creditos}
                                onChange={(e) => setNewSubjectAccount({ ...newSubjectAccount, creditos: Number(e.target.value) })}
                                style={inputStyle}
                              />
                            </div>
                            <div>
                              <label style={labelStyle}>Período Académico</label>
                              <input
                                type="number" required min={1}
                                value={newSubjectAccount.periodos}
                                onChange={(e) => setNewSubjectAccount({ ...newSubjectAccount, periodos: Number(e.target.value) })}
                                style={inputStyle}
                              />
                            </div>
                          </div>

                          <div>
                            <label style={labelStyle}>Prelaciones (Códigos separados por coma)</label>
                            <input
                              type="text" placeholder="Ej: PP-100, PP-99 (Vacío si no prela)"
                              value={newSubjectAccount.prelacionesRaw}
                              onChange={(e) => setNewSubjectAccount({ ...newSubjectAccount, prelacionesRaw: e.target.value })}
                              style={inputStyle}
                            />
                          </div>

                          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                            <button type="button" onClick={() => setShowCreateSubjectModal(false)} style={btnStyleSecondary}>Cancelar</button>
                            <button type="submit" style={btnStylePrimary}>{loading ? 'Guardando...' : 'Agregar Asignatura'}</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  )}

                  {/* MODAL EDITAR ASIGNATURA */}
                  {showEditSubjectModal && selectedSubjectForEdit && (
                    <div style={modalBackdropStyle}>
                      <div style={{ ...modalContentStyle, maxWidth: '550px', zIndex: 1000 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                          <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                            ✏️ Editar Asignatura: {selectedSubjectForEdit.codasig}
                          </h3>
                          <button onClick={() => setShowEditSubjectModal(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '18px', cursor: 'pointer' }}>✕</button>
                        </div>
                        <form onSubmit={handleUpdateSubject} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                            <div>
                              <label style={labelStyle}>Código (No editable)</label>
                              <input type="text" disabled value={selectedSubjectForEdit.codasig} style={{ ...inputStyle, opacity: 0.5 }} />
                            </div>
                            <div>
                              <label style={labelStyle}>Código Imprimible</label>
                              <input
                                type="text" required
                                value={selectedSubjectForEdit.codasig_imp}
                                onChange={(e) => setSelectedSubjectForEdit({ ...selectedSubjectForEdit, codasig_imp: e.target.value })}
                                style={inputStyle}
                              />
                            </div>
                          </div>

                          <div>
                            <label style={labelStyle}>Nombre de la Asignatura</label>
                            <input
                              type="text" required
                              value={selectedSubjectForEdit.asignatura}
                              onChange={(e) => setSelectedSubjectForEdit({ ...selectedSubjectForEdit, asignatura: e.target.value })}
                              style={inputStyle}
                            />
                          </div>

                          <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                            <div>
                              <label style={labelStyle}>Unidades de Crédito</label>
                              <input
                                type="number" required min={0}
                                value={selectedSubjectForEdit.creditos}
                                onChange={(e) => setSelectedSubjectForEdit({ ...selectedSubjectForEdit, creditos: Number(e.target.value) })}
                                style={inputStyle}
                              />
                            </div>
                            <div>
                              <label style={labelStyle}>Período Académico</label>
                              <input
                                type="number" required min={1}
                                value={selectedSubjectForEdit.periodos}
                                onChange={(e) => setSelectedSubjectForEdit({ ...selectedSubjectForEdit, periodos: Number(e.target.value) })}
                                style={inputStyle}
                              />
                            </div>
                          </div>

                          <div>
                            <label style={labelStyle}>Prelaciones (Códigos separados por coma)</label>
                            <input
                              type="text" placeholder="Ej: PP-100, PP-99"
                              value={selectedSubjectForEdit.prelacionesRaw || ''}
                              onChange={(e) => setSelectedSubjectForEdit({ ...selectedSubjectForEdit, prelacionesRaw: e.target.value })}
                              style={inputStyle}
                            />
                          </div>

                          <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                            <button type="button" onClick={() => setShowEditSubjectModal(false)} style={btnStyleSecondary}>Cancelar</button>
                            <button type="submit" style={btnStylePrimary}>{loading ? 'Guardando...' : 'Guardar Cambios'}</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  )}

                </div>
              </div>
            )}

            {/* Cohorte Detail Modal */}
            {showCohorteDetailModal && selectedCohorte && selectedProgramFilter && (
              <div style={modalBackdropStyle}>
                <div style={{ ...modalContentStyle, maxWidth: '850px', maxHeight: '85vh', overflowY: 'auto' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                    <div>
                      <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 800, color: '#a78bfa' }}>
                        🎓 Cohorte: {selectedCohorte.codcohorte}
                      </h3>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Información del Postgrado y Actas de Evaluación
                      </span>
                    </div>
                    <button
                      onClick={() => setShowCohorteDetailModal(false)}
                      style={{
                        background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                        width: '36px', height: '36px', display: 'flex', alignItems: 'center',
                        justifyContent: 'center', cursor: 'pointer', color: '#fff', fontSize: '16px'
                      }}
                    >
                      ✕
                    </button>
                  </div>

                  <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    {/* Sección 1: Información del Postgrado */}
                    <div>
                      <h4 style={{ margin: '0 0 16px', fontSize: '15px', color: '#60a5fa', fontWeight: 700, borderBottom: '1px solid rgba(96,165,250,0.15)', paddingBottom: '8px' }}>
                        📖 Información del Programa de Estudios y Cohorte
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Título a Otorgar</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedProgramFilter.titulo_a_otorgar || 'No registrado'}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Mención / Especialidad</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedProgramFilter.mencion_especialidad || 'No registrada'}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Tipo / Código OPEST</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedProgramFilter.tipo} / {selectedCohorte.codopest}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Sede de la Cohorte</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedCohorte.codsede}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Periodo Lectivo</span>
                          {isEditingCohorte ? (
                            <input
                              type="text"
                              value={editableCohorte.periodo_lectivo || ''}
                              onChange={(e) => setEditableCohorte({ ...editableCohorte, periodo_lectivo: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                              {selectedCohorte.periodo_lectivo}
                            </div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Fecha de Inicio</span>
                          {isEditingCohorte ? (
                            <input
                              type="date"
                              value={editableCohorte.fecha_inicio || ''}
                              onChange={(e) => setEditableCohorte({ ...editableCohorte, fecha_inicio: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                              {selectedCohorte.fecha_inicio ? new Date(selectedCohorte.fecha_inicio).toLocaleDateString('es-VE') : 'No registrada'}
                            </div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Sección 2: Actas de Evaluación Asociadas */}
                    <div>
                      <h4 style={{ margin: '0 0 16px', fontSize: '15px', color: '#60a5fa', fontWeight: 700, borderBottom: '1px solid rgba(96,165,250,0.15)', paddingBottom: '8px' }}>
                        📝 Actas de Evaluación Asociadas
                      </h4>

                      {loadingCohorteActas ? (
                        <div style={{ textAlign: 'center', color: 'rgba(255,255,255,0.4)', padding: '30px' }}>
                          Cargando actas de la cohorte...
                        </div>
                      ) : cohorteActas.length === 0 ? (
                        <div style={{ textAlign: 'center', color: 'rgba(255,255,255,0.3)', padding: '30px', background: 'rgba(255,255,255,0.01)', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.04)', display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '12px' }}>
                          <span>No se encontraron actas de evaluación registradas para esta cohorte.</span>
                          {profile.role <= 2 && (
                            <button onClick={handleGenerateActaFromCohorte} style={{ ...btnStylePrimary, background: '#10b981', padding: '8px 16px', fontSize: '12.5px' }}>
                              📝 Generar Acta
                            </button>
                          )}
                        </div>
                      ) : (
                        <div style={{ overflowX: 'auto', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', border: '1px solid rgba(255,255,255,0.05)' }}>
                          <table style={tableStyle}>
                            <thead>
                              <tr>
                                <th style={thStyle}>Acta</th>
                                <th style={thStyle}>Profesor</th>
                                <th style={thStyle}>Asignatura</th>
                                <th style={thStyle}>Período</th>
                                <th style={thStyle}>Fecha Aprobación</th>
                              </tr>
                            </thead>
                            <tbody>
                              {cohorteActas.map((a) => (
                                <tr
                                  key={`${a.codcohorte}-${a.codasig}-${a.codacta}`}
                                  onClick={() => {
                                    setSelectedActaDetail(a);
                                    setEditableActa({
                                      ...a,
                                      fecha_aprobacion: a.fecha_aprobacion ? a.fecha_aprobacion.substring(0, 10) : ''
                                    });
                                    setIsEditingActa(false);
                                    setActaNotasDetail([]);
                                    setShowActaDetailModal(true);
                                    loadActaNotasDetail(a.codacta);
                                  }}
                                  style={{ ...trStyle, cursor: 'pointer' }}
                                  onMouseEnter={(e) => {
                                    e.currentTarget.style.background = 'rgba(255,255,255,0.03)';
                                  }}
                                  onMouseLeave={(e) => {
                                    e.currentTarget.style.background = 'transparent';
                                  }}
                                >
                                  <td style={{ ...tdStyle, fontWeight: 700, color: '#a78bfa' }}>{a.codacta}</td>
                                  <td style={tdStyle}>{a.profesor}</td>
                                  <td style={tdStyle}>{a.asignatura_nombre} <span style={{ fontSize: '11px', color: 'rgba(255,255,255,0.4)' }}>({a.codasig})</span></td>
                                  <td style={{ ...tdStyle, textAlign: 'center' }}>
                                    {a.periodo !== null && a.periodo !== undefined ? `P-${a.periodo}` : '-'}
                                  </td>
                                  <td style={tdStyle}>
                                    {a.fecha_aprobacion ? (
                                      new Date(a.fecha_aprobacion).toLocaleDateString('es-VE')
                                    ) : (
                                      <span style={{ color: 'rgba(255,255,255,0.3)', fontStyle: 'italic' }}>Pendiente</span>
                                    )}
                                  </td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>
                      )}
                    </div>
                  </div>

                   <div style={{ display: 'flex', justifyContent: 'flex-end', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '30px', gap: '12px' }}>
                    {isEditingCohorte ? (
                      <>
                        <button onClick={() => { setIsEditingCohorte(false); setEditableCohorte({ ...selectedCohorte, fecha_inicio: selectedCohorte.fecha_inicio ? selectedCohorte.fecha_inicio.substring(0, 10) : '' }); }} style={btnStyleSecondary}>
                          Cancelar
                        </button>
                        <button onClick={handleSaveCohorteChanges} style={btnStylePrimary}>
                          {loading ? 'Guardando...' : 'Guardar Cambios'}
                        </button>
                      </>
                    ) : (
                      <>
                        <button onClick={() => setShowCohorteDetailModal(false)} style={btnStyleSecondary}>
                          Cerrar
                        </button>
                        {profile.role <= 2 && (
                          <>
                            <button onClick={handleGenerateActaFromCohorte} style={{ ...btnStylePrimary, background: '#10b981' }}>
                              📝 Generar Acta
                            </button>
                            <button onClick={() => setIsEditingCohorte(true)} style={btnStylePrimary}>
                              ✏️ Editar Cohorte
                            </button>
                          </>
                        )}
                      </>
                    )}
                  </div>
                </div>
              </div>
            )}

            {/* Acta Detail Modal */}
            {showActaDetailModal && selectedActaDetail && (
              <div style={{ ...modalBackdropStyle, zIndex: 1100 }}>
                <div style={{ ...modalContentStyle, maxWidth: '850px', maxHeight: '85vh', overflowY: 'auto' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                    <div>
                      <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 800, color: '#a78bfa' }}>
                        📋 Acta de Evaluación: {selectedActaDetail.codacta}
                      </h3>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Información Detallada y Calificaciones de Estudiantes
                      </span>
                    </div>
                    <button
                      onClick={() => setShowActaDetailModal(false)}
                      style={{
                        background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                        width: '36px', height: '36px', display: 'flex', alignItems: 'center',
                        justifyContent: 'center', cursor: 'pointer', color: '#fff', fontSize: '16px'
                      }}
                    >
                      ✕
                    </button>
                  </div>

                  <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    {/* Sección 1: Información del Acta */}
                    <div>
                      <h4 style={{ margin: '0 0 16px', fontSize: '15px', color: '#60a5fa', fontWeight: 700, borderBottom: '1px solid rgba(96,165,250,0.15)', paddingBottom: '8px' }}>
                        📖 Información del Acta Seleccionada
                      </h4>
                      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
                        <div>
                          <span style={detailLabelStyle}>Asignatura</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedActaDetail.asignatura_nombre || 'Desconocida'}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Código de Asignatura</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedActaDetail.codasig || 'No registrado'}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Período / Créditos</span>
                          <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                            {selectedActaDetail.periodo !== null && selectedActaDetail.periodo !== undefined ? `P-${selectedActaDetail.periodo}` : 'No definido'} / {selectedActaDetail.creditos !== null && selectedActaDetail.creditos !== undefined ? `${selectedActaDetail.creditos} U.C.` : 'No registrado'}
                          </div>
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Profesor Asignado</span>
                          {isEditingActa ? (
                            <select
                              value={editableActa.cedula_profesor || ''}
                              onChange={(e) => setEditableActa({ ...editableActa, cedula_profesor: e.target.value ? Number(e.target.value) : null })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px', background: '#120f30' }}
                            >
                              <option value="">Seleccione un profesor</option>
                              {modalProfesoresList.map(p => (
                                <option key={p.cedula_profesor} value={p.cedula_profesor}>
                                  {p.apellidos_nombres} (C.I. {p.cedula_profesor})
                                </option>
                              ))}
                            </select>
                          ) : (
                            <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                              {selectedActaDetail.profesor}
                            </div>
                          )}
                        </div>
                        <div>
                          <span style={detailLabelStyle}>Fecha de Aprobación</span>
                          {isEditingActa ? (
                            <input
                              type="date"
                              value={editableActa.fecha_aprobacion || ''}
                              onChange={(e) => setEditableActa({ ...editableActa, fecha_aprobacion: e.target.value })}
                              style={{ ...inputStyle, padding: '8px 12px', marginTop: '4px' }}
                            />
                          ) : (
                            <div style={{ ...detailValueStyle, fontSize: '14.5px' }}>
                              {selectedActaDetail.fecha_aprobacion ? new Date(selectedActaDetail.fecha_aprobacion).toLocaleDateString('es-VE') : 'Pendiente'}
                            </div>
                          )}
                        </div>
                      </div>
                    </div>

                    {/* Sección 2: Listado de Calificaciones */}
                    <div>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px', borderBottom: '1px solid rgba(96,165,250,0.15)', paddingBottom: '8px' }}>
                        <h4 style={{ margin: 0, fontSize: '15px', color: '#60a5fa', fontWeight: 700 }}>
                          📊 Listado de Calificaciones
                        </h4>
                        {profile.role <= 2 && (
                          <button
                            onClick={() => {
                              setShowAddNota(true);
                              setNewNota({ codacta: selectedActaDetail.codacta, cedula: '', calificacion: '' });
                            }}
                            style={{ ...btnStylePrimary, padding: '8px 16px', fontSize: '12.5px' }}
                          >
                            + Cargar Calificación
                          </button>
                        )}
                      </div>

                      {loadingActaNotas ? (
                        <div style={{ textAlign: 'center', color: 'rgba(255,255,255,0.4)', padding: '30px' }}>
                          Cargando calificaciones...
                        </div>
                      ) : actaNotasDetail.length === 0 ? (
                        <div style={{ textAlign: 'center', color: 'rgba(255,255,255,0.3)', padding: '30px', background: 'rgba(255,255,255,0.01)', borderRadius: '12px', border: '1px solid rgba(255,255,255,0.04)' }}>
                          No se encontraron calificaciones registradas para esta acta.
                        </div>
                      ) : (
                        <div style={{ overflowX: 'auto', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', border: '1px solid rgba(255,255,255,0.05)' }}>
                          <table style={tableStyle}>
                            <thead>
                              <tr>
                                <th style={{ ...thStyle, width: '60px', textAlign: 'center' }}>Número</th>
                                <th style={thStyle}>Cédula</th>
                                <th style={thStyle}>Apellidos</th>
                                <th style={thStyle}>Nombres</th>
                                <th style={{ ...thStyle, textAlign: 'center' }}>Notas</th>
                                <th style={{ ...thStyle, textAlign: 'center' }}>Equivalencia</th>
                              </tr>
                            </thead>
                            <tbody>
                              {actaNotasDetail.map((n, idx) => (
                                <tr key={`${n.codacta}-${n.cedula}`} style={trStyle}>
                                  <td style={{ ...tdStyle, textAlign: 'center', color: 'rgba(255,255,255,0.4)', fontWeight: 600 }}>{idx + 1}</td>
                                  <td style={{ ...tdStyle, fontWeight: 700 }}>{n.cedula}</td>
                                  <td style={tdStyle}>{n.apellidos}</td>
                                  <td style={tdStyle}>{n.nombres}</td>
                                  <td style={{ ...tdStyle, textAlign: 'center', fontWeight: 800, color: getCalificacionColor(n.calificacion) }}>
                                    {formatCalificacion(n.calificacion)}
                                  </td>
                                  <td style={{ ...tdStyle, textAlign: 'center', color: '#60a5fa', fontWeight: 600 }}>{n.codeq || '-'}</td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>
                      )}
                    </div>
                  </div>

                  <div style={{ display: 'flex', justifyContent: 'flex-end', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '30px', gap: '12px' }}>
                    {isEditingActa ? (
                      <>
                        <button onClick={() => { setIsEditingActa(false); setEditableActa({ ...selectedActaDetail, fecha_aprobacion: selectedActaDetail.fecha_aprobacion ? selectedActaDetail.fecha_aprobacion.substring(0, 10) : '' }); }} style={btnStyleSecondary}>
                          Cancelar
                        </button>
                        <button onClick={handleSaveActaChanges} style={btnStylePrimary}>
                          {loading ? 'Guardando...' : 'Guardar Cambios'}
                        </button>
                      </>
                    ) : (
                      <>
                        <button onClick={() => setShowActaDetailModal(false)} style={btnStyleSecondary}>
                          Regresar
                        </button>
                        {profile.role <= 2 && (
                          <button onClick={startEditingActa} style={btnStylePrimary}>
                            ✏️ Editar Acta
                          </button>
                        )}
                      </>
                    )}
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'estadisticas' && profile.role <= 3 && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 700 }}>
                  📈 Estadísticas SACE
                </h3>
                
                {/* 1. Resumen General */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: '20px' }}>
                  <div style={statCardStyle}>
                    <span style={{ fontSize: '28px' }}>📂</span>
                    <div>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Estudiantes Registrados</div>
                      <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>{stats.expedientes}</div>
                    </div>
                  </div>

                  <div style={statCardStyle}>
                    <span style={{ fontSize: '28px' }}>📅</span>
                    <div>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Cohortes Activas</div>
                      <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>{stats.cohortes}</div>
                    </div>
                  </div>

                  <div style={statCardStyle}>
                    <span style={{ fontSize: '28px' }}>👨‍🏫</span>
                    <div>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Profesores Registrados</div>
                      <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>{stats.profesores}</div>
                    </div>
                  </div>

                  <div style={statCardStyle}>
                    <span style={{ fontSize: '28px' }}>📝</span>
                    <div>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Actas Emitidas</div>
                      <div style={{ fontSize: '28px', fontWeight: 800, marginTop: '4px' }}>{stats.actas}</div>
                    </div>
                  </div>
                </div>

                {/* 2. Distribuciones Analíticas */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(400px, 1fr))', gap: '30px' }}>
                  {/* Distribución por Sede */}
                  <div style={{ ...panelCardStyle, cursor: 'pointer', transition: 'all 0.2s' }} onClick={() => setExpandedChart('sedes')} title="Hacer clic para ampliar este gráfico" className="hover-scale-subtle">
                    <h4 style={{ margin: '0 0 20px', fontSize: '16px', fontWeight: 700, color: '#a78bfa', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                      <span>📍 Distribución de Cohortes por Sede</span>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', fontWeight: 'normal' }}>🔍 Ampliar</span>
                    </h4>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                      {(() => {
                        const counts: Record<string, number> = {};
                        cohortes.forEach((c) => {
                          const city = getCityFromSede(extractSedeFromCohorte(c.codcohorte)) || 'Desconocida';
                          counts[city] = (counts[city] || 0) + 1;
                        });
                        const data = Object.entries(counts).sort((a, b) => b[1] - a[1]);
                        const maxVal = Math.max(...data.map(d => d[1]), 1);
                        
                        const height = 180;
                        const barWidth = 35;
                        const gap = 25;
                        const paddingLeft = 30;
                        const paddingTop = 20;
                        const width = data.length * (barWidth + gap) + paddingLeft + 10;

                        return (
                          <div style={{ overflowX: 'auto', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', padding: '16px 10px', border: '1px solid rgba(255,255,255,0.03)' }}>
                            <svg width="100%" height={height + 50} viewBox={`0 0 ${width} ${height + 50}`} style={{ display: 'block', overflow: 'visible' }}>
                              <defs>
                                <linearGradient id="barGrad" x1="0" y1="100%" x2="0" y2="0%">
                                  <stop offset="0%" stopColor="rgba(99,102,241,0.2)" />
                                  <stop offset="100%" stopColor="#8b5cf6" />
                                </linearGradient>
                              </defs>
                              {[0, 0.25, 0.5, 0.75, 1].map((ratio, i) => {
                                const y = paddingTop + (1 - ratio) * (height - paddingTop);
                                const val = Math.round(ratio * maxVal);
                                return (
                                  <g key={i}>
                                    <line x1={paddingLeft} y1={y} x2={width} y2={y} stroke="rgba(255,255,255,0.05)" strokeDasharray="3,3" />
                                    <text x={paddingLeft - 8} y={y + 4} fill="rgba(255,255,255,0.3)" fontSize="10" textAnchor="end">{val}</text>
                                  </g>
                                );
                              })}
                              {data.map(([city, count], idx) => {
                                const x = paddingLeft + idx * (barWidth + gap) + gap/2;
                                const barHeight = (count / maxVal) * (height - paddingTop);
                                const y = height - barHeight;
                                return (
                                  <g key={city}>
                                    <rect
                                      x={x}
                                      y={y}
                                      width={barWidth}
                                      height={barHeight}
                                      fill="url(#barGrad)"
                                      rx="6"
                                      style={{ transition: 'all 0.3s' }}
                                    />
                                    <text x={x + barWidth/2} y={y - 6} fill="#fff" fontSize="11" fontWeight="700" textAnchor="middle">{count}</text>
                                    <text
                                      x={x + barWidth/2}
                                      y={height + 20}
                                      fill="rgba(255,255,255,0.6)"
                                      fontSize="10"
                                      fontWeight="600"
                                      textAnchor="middle"
                                      transform={`rotate(-15, ${x + barWidth/2}, ${height + 20})`}
                                    >
                                      {city}
                                    </text>
                                  </g>
                                );
                              })}
                            </svg>
                          </div>
                        );
                      })()}
                    </div>
                  </div>

                  {/* Distribución por Programa */}
                  <div style={{ ...panelCardStyle, cursor: 'pointer', transition: 'all 0.2s' }} onClick={() => setExpandedChart('programas')} title="Hacer clic para ampliar este gráfico" className="hover-scale-subtle">
                    <h4 style={{ margin: '0 0 20px', fontSize: '16px', fontWeight: 700, color: '#a78bfa', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                      <span>🎓 Distribución por Programa (Top 5)</span>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', fontWeight: 'normal' }}>🔍 Ampliar</span>
                    </h4>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '14px' }}>
                      {(() => {
                        const counts: Record<string, number> = {};
                        cohortes.forEach((c) => {
                          const prog = c.codopest || 'Desconocido';
                          counts[prog] = (counts[prog] || 0) + 1;
                        });
                        const total = Object.values(counts).reduce((s, c) => s + c, 0) || 1;
                        
                        return Object.entries(counts)
                          .sort((a, b) => b[1] - a[1])
                          .slice(0, 5)
                          .map(([prog, count]) => {
                            const pct = ((count / total) * 100).toFixed(1);
                            const progDetail = programs.find((p) => p.codopest === prog);
                            const label = progDetail ? progDetail.titulo_a_otorgar || progDetail.mencion_especialidad : prog;
                            return (
                              <div key={prog}>
                                <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '13px', marginBottom: '6px' }}>
                                  <span style={{ textOverflow: 'ellipsis', overflow: 'hidden', whiteSpace: 'nowrap', maxWidth: '300px' }} title={label}>
                                    {label}
                                  </span>
                                  <span style={{ color: '#34d399', fontWeight: 600 }}>{count} ({pct}%)</span>
                                </div>
                                <div style={{ height: '8px', background: 'rgba(255,255,255,0.05)', borderRadius: '4px', overflow: 'hidden' }}>
                                  <div style={{ width: `${pct}%`, height: '100%', background: 'linear-gradient(90deg, #10b981, #34d399)', borderRadius: '4px' }} />
                                </div>
                              </div>
                            );
                          });
                      })()}
                    </div>
                  </div>

                  {/* Distribución por Año */}
                  <div style={{ ...panelCardStyle, cursor: 'pointer', transition: 'all 0.2s' }} onClick={() => setExpandedChart('anos')} title="Hacer clic para ampliar este gráfico" className="hover-scale-subtle">
                    <h4 style={{ margin: '0 0 20px', fontSize: '16px', fontWeight: 700, color: '#a78bfa', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                      <span>📅 Historial de Cohortes por Año</span>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', fontWeight: 'normal' }}>🔍 Ampliar</span>
                    </h4>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                      {(() => {
                        const counts: Record<number, number> = {};
                        cohortes.forEach((c) => {
                          let year = 2026;
                          if (c.fecha_inicio) {
                            const d = new Date(c.fecha_inicio);
                            if (!isNaN(d.getTime())) {
                              year = d.getFullYear();
                            }
                          } else {
                            const match = (c.codcohorte || '').match(/\d+/);
                            if (match) {
                              const num = Number(match[0]);
                              if (num >= 80 && num <= 99) year = 1900 + num;
                              else if (num >= 0 && num <= 50) year = 2000 + num;
                              else if (num >= 1980 && num <= 2050) year = num;
                            }
                          }
                          counts[year] = (counts[year] || 0) + 1;
                        });
                        const data = Object.entries(counts).sort((a, b) => Number(a[0]) - Number(b[0]));
                        if (data.length === 0) return <div>No hay datos históricos.</div>;

                        const maxVal = Math.max(...data.map(d => d[1]), 1);
                        
                        const height = 180;
                        const width = 450;
                        const paddingLeft = 30;
                        const paddingRight = 15;
                        const paddingTop = 20;
                        const chartWidth = width - paddingLeft - paddingRight;

                        const points = data.map(([year, count], idx) => {
                          const x = paddingLeft + (idx / Math.max(data.length - 1, 1)) * chartWidth;
                          const y = height - (count / maxVal) * (height - paddingTop);
                          return { x, y, year, count };
                        });

                        const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
                        const areaPath = linePath ? `${linePath} L ${points[points.length - 1].x} ${height} L ${points[0].x} ${height} Z` : '';

                        return (
                          <div style={{ overflowX: 'auto', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', padding: '16px 10px', border: '1px solid rgba(255,255,255,0.03)' }}>
                            <svg width="100%" height={height + 40} viewBox={`0 0 ${width} ${height + 40}`} style={{ display: 'block', overflow: 'visible' }}>
                              <defs>
                                <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                                  <stop offset="0%" stopColor="#3b82f6" stopOpacity="0.4" />
                                  <stop offset="100%" stopColor="#3b82f6" stopOpacity="0.0" />
                                </linearGradient>
                              </defs>
                              {[0, 0.25, 0.5, 0.75, 1].map((ratio, i) => {
                                const y = paddingTop + (1 - ratio) * (height - paddingTop);
                                const val = Math.round(ratio * maxVal);
                                return (
                                  <g key={i}>
                                    <line x1={paddingLeft} y1={y} x2={width - paddingRight} y2={y} stroke="rgba(255,255,255,0.05)" strokeDasharray="3,3" />
                                    <text x={paddingLeft - 8} y={y + 4} fill="rgba(255,255,255,0.3)" fontSize="10" textAnchor="end">{val}</text>
                                  </g>
                                );
                              })}
                              {areaPath && <path d={areaPath} fill="url(#areaGrad)" />}
                              {linePath && <path d={linePath} fill="none" stroke="#60a5fa" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round" />}
                              {points.map((p, idx) => (
                                <g key={p.year}>
                                  <circle cx={p.x} cy={p.y} r="4.5" fill="#fff" stroke="#3b82f6" strokeWidth="2.5" />
                                  <text x={p.x} y={p.y - 8} fill="#fff" fontSize="10" fontWeight="700" textAnchor="middle">{p.count}</text>
                                  <text
                                    x={p.x}
                                    y={height + 14}
                                    fill="rgba(255,255,255,0.6)"
                                    fontSize="9px"
                                    fontWeight="600"
                                    textAnchor="end"
                                    transform={`rotate(-90, ${p.x}, ${height + 14})`}
                                  >
                                    {p.year}
                                  </text>
                                </g>
                              ))}
                            </svg>
                          </div>
                        );
                      })()}
                    </div>
                  </div>
                </div>
{/* 3. Rendimiento y Notas */}
                <div style={panelCardStyle}>
                  <h4 style={{ margin: '0 0 20px', fontSize: '16px', fontWeight: 700, color: '#a78bfa' }}>
                    📈 Rendimiento Académico General (Actas)
                  </h4>
                  <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '20px', textAlign: 'center' }}>
                    <div style={{ padding: '20px', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', border: '1px solid rgba(255,255,255,0.04)' }}>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Asignaturas Registradas en Pensum</div>
                      <div style={{ fontSize: '32px', fontWeight: 800, color: '#3b82f6', marginTop: '10px' }}>
                        {pensum.length}
                      </div>
                    </div>
                    <div style={{ padding: '20px', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', border: '1px solid rgba(255,255,255,0.04)' }}>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Programas de Estudio Activos</div>
                      <div style={{ fontSize: '32px', fontWeight: 800, color: '#a78bfa', marginTop: '10px' }}>
                        {programs.length}
                      </div>
                    </div>
                    <div style={{ padding: '20px', background: 'rgba(255,255,255,0.01)', borderRadius: '16px', border: '1px solid rgba(255,255,255,0.04)' }}>
                      <div style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase' }}>Promedio de Estudiantes / Cohorte</div>
                      <div style={{ fontSize: '32px', fontWeight: 800, color: '#10b981', marginTop: '10px' }}>
                        {stats.cohortes > 0 ? (stats.expedientes / stats.cohortes).toFixed(1) : 0}
                    </div>
                  </div>
                </div>
              </div>

            {/* MODALES DE EXPANSIÓN DE GRÁFICOS (MODULO 13 - UI DETAIL) */}
            {expandedChart && (
              <div style={modalBackdropStyle}>
                <div style={{ ...modalContentStyle, maxWidth: '850px', maxHeight: '85vh', overflowY: 'auto' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                    <div>
                      <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 800, color: '#a78bfa' }}>
                        {expandedChart === 'sedes' && '📍 Distribución de Cohortes por Sede (Detalle)'}
                        {expandedChart === 'programas' && '🎓 Distribución de Cohortes por Programa de Estudio (Detalle)'}
                        {expandedChart === 'anos' && '📅 Historial de Cohortes por Año (Detalle)'}
                      </h3>
                      <span style={{ fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Vista ampliada y desglose tabular en tiempo real
                      </span>
                    </div>
                    <button
                      onClick={() => setExpandedChart(null)}
                      style={{
                        background: 'rgba(255,255,255,0.06)', border: 'none', borderRadius: '50%',
                        width: '36px', height: '36px', display: 'flex', alignItems: 'center',
                        justifyContent: 'center', cursor: 'pointer', color: '#fff', fontSize: '16px'
                      }}
                    >
                      ✕
                    </button>
                  </div>

                  {/* 1. VISTA AMPLIADA DE SEDES */}
                  {expandedChart === 'sedes' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                      {(() => {
                        const counts: Record<string, number> = {};
                        cohortes.forEach((c) => {
                          const city = getCityFromSede(extractSedeFromCohorte(c.codcohorte)) || 'Desconocida';
                          counts[city] = (counts[city] || 0) + 1;
                        });
                        const data = Object.entries(counts).sort((a, b) => b[1] - a[1]);
                        const totalCohortes = data.reduce((s, c) => s + c[1], 0) || 1;
                        const maxVal = Math.max(...data.map(d => d[1]), 1);
                        
                        const height = 240;
                        const barWidth = 45;
                        const gap = 30;
                        const paddingLeft = 35;
                        const paddingTop = 25;
                        const width = data.length * (barWidth + gap) + paddingLeft + 20;

                        return (
                          <>
                            {/* Gráfico Ampliado */}
                            <div style={{ overflowX: 'auto', background: 'rgba(255,255,255,0.01)', borderRadius: '20px', padding: '24px 16px', border: '1px solid rgba(255,255,255,0.04)' }}>
                              <svg width="100%" height={height + 60} viewBox={`0 0 ${width} ${height + 60}`} style={{ display: 'block', overflow: 'visible', margin: '0 auto', maxWidth: '100%' }}>
                                <defs>
                                  <linearGradient id="barGradLarge" x1="0" y1="100%" x2="0" y2="0%">
                                    <stop offset="0%" stopColor="rgba(99,102,241,0.2)" />
                                    <stop offset="100%" stopColor="#a78bfa" />
                                  </linearGradient>
                                </defs>
                                {[0, 0.25, 0.5, 0.75, 1].map((ratio, i) => {
                                  const y = paddingTop + (1 - ratio) * (height - paddingTop);
                                  const val = Math.round(ratio * maxVal);
                                  return (
                                    <g key={i}>
                                      <line x1={paddingLeft} y1={y} x2={width} y2={y} stroke="rgba(255,255,255,0.06)" strokeDasharray="4,4" />
                                      <text x={paddingLeft - 10} y={y + 4} fill="rgba(255,255,255,0.3)" fontSize="11" fontWeight="600" textAnchor="end">{val}</text>
                                    </g>
                                  );
                                })}
                                {data.map(([city, count], idx) => {
                                  const x = paddingLeft + idx * (barWidth + gap) + gap/2;
                                  const barHeight = (count / maxVal) * (height - paddingTop);
                                  const y = height - barHeight;
                                  return (
                                    <g key={city}>
                                      <rect
                                        x={x}
                                        y={y}
                                        width={barWidth}
                                        height={barHeight}
                                        fill="url(#barGradLarge)"
                                        rx="8"
                                      />
                                      <text x={x + barWidth/2} y={y - 8} fill="#fff" fontSize="12" fontWeight="800" textAnchor="middle">{count}</text>
                                      <text
                                        x={x + barWidth/2}
                                        y={height + 22}
                                        fill="rgba(255,255,255,0.7)"
                                        fontSize="11"
                                        fontWeight="600"
                                        textAnchor="middle"
                                        transform={`rotate(-15, ${x + barWidth/2}, ${height + 22})`}
                                      >
                                        {city}
                                      </text>
                                    </g>
                                  );
                                })}
                              </svg>
                            </div>

                            {/* Desglose Tabular */}
                            <div>
                              <h4 style={{ margin: '0 0 12px', fontSize: '15px', color: '#fff', fontWeight: 700 }}>📋 Desglose por Ciudad y Sede</h4>
                              <table style={tableStyle}>
                                <thead>
                                  <tr>
                                    <th style={thStyle}>Sede Académica</th>
                                    <th style={thStyle}>Código Referencial</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Total Cohortes</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Porcentaje</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {data.map(([city, count]) => {
                                    const code = getSedeFromCity(city) || 'N/A';
                                    const pct = ((count / totalCohortes) * 100).toFixed(1);
                                    return (
                                      <tr key={city} style={trStyle}>
                                        <td style={{ ...tdStyle, color: '#fff', fontWeight: 700 }}>{city}</td>
                                        <td style={{ ...tdStyle, color: '#60a5fa', fontWeight: 600 }}>{code}</td>
                                        <td style={{ ...tdStyle, textAlign: 'center', fontWeight: 700 }}>{count}</td>
                                        <td style={{ ...tdStyle, textAlign: 'center', color: '#10b981', fontWeight: 700 }}>{pct}%</td>
                                      </tr>
                                    );
                                  })}
                                </tbody>
                              </table>
                            </div>
                          </>
                        );
                      })()}
                    </div>
                  )}

                  {/* 2. VISTA AMPLIADA DE PROGRAMAS */}
                  {expandedChart === 'programas' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                      {(() => {
                        const counts: Record<string, number> = {};
                        cohortes.forEach((c) => {
                          const prog = c.codopest || 'Desconocido';
                          counts[prog] = (counts[prog] || 0) + 1;
                        });
                        const data = Object.entries(counts).sort((a, b) => b[1] - a[1]);
                        const totalCohortes = data.reduce((s, c) => s + c[1], 0) || 1;

                        return (
                          <>
                            {/* Desglose Tabular de todos los programas con cohortes */}
                            <div>
                              <h4 style={{ margin: '0 0 12px', fontSize: '15px', color: '#fff', fontWeight: 700 }}>📋 Distribución de Cohortes por Programas de Estudio</h4>
                              <table style={tableStyle}>
                                <thead>
                                  <tr>
                                    <th style={thStyle}>Código Prog.</th>
                                    <th style={thStyle}>Título a Otorgar</th>
                                    <th style={thStyle}>Mención / Especialidad</th>
                                    <th style={thStyle}>Tipo</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Total Cohortes</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Porcentaje</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {data.map(([prog, count]) => {
                                    const progDetail = programs.find((p) => p.codopest === prog);
                                    const label = progDetail ? progDetail.titulo_a_otorgar : 'No registrado';
                                    const mencion = progDetail ? progDetail.mencion_especialidad : 'General';
                                    const tipo = progDetail ? progDetail.tipo : 'N/D';
                                    const pct = ((count / totalCohortes) * 100).toFixed(1);
                                    return (
                                      <tr key={prog} style={trStyle}>
                                        <td style={{ ...tdStyle, color: '#a78bfa', fontWeight: 700 }}>{prog}</td>
                                        <td style={{ ...tdStyle, color: '#fff', fontWeight: 600 }}>{label}</td>
                                        <td style={tdStyle}>{mencion}</td>
                                        <td style={tdStyle}>
                                          <span style={{
                                            background: 'rgba(99,102,241,0.1)', color: '#a78bfa',
                                            padding: '3px 6px', borderRadius: '6px', fontSize: '11px', fontWeight: 600
                                          }}>
                                            {tipo}
                                          </span>
                                        </td>
                                        <td style={{ ...tdStyle, textAlign: 'center', fontWeight: 700 }}>{count}</td>
                                        <td style={{ ...tdStyle, textAlign: 'center', color: '#10b981', fontWeight: 700 }}>{pct}%</td>
                                      </tr>
                                    );
                                  })}
                                </tbody>
                              </table>
                            </div>
                          </>
                        );
                      })()}
                    </div>
                  )}

                  {/* 3. VISTA AMPLIADA DE HISTORIAL ANUAL */}
                  {expandedChart === 'anos' && (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                      {(() => {
                        const counts: Record<number, number> = {};
                        cohortes.forEach((c) => {
                          let year = 2026;
                          if (c.fecha_inicio) {
                            const d = new Date(c.fecha_inicio);
                            if (!isNaN(d.getTime())) {
                              year = d.getFullYear();
                            }
                          } else {
                            const match = (c.codcohorte || '').match(/\d+/);
                            if (match) {
                              const num = Number(match[0]);
                              if (num >= 80 && num <= 99) year = 1900 + num;
                              else if (num >= 0 && num <= 50) year = 2000 + num;
                              else if (num >= 1980 && num <= 2050) year = num;
                            }
                          }
                          counts[year] = (counts[year] || 0) + 1;
                        });
                        const data = Object.entries(counts).sort((a, b) => Number(a[0]) - Number(b[0]));
                        const totalCohortes = data.reduce((s, c) => s + c[1], 0) || 1;
                        const maxVal = Math.max(...data.map(d => d[1]), 1);

                        const height = 240;
                        const width = 700;
                        const paddingLeft = 35;
                        const paddingRight = 20;
                        const paddingTop = 25;
                        const chartWidth = width - paddingLeft - paddingRight;

                        const points = data.map(([year, count], idx) => {
                          const x = paddingLeft + (idx / Math.max(data.length - 1, 1)) * chartWidth;
                          const y = height - (count / maxVal) * (height - paddingTop);
                          return { x, y, year, count };
                        });

                        const linePath = points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${p.x} ${p.y}`).join(' ');
                        const areaPath = linePath ? `${linePath} L ${points[points.length - 1].x} ${height} L ${points[0].x} ${height} Z` : '';

                        return (
                          <>
                            {/* Gráfico de Área Ampliado */}
                            <div style={{ overflowX: 'auto', background: 'rgba(255,255,255,0.01)', borderRadius: '20px', padding: '24px 16px', border: '1px solid rgba(255,255,255,0.04)' }}>
                              <svg width="100%" height={height + 50} viewBox={`0 0 ${width} ${height + 50}`} style={{ display: 'block', overflow: 'visible', margin: '0 auto', maxWidth: '100%' }}>
                                <defs>
                                  <linearGradient id="areaGradLarge" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stopColor="#3b82f6" stopOpacity="0.5" />
                                    <stop offset="100%" stopColor="#3b82f6" stopOpacity="0.0" />
                                  </linearGradient>
                                </defs>
                                {[0, 0.25, 0.5, 0.75, 1].map((ratio, i) => {
                                  const y = paddingTop + (1 - ratio) * (height - paddingTop);
                                  const val = Math.round(ratio * maxVal);
                                  return (
                                    <g key={i}>
                                      <line x1={paddingLeft} y1={y} x2={width - paddingRight} y2={y} stroke="rgba(255,255,255,0.06)" strokeDasharray="4,4" />
                                      <text x={paddingLeft - 10} y={y + 4} fill="rgba(255,255,255,0.3)" fontSize="11" fontWeight="600" textAnchor="end">{val}</text>
                                    </g>
                                  );
                                })}
                                {areaPath && <path d={areaPath} fill="url(#areaGradLarge)" />}
                                {linePath && <path d={linePath} fill="none" stroke="#60a5fa" strokeWidth="4.5" strokeLinecap="round" strokeLinejoin="round" />}
                                {points.map((p, idx) => (
                                  <g key={p.year}>
                                    <circle cx={p.x} cy={p.y} r="6" fill="#fff" stroke="#3b82f6" strokeWidth="3" />
                                    <text x={p.x} y={p.y - 10} fill="#fff" fontSize="11" fontWeight="800" textAnchor="middle">{p.count}</text>
                                    <text
                                      x={p.x}
                                      y={height + 15}
                                      fill="rgba(255,255,255,0.7)"
                                      fontSize="10px"
                                      fontWeight="600"
                                      textAnchor="end"
                                      transform={`rotate(-90, ${p.x}, ${height + 15})`}
                                    >
                                      {p.year}
                                    </text>
                                  </g>
                                ))}
                              </svg>
                            </div>

                            {/* Desglose Tabular */}
                            <div>
                              <h4 style={{ margin: '0 0 12px', fontSize: '15px', color: '#fff', fontWeight: 700 }}>📋 Desglose por Año de Cohorte</h4>
                              <table style={tableStyle}>
                                <thead>
                                  <tr>
                                    <th style={thStyle}>Año de Apertura</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Total Cohortes</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Porcentaje de Histórico</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  {data.map(([year, count]) => {
                                    const pct = ((count / totalCohortes) * 100).toFixed(1);
                                    return (
                                      <tr key={year} style={trStyle}>
                                        <td style={{ ...tdStyle, color: '#fff', fontWeight: 700 }}>{year}</td>
                                        <td style={{ ...tdStyle, textAlign: 'center', fontWeight: 700 }}>{count}</td>
                                        <td style={{ ...tdStyle, textAlign: 'center', color: '#10b981', fontWeight: 700 }}>{pct}%</td>
                                      </tr>
                                    );
                                  })}
                                </tbody>
                              </table>
                            </div>
                          </>
                        );
                      })()}
                    </div>
                  )}

                  <div style={{ borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '20px', display: 'flex', justifyContent: 'flex-end' }}>
                    <button onClick={() => setExpandedChart(null)} style={{ ...btnStyleSecondary, padding: '10px 24px' }}>
                      Cerrar Vista Ampliada
                    </button>
                  </div>
                </div>
              </div>
            )}

              </div>
            )}

                        {activeTab === 'configuracion_academica' && profile.role <= 2 && (
              <div style={{ display: 'flex', flexDirection: 'column', gap: '30px' }}>
                <h3 style={{ margin: 0, fontSize: '20px', fontWeight: 700 }}>
                  ⚙️ Configuración Académica
                </h3>

                {/* 1. SECCIÓN DE SEDES */}
                <div style={panelCardStyle}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', flexWrap: 'wrap', gap: '12px' }}>
                    <div>
                      <h4 style={{ margin: 0, fontSize: '16px', fontWeight: 700, color: '#a78bfa' }}>
                        📍 Directorio de Sedes y Núcleos
                      </h4>
                      <p style={{ margin: '4px 0 0', fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Administra los núcleos geográficos donde se imparten los programas académicos del CIPPSV.
                      </p>
                    </div>
                    <div style={{ display: 'flex', gap: '12px', alignItems: 'center' }}>
                      <input
                        type="text"
                        placeholder="🔍 Buscar sede..."
                        value={configSedeSearch}
                        onChange={(e) => setConfigSedeSearch(e.target.value)}
                        style={{ ...inputStyle, width: '250px', padding: '8px 12px', fontSize: '13px' }}
                      />
                      <button
                        onClick={() => setShowCreateSedeModal(true)}
                        style={btnStylePrimary}
                      >
                        ➕ Agregar Nueva Sede
                      </button>
                    </div>
                  </div>

                  <div style={{ overflowX: 'auto' }}>
                    <table style={tableStyle}>
                      <thead>
                        <tr>
                          <th style={{ ...thStyle, width: '100px' }}>Código</th>
                          <th style={thStyle}>Nombre / Ciudad</th>
                          <th style={thStyle}>Estado / Provincia</th>
                          <th style={thStyle}>Modalidad</th>
                          <th style={thStyle}>Director / Coordinador</th>
                          <th style={thStyle}>Dirección Física</th>
                          <th style={{ ...thStyle, width: '150px', textAlign: 'center' }}>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        {(() => {
                          const filteredSedes = directorioSedes.filter(s => {
                            const term = configSedeSearch.toLowerCase().trim();
                            if (!term) return true;
                            return (
                              (s.codsede || '').toLowerCase().includes(term) ||
                              (s.ciudad || '').toLowerCase().includes(term) ||
                              (s.edo_prov || '').toLowerCase().includes(term) ||
                              (s.director_coordinador || '').toLowerCase().includes(term)
                            );
                          });

                          if (filteredSedes.length === 0) {
                            return (
                              <tr>
                                <td colSpan={7} style={{ ...tdStyle, textAlign: 'center', color: 'rgba(255,255,255,0.4)', padding: '24px' }}>
                                  No se encontraron sedes que coincidan con la búsqueda.
                                </td>
                              </tr>
                            );
                          }

                          return filteredSedes.map((s) => (
                            <tr key={s.codsede} style={trStyle}>
                              <td style={{ ...tdStyle, fontWeight: 700, color: '#a78bfa' }}>{s.codsede}</td>
                              <td style={{ ...tdStyle, color: '#fff', fontWeight: 600 }}>{s.ciudad}</td>
                              <td style={s.edo_prov ? tdStyle : { ...tdStyle, color: 'rgba(255,255,255,0.3)', fontStyle: 'italic' }}>{s.edo_prov || 'No definido'}</td>
                              <td style={tdStyle}>
                                <span style={{
                                  background: s.modalidad === 'Sede' ? 'rgba(59,130,246,0.1)' : 'rgba(16,185,129,0.1)',
                                  color: s.modalidad === 'Sede' ? '#3b82f6' : '#10b981',
                                  padding: '4px 8px', borderRadius: '8px', fontSize: '11px', fontWeight: 600,
                                  border: `1px solid ${s.modalidad === 'Sede' ? 'rgba(59,130,246,0.2)' : 'rgba(16,185,129,0.2)'}`
                                }}>
                                  {s.modalidad || 'Sede'}
                                </span>
                              </td>
                              <td style={tdStyle}>{s.director_coordinador || 'No asignado'}</td>
                              <td style={{ ...tdStyle, fontSize: '12.5px', maxWidth: '200px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }} title={s.direccion}>
                                {s.direccion}
                              </td>
                              <td style={{ ...tdStyle, textAlign: 'center' }}>
                                <div style={{ display: 'flex', gap: '8px', justifyContent: 'center' }}>
                                  <button
                                    onClick={() => { setSelectedSedeForEdit(s); setShowEditSedeModal(true); }}
                                    style={{ ...btnStyleSecondary, padding: '6px 12px', fontSize: '12px' }}
                                  >
                                    Editar
                                  </button>
                                  <button
                                    onClick={() => handleDeleteSede(s.codsede, s.ciudad)}
                                    style={{
                                      background: 'rgba(239,68,68,0.1)', border: '1px solid rgba(239,68,68,0.2)',
                                      color: '#f87171', padding: '6px 12px', borderRadius: '8px', cursor: 'pointer',
                                      fontSize: '12px', fontWeight: 600
                                    }}
                                  >
                                    Eliminar
                                  </button>
                                </div>
                              </td>
                            </tr>
                          ));
                        })()}
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* 2. SECCIÓN DE PROGRAMAS */}
                <div style={panelCardStyle}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px', flexWrap: 'wrap', gap: '12px' }}>
                    <div>
                      <h4 style={{ margin: 0, fontSize: '16px', fontWeight: 700, color: '#a78bfa' }}>
                        🎓 Gestión de Programas de Estudio
                      </h4>
                      <p style={{ margin: '4px 0 0', fontSize: '12px', color: 'rgba(255,255,255,0.4)' }}>
                        Administra la oferta académica de Postgrados, Doctorados, Maestrías y Diplomados del CIPPSV.
                      </p>
                    </div>
                    <div style={{ display: 'flex', gap: '12px', alignItems: 'center', flexWrap: 'wrap' }}>
                      <form
                        onSubmit={(e) => { e.preventDefault(); setConfigProgramPage(1); loadConfigPrograms(); }}
                        style={{ display: 'flex', gap: '8px' }}
                      >
                        <input
                          type="text"
                          placeholder="🔍 Buscar programa..."
                          value={configProgramSearch}
                          onChange={(e) => setConfigProgramSearch(e.target.value)}
                          style={{ ...inputStyle, width: '250px', padding: '8px 12px', fontSize: '13px' }}
                        />
                        <button type="submit" style={{ ...btnStylePrimary, padding: '8px 16px' }}>Buscar</button>
                        {configProgramSearch && (
                          <button
                            type="button"
                            onClick={() => { setConfigProgramSearch(''); setConfigProgramPage(1); }}
                            style={{ ...btnStyleSecondary, padding: '8px 12px' }}
                          >
                            Limpiar
                          </button>
                        )}
                      </form>
                      <button
                        onClick={() => setShowCreateProgramModal(true)}
                        style={btnStylePrimary}
                      >
                        ➕ Registrar Nuevo Programa
                      </button>
                    </div>
                  </div>

                  <div style={{ overflowX: 'auto' }}>
                    <table style={tableStyle}>
                      <thead>
                        <tr>
                          <th style={{ ...thStyle, width: '120px' }}>Código Prog.</th>
                          <th style={thStyle}>Sede Oferta</th>
                          <th style={thStyle}>Título a Otorgar</th>
                          <th style={thStyle}>Mención / Especialidad</th>
                          <th style={thStyle}>Tipo</th>
                          <th style={{ ...thStyle, width: '80px', textAlign: 'center' }}>Créditos</th>
                          <th style={{ ...thStyle, width: '150px', textAlign: 'center' }}>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                        {configProgramsList.length === 0 ? (
                          <tr>
                            <td colSpan={7} style={{ ...tdStyle, textAlign: 'center', color: 'rgba(255,255,255,0.4)', padding: '24px' }}>
                              No se encontraron programas de estudio.
                            </td>
                          </tr>
                        ) : (
                          configProgramsList.map((p, idx) => (
                            <tr key={`${p.codsede}-${p.codopest}-${idx}`} style={trStyle}>
                              <td style={{ ...tdStyle, fontWeight: 700, color: '#a78bfa' }}>{p.codopest}</td>
                              <td style={{ ...tdStyle, color: '#60a5fa', fontWeight: 600 }}>{getCityFromSede(p.codsede)}</td>
                              <td style={{ ...tdStyle, color: '#fff', fontWeight: 600 }}>{p.titulo_a_otorgar}</td>
                              <td style={tdStyle}>{p.mencion_especialidad || 'General'}</td>
                              <td style={tdStyle}>
                                <span style={{
                                  background: p.tipo === 'Doctorado' ? 'rgba(239,68,68,0.1)' : p.tipo === 'Maestria' ? 'rgba(139,92,246,0.1)' : p.tipo === 'Especializacion' ? 'rgba(245,158,11,0.1)' : 'rgba(16,185,129,0.1)',
                                  color: p.tipo === 'Doctorado' ? '#ef4444' : p.tipo === 'Maestria' ? '#a78bfa' : p.tipo === 'Especializacion' ? '#f59e0b' : '#10b981',
                                  padding: '4px 8px', borderRadius: '8px', fontSize: '11px', fontWeight: 600,
                                  border: `1px solid ${p.tipo === 'Doctorado' ? 'rgba(239,68,68,0.2)' : p.tipo === 'Maestria' ? 'rgba(139,92,246,0.2)' : p.tipo === 'Especializacion' ? 'rgba(245,158,11,0.2)' : 'rgba(16,185,129,0.2)'}`
                                }}>
                                  {p.tipo}
                                </span>
                              </td>
                              <td style={{ ...tdStyle, textAlign: 'center', fontWeight: 700 }}>{p.creditos || '0'}</td>
                              <td style={{ ...tdStyle, textAlign: 'center' }}>
                                <div style={{ display: 'flex', gap: '8px', justifyContent: 'center' }}>
                                  <button
                                    onClick={() => { setSelectedProgramForEdit(p); setShowEditProgramModal(true); }}
                                    style={{ ...btnStyleSecondary, padding: '6px 12px', fontSize: '12px' }}
                                  >
                                    Editar
                                  </button>
                                  <button
                                    onClick={() => handleDeleteProgram(p.codsede, p.codopest, p.titulo_a_otorgar)}
                                    style={{
                                      background: 'rgba(239,68,68,0.1)', border: '1px solid rgba(239,68,68,0.2)',
                                      color: '#f87171', padding: '6px 12px', borderRadius: '8px', cursor: 'pointer',
                                      fontSize: '12px', fontWeight: 600
                                    }}
                                  >
                                    Eliminar
                                  </button>
                                </div>
                              </td>
                            </tr>
                          ))
                        )}
                      </tbody>
                    </table>
                  </div>

                  {/* Paginación de Programas */}
                  {configProgramTotal > 0 && (
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: '20px', flexWrap: 'wrap', gap: '12px' }}>
                      <div style={{ fontSize: '13px', color: 'rgba(255,255,255,0.4)' }}>
                        Mostrando {configProgramsList.length} de {configProgramTotal} programas
                      </div>
                      <div style={{ display: 'flex', gap: '8px', alignItems: 'center' }}>
                        <button
                          disabled={configProgramPage <= 1}
                          onClick={() => setConfigProgramPage(prev => Math.max(prev - 1, 1))}
                          style={{
                            ...btnStyleSecondary,
                            padding: '6px 12px',
                            fontSize: '12px',
                            opacity: configProgramPage <= 1 ? 0.5 : 1,
                            cursor: configProgramPage <= 1 ? 'not-allowed' : 'pointer'
                          }}
                        >
                          ◀ Anterior
                        </button>
                        <span style={{ fontSize: '13px', fontWeight: 600, color: '#fff', padding: '0 8px' }}>
                          Página {configProgramPage} de {Math.max(Math.ceil(configProgramTotal / 10), 1)}
                        </span>
                        <button
                          disabled={configProgramPage >= Math.max(Math.ceil(configProgramTotal / 10), 1)}
                          onClick={() => setConfigProgramPage(prev => prev + 1)}
                          style={{
                            ...btnStyleSecondary,
                            padding: '6px 12px',
                            fontSize: '12px',
                            opacity: configProgramPage >= Math.max(Math.ceil(configProgramTotal / 10), 1) ? 0.5 : 1,
                            cursor: configProgramPage >= Math.max(Math.ceil(configProgramTotal / 10), 1) ? 'not-allowed' : 'pointer'
                          }}
                        >
                          Siguiente ▶
                        </button>
                      </div>
                    </div>
                  )}
                </div>

                {/* MODALES CRUD PARA SEDES */}
                {showCreateSedeModal && (
                  <div style={modalBackdropStyle}>
                    <div style={{ ...modalContentStyle, maxWidth: '600px' }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                          📍 Registrar Nueva Sede o Núcleo
                        </h3>
                        <button onClick={() => setShowCreateSedeModal(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '18px', cursor: 'pointer' }}>✕</button>
                      </div>
                      <form onSubmit={handleCreateSede} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Código de Sede (Siglas)</label>
                            <input
                              type="text" required placeholder="Ej: EV, PPAL, ORN"
                              value={newSedeAccount.codsede}
                              onChange={(e) => setNewSedeAccount({ ...newSedeAccount, codsede: e.target.value.toUpperCase() })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Modalidad</label>
                            <select
                              value={newSedeAccount.modalidad}
                              onChange={(e) => setNewSedeAccount({ ...newSedeAccount, modalidad: e.target.value as any })}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value="Sede">Sede Principal</option>
                              <option value="Nucleo">Núcleo Académico</option>
                            </select>
                          </div>
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Ciudad / Nombre Identificador</label>
                            <input
                              type="text" required placeholder="Ej: Entorno Virtual, Caracas"
                              value={newSedeAccount.ciudad}
                              onChange={(e) => setNewSedeAccount({ ...newSedeAccount, ciudad: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Estado / Provincia</label>
                            <input
                              type="text" required placeholder="Ej: Virtual, Miranda, Anzoategui"
                              value={newSedeAccount.edo_prov}
                              onChange={(e) => setNewSedeAccount({ ...newSedeAccount, edo_prov: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div>
                          <label style={labelStyle}>Director o Coordinador a Cargo</label>
                          <input
                            type="text" required placeholder="Ej: Lic. Mercedes Labrador"
                            value={newSedeAccount.director_coordinador}
                            onChange={(e) => setNewSedeAccount({ ...newSedeAccount, director_coordinador: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Dirección Física o Acceso</label>
                          <input
                            type="text" required placeholder="Ej: Calle Principal Edif Pelícano Piso 1"
                            value={newSedeAccount.direccion}
                            onChange={(e) => setNewSedeAccount({ ...newSedeAccount, direccion: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Teléfono / Fax (Opcional)</label>
                            <input
                              type="text" placeholder="Ej: 0212-5556677"
                              value={newSedeAccount.fax}
                              onChange={(e) => setNewSedeAccount({ ...newSedeAccount, fax: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Correo de Contacto (Opcional)</label>
                            <input
                              type="email" placeholder="Ej: virtual@cippsv.com"
                              value={newSedeAccount.email}
                              onChange={(e) => setNewSedeAccount({ ...newSedeAccount, email: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowCreateSedeModal(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>{loading ? 'Guardando...' : 'Registrar Sede'}</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}

                {showEditSedeModal && selectedSedeForEdit && (
                  <div style={modalBackdropStyle}>
                    <div style={{ ...modalContentStyle, maxWidth: '600px' }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                          ✏️ Editar Sede: {selectedSedeForEdit.codsede}
                        </h3>
                        <button onClick={() => setShowEditSedeModal(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '18px', cursor: 'pointer' }}>✕</button>
                      </div>
                      <form onSubmit={handleUpdateSede} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Código de Sede (No editable)</label>
                            <input type="text" disabled value={selectedSedeForEdit.codsede} style={{ ...inputStyle, opacity: 0.5 }} />
                          </div>
                          <div>
                            <label style={labelStyle}>Modalidad</label>
                            <select
                              value={selectedSedeForEdit.modalidad || 'Sede'}
                              onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, modalidad: e.target.value as any })}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value="Sede">Sede Principal</option>
                              <option value="Nucleo">Núcleo Académico</option>
                            </select>
                          </div>
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Ciudad / Nombre Identificador</label>
                            <input
                              type="text" required
                              value={selectedSedeForEdit.ciudad}
                              onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, ciudad: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Estado / Provincia</label>
                            <input
                              type="text" required
                              value={selectedSedeForEdit.edo_prov}
                              onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, edo_prov: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div>
                          <label style={labelStyle}>Director o Coordinador a Cargo</label>
                          <input
                            type="text" required
                            value={selectedSedeForEdit.director_coordinador}
                            onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, director_coordinador: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Dirección Física o Acceso</label>
                          <input
                            type="text" required
                            value={selectedSedeForEdit.direccion}
                            onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, direccion: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Teléfono / Fax (Opcional)</label>
                            <input
                              type="text"
                              value={selectedSedeForEdit.fax || ''}
                              onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, fax: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                          <div>
                            <label style={labelStyle}>Correo de Contacto (Opcional)</label>
                            <input
                              type="email"
                              value={selectedSedeForEdit.email || ''}
                              onChange={(e) => setSelectedSedeForEdit({ ...selectedSedeForEdit, email: e.target.value })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowEditSedeModal(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>{loading ? 'Guardando...' : 'Guardar Cambios'}</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}

                {/* MODALES CRUD PARA PROGRAMAS */}
                {showCreateProgramModal && (
                  <div style={modalBackdropStyle}>
                    <div style={{ ...modalContentStyle, maxWidth: '600px' }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                          🎓 Registrar Nuevo Programa de Estudio
                        </h3>
                        <button onClick={() => setShowCreateProgramModal(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '18px', cursor: 'pointer' }}>✕</button>
                      </div>
                      <form onSubmit={handleCreateProgram} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div>
                          <label style={labelStyle}>Sede / Núcleo de Oferta</label>
                          <select
                            required
                            value={newProgramAccount.codsede}
                            onChange={(e) => setNewProgramAccount({ ...newProgramAccount, codsede: e.target.value })}
                            style={{ ...inputStyle, background: '#120f30' }}
                          >
                            <option value="">Selecciona Sede...</option>
                            {directorioSedes.map(s => (
                              <option key={s.codsede} value={s.codsede}>{s.ciudad} ({s.codsede})</option>
                            ))}
                          </select>
                        </div>

                        <div>
                          <label style={labelStyle}>Título a Otorgar</label>
                          <input
                            type="text" required placeholder="Ej: Magister en Planificación de la Conducta"
                            value={newProgramAccount.titulo_a_otorgar}
                            onChange={(e) => setNewProgramAccount({ ...newProgramAccount, titulo_a_otorgar: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Mención / Especialidad</label>
                          <input
                            type="text" required placeholder="Ej: Mención Terapia de la Conducta"
                            value={newProgramAccount.mencion_especialidad}
                            onChange={(e) => setNewProgramAccount({ ...newProgramAccount, mencion_especialidad: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Tipo de Programa</label>
                            <select
                              value={newProgramAccount.tipo}
                              onChange={(e) => setNewProgramAccount({ ...newProgramAccount, tipo: e.target.value })}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value="Maestria">Maestría</option>
                              <option value="Especializacion">Especialización</option>
                              <option value="Doctorado">Doctorado</option>
                              <option value="Diplomado">Diplomado / Otro</option>
                            </select>
                          </div>
                          <div>
                            <label style={labelStyle}>Créditos Requeridos</label>
                            <input
                              type="number" required min={0}
                              value={newProgramAccount.creditos}
                              onChange={(e) => setNewProgramAccount({ ...newProgramAccount, creditos: Number(e.target.value) })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div>
                          <label style={labelStyle}>Código del Programa (codopest)</label>
                          <input
                            type="text" required placeholder="Ej: DIP-DE, MC-OC, DR-CS"
                            value={newProgramAccount.codopest}
                            onChange={(e) => setNewProgramAccount({ ...newProgramAccount, codopest: e.target.value.toUpperCase() })}
                            style={inputStyle}
                          />
                          {(() => {
                            const suggested = suggestProgramCode(
                              newProgramAccount.codsede,
                              newProgramAccount.tipo,
                              newProgramAccount.mencion_especialidad,
                              newProgramAccount.titulo_a_otorgar
                            );
                            if (!suggested) return null;
                            return (
                              <span 
                                onClick={() => setNewProgramAccount({ ...newProgramAccount, codopest: suggested })}
                                style={{ fontSize: '11px', color: '#818cf8', cursor: 'pointer', display: 'block', marginTop: '4px', textDecoration: 'underline' }}
                              >
                                Sugerencia basada en especialidad: {suggested} (hacer clic para aplicar)
                              </span>
                            );
                          })()}
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowCreateProgramModal(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>{loading ? 'Guardando...' : 'Registrar Programa'}</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}

                {showEditProgramModal && selectedProgramForEdit && (
                  <div style={modalBackdropStyle}>
                    <div style={{ ...modalContentStyle, maxWidth: '600px' }}>
                      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '1px solid rgba(255,255,255,0.08)', paddingBottom: '16px', marginBottom: '20px' }}>
                        <h3 style={{ margin: 0, fontSize: '18px', fontWeight: 700, color: '#a78bfa' }}>
                          ✏️ Editar Programa: {selectedProgramForEdit.codopest}
                        </h3>
                        <button onClick={() => setShowEditProgramModal(false)} style={{ background: 'transparent', border: 'none', color: '#fff', fontSize: '18px', cursor: 'pointer' }}>✕</button>
                      </div>
                      <form onSubmit={handleUpdateProgram} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Sede (No editable)</label>
                            <input type="text" disabled value={getCityFromSede(selectedProgramForEdit.codsede)} style={{ ...inputStyle, opacity: 0.5 }} />
                          </div>
                          <div>
                            <label style={labelStyle}>Código del Programa (No editable)</label>
                            <input type="text" disabled value={selectedProgramForEdit.codopest} style={{ ...inputStyle, opacity: 0.5 }} />
                          </div>
                        </div>

                        <div>
                          <label style={labelStyle}>Título a Otorgar</label>
                          <input
                            type="text" required
                            value={selectedProgramForEdit.titulo_a_otorgar}
                            onChange={(e) => setSelectedProgramForEdit({ ...selectedProgramForEdit, titulo_a_otorgar: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div>
                          <label style={labelStyle}>Mención / Especialidad</label>
                          <input
                            type="text" required
                            value={selectedProgramForEdit.mencion_especialidad}
                            onChange={(e) => setSelectedProgramForEdit({ ...selectedProgramForEdit, mencion_especialidad: e.target.value })}
                            style={inputStyle}
                          />
                        </div>

                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
                          <div>
                            <label style={labelStyle}>Tipo de Programa</label>
                            <select
                              value={selectedProgramForEdit.tipo}
                              onChange={(e) => setSelectedProgramForEdit({ ...selectedProgramForEdit, tipo: e.target.value })}
                              style={{ ...inputStyle, background: '#120f30' }}
                            >
                              <option value="Maestria">Maestría</option>
                              <option value="Especializacion">Especialización</option>
                              <option value="Doctorado">Doctorado</option>
                              <option value="Diplomado">Diplomado / Otro</option>
                            </select>
                          </div>
                          <div>
                            <label style={labelStyle}>Créditos Requeridos</label>
                            <input
                              type="number" required min={0}
                              value={selectedProgramForEdit.creditos || 0}
                              onChange={(e) => setSelectedProgramForEdit({ ...selectedProgramForEdit, creditos: Number(e.target.value) })}
                              style={inputStyle}
                            />
                          </div>
                        </div>

                        <div style={{ display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid rgba(255,255,255,0.08)', paddingTop: '20px', marginTop: '10px' }}>
                          <button type="button" onClick={() => setShowEditProgramModal(false)} style={btnStyleSecondary}>Cancelar</button>
                          <button type="submit" style={btnStylePrimary}>{loading ? 'Guardando...' : 'Guardar Cambios'}</button>
                        </div>
                      </form>
                    </div>
                  </div>
                )}

              </div>
            )}



          </div>

        </div>
      )}
    </div>
  );
}

// Inline CSS style definitions helper functions
const labelStyle: React.CSSProperties = {
  display: 'block', fontSize: '12px', fontWeight: 600,
  color: 'rgba(255,255,255,0.5)', marginBottom: '8px',
  textTransform: 'uppercase', letterSpacing: '0.5px',
};

const inputStyle: React.CSSProperties = {
  width: '100%', padding: '12px 16px', borderRadius: '12px',
  background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(255,255,255,0.08)',
  color: '#fff', fontSize: '14.5px', outline: 'none',
  boxSizing: 'border-box', transition: 'border-color 0.2s',
};

const navItemStyle = (active: boolean): React.CSSProperties => ({
  width: '100%', padding: '12px 16px', borderRadius: '12px',
  border: 'none', cursor: 'pointer', textAlign: 'left',
  fontWeight: 600, fontSize: '14.5px', transition: 'all 0.2s',
  background: active ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'transparent',
  color: active ? '#fff' : 'rgba(255,255,255,0.5)',
});

const statCardStyle: React.CSSProperties = {
  background: 'rgba(255,255,255,0.02)',
  border: '1px solid rgba(255,255,255,0.05)',
  borderRadius: '20px', padding: '24px',
  display: 'flex', alignItems: 'center', gap: '20px'
};

const panelCardStyle: React.CSSProperties = {
  background: 'rgba(255,255,255,0.02)',
  border: '1px solid rgba(255,255,255,0.05)',
  borderRadius: '20px', padding: '28px',
};

const detailLabelStyle: React.CSSProperties = {
  display: 'block', fontSize: '11px', color: 'rgba(255,255,255,0.4)', textTransform: 'uppercase', letterSpacing: '0.5px'
};

const detailValueStyle: React.CSSProperties = {
  fontSize: '15px', color: '#fff', fontWeight: 600, marginTop: '4px', background: 'rgba(255,255,255,0.01)', padding: '10px 14px', borderRadius: '8px', border: '1px solid rgba(255,255,255,0.03)'
};

const btnStylePrimary: React.CSSProperties = {
  background: 'linear-gradient(135deg, #6366f1, #8b5cf6)', border: 'none', color: '#fff',
  fontWeight: 700, padding: '12px 20px', borderRadius: '12px', cursor: 'pointer',
  boxShadow: '0 4px 15px rgba(99,102,241,0.25)', fontSize: '14px'
};

const btnStyleSecondary: React.CSSProperties = {
  background: 'rgba(255,255,255,0.06)', border: '1px solid rgba(255,255,255,0.08)', color: '#fff',
  fontWeight: 600, padding: '12px 20px', borderRadius: '12px', cursor: 'pointer', fontSize: '14px'
};

const modalBackdropStyle: React.CSSProperties = {
  position: 'fixed', top: 0, left: 0, width: '100vw', height: '100vh',
  background: 'rgba(0,0,0,0.7)', backdropFilter: 'blur(5px)',
  display: 'flex', alignItems: 'center', zIndex: 1000,
  justifyContent: 'center',
};

const modalContentStyle: React.CSSProperties = {
  width: '100%', maxWidth: '520px', background: '#0e0b24',
  border: '1px solid rgba(255,255,255,0.08)', borderRadius: '24px',
  padding: '32px', boxShadow: '0 25px 50px rgba(0,0,0,0.5)',
};

const tableStyle: React.CSSProperties = {
  width: '100%', borderCollapse: 'collapse', textAlign: 'left',
};

const thStyle: React.CSSProperties = {
  padding: '12px 16px', borderBottom: '1px solid rgba(255,255,255,0.08)',
  color: 'rgba(255,255,255,0.4)', fontSize: '12px', textTransform: 'uppercase', fontWeight: 600
};

const tdStyle: React.CSSProperties = {
  padding: '14px 16px', borderBottom: '1px solid rgba(255,255,255,0.04)',
  fontSize: '14px', color: 'rgba(255,255,255,0.8)'
};

const trStyle: React.CSSProperties = {
  transition: 'background 0.2s',
};
