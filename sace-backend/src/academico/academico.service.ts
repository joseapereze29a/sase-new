import { Injectable, NotFoundException, BadRequestException, OnModuleInit } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import { CreateOportunidadDto } from './dto/create-oportunidad.dto';
import { UpdateOportunidadDto } from './dto/update-oportunidad.dto';
import { CreatePensumDto } from './dto/create-pensum.dto';
import { UpdatePensumDto } from './dto/update-pensum.dto';
import PDFDocument from 'pdfkit';
import * as fs from 'fs';

function cleanHtmlEntities(str?: string | null): string {
  if (!str) return '';
  return str
    .replace(/Filosof\?\?a/gi, 'Filosofía')
    .replace(/Formaci\?\?n/gi, 'Formación')
    .replace(/Estad\?\?stica/gi, 'Estadística')
    .replace(/M\?\?todos/gi, 'Métodos')
    .replace(/Investigaci\?\?n/gi, 'Investigación')
    .replace(/&oacute;/gi, 'ó')
    .replace(/&iacute;/gi, 'í')
    .replace(/&aacute;/gi, 'á')
    .replace(/&eacute;/gi, 'é')
    .replace(/&uacute;/gi, 'ú')
    .replace(/&ntilde;/gi, 'ñ')
    .replace(/&Oacute;/gi, 'Ó')
    .replace(/&Iacute;/gi, 'Í')
    .replace(/&Aacute;/gi, 'Á')
    .replace(/&Eacute;/gi, 'É')
    .replace(/&Uacute;/gi, 'Ú')
    .replace(/&Ntilde;/gi, 'Ñ');
}

function cleanProgramName(str?: string | null): string {
  if (!str) return '';
  let clean = cleanHtmlEntities(str);
  clean = clean.replace(/en\s+ciencias\s+menci[oó]n\s+/i, 'en ');
  clean = clean.replace(/ciencias\s+menci[oó]n\s+/i, '');
  clean = clean.replace(/orientaci[oó]n\s+(en\s+)?psicopol[ií]tica/gi, 'Psicopolítica');
  clean = clean.replace(/orientaci[oó]n\s+psicopol[ií]tica/gi, 'Psicopolítica');
  if (/^mag[ií]ster\s+en\s+psicopol[ií]tica/i.test(clean)) {
    clean = 'Maestría en Psicopolítica';
  }
  return clean.trim();
}

function getCurricularRank(subjectName?: string | null): number {
  if (!subjectName) return 100;
  const name = subjectName
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '');

  // 1. Eje Actitudinal (Siempre primero en toda la oferta institucional)
  if (name.includes('formacion de actitudes') || name.includes('actitudes')) return 10;

  // 2. Eje Disciplinar / Especialidad Principal
  if (name.includes('filosofia de la ciencia')) return 20;
  if (name.includes('psicopolitica')) return 21;
  if (name.includes('sexologia') && !name.includes('sexologia basica')) return 22;
  if (name.includes('terapia de la conducta') || (name.includes('conducta') && !name.includes('conducta general'))) return 23;
  if (name.includes('educacion para padres')) return 24;
  if (name.includes('supervision') || name.includes('gerencia') || name.includes('direccion, supervision')) return 25;

  // 3. Eje Metodológico / Investigativo / Seminarios / Tesis
  if (name.includes('metodologia')) return 30;
  if (name.includes('estadistica')) return 32;
  if (name.includes('metodos avanzados') || name.includes('metodos de investigacion')) return 34;
  if (name.includes('seminario de tesis') || name.includes('seminario')) return 40;
  if (name.includes('protocolo de casos') || name.includes('presentacion de casos') || name.includes('casos')) return 45;
  if (name.includes('trabajo de grado') || name.includes('tesis') || name.includes('trabajo especial')) return 50;

  // 4. Eje Instrumental y Complementario
  if (name.includes('recursos audiovisuales') || name.includes('recursos expresivos') || name.includes('audiovisual')) return 60;
  if (name.includes('conducta general')) return 62;
  if (name.includes('variante fisiologica')) return 64;
  if (name.includes('entrevista') || name.includes('tecnicas de entrevista')) return 66;
  if (name.includes('neuropsicologia')) return 68;
  if (name.includes('psiquiatria') || name.includes('sexologia basica')) return 70;
  if (name.includes('electiva')) return 80;

  return 90;
}

function getRomanOrNumeralValue(subjectName?: string | null): number {
  if (!subjectName) return 0;
  const match = subjectName.match(/\b([IVXLCDM]+|\d+)\b$/i);
  if (!match) return 0;
  const token = match[1].toUpperCase();
  const romanMap: Record<string, number> = {
    I: 1, II: 2, III: 3, IV: 4, V: 5,
    VI: 6, VII: 7, VIII: 8, IX: 9, X: 10,
  };
  if (romanMap[token]) return romanMap[token];
  const num = parseInt(token, 10);
  return isNaN(num) ? 0 : num;
}

function sortSubjectsCurricularly(a: any, b: any): number {
  // 1. Período cronológico
  const perA = a.periodos !== null && a.periodos !== undefined ? Number(a.periodos) : 999;
  const perB = b.periodos !== null && b.periodos !== undefined ? Number(b.periodos) : 999;
  if (perA !== perB) return perA - perB;

  // 2. Eje curricular institucional
  const rankA = getCurricularRank(a.asignatura);
  const rankB = getCurricularRank(b.asignatura);
  if (rankA !== rankB) return rankA - rankB;

  // 3. Número ordinal (I, II, III, IV...)
  const numA = getRomanOrNumeralValue(a.asignatura);
  const numB = getRomanOrNumeralValue(b.asignatura);
  if (numA !== numB) return numA - numB;

  // 4. Nombre
  const nameComp = (a.asignatura || '').localeCompare(b.asignatura || '');
  if (nameComp !== 0) return nameComp;

  // 5. Código
  return (a.codasig || '').localeCompare(b.codasig || '');
}

@Injectable()
export class AcademicoService implements OnModuleInit {
  constructor(private readonly prisma: PrismaService) {}

  async onModuleInit() {
    try {
      // 1. Asegurar que MC-PP tenga el título correcto sin "Orientación"
      await this.prisma.oportunidadesEstudio.updateMany({
        where: { codopest: 'MC-PP' },
        data: {
          mencion_especialidad: 'Psicopolítica',
          titulo_a_otorgar: 'Maestría en Psicopolítica',
        },
      });

      // 2. Asegurar que las prelaciones de MC-PP existan en todas las sedes
      const existingPP = await this.prisma.pensum_prelaciones.count({
        where: { codopest: 'MC-PP' },
      });
      if (existingPP === 0) {
        const sedes = ['COC', 'COC1', 'COC2', 'CPL', 'CUM', 'EV', 'MAT', 'OCC', 'OCC1', 'ORN', 'PPAL'];
        const prelsDefinition = [
          { codasig: 'PP-002', prelacion: 'PP-001' },
          { codasig: 'PP-006', prelacion: 'PP-005' },
          { codasig: 'PP-013', prelacion: 'PP-012' },
          { codasig: 'PP-018', prelacion: 'PP-017' },
          { codasig: 'PP-003', prelacion: 'PP-002' },
          { codasig: 'PP-007', prelacion: 'PP-006' },
          { codasig: 'PP-009', prelacion: 'PP-006' },
          { codasig: 'PP-010', prelacion: 'PP-009' },
          { codasig: 'PP-014', prelacion: 'PP-013' },
          { codasig: 'PP-004', prelacion: 'PP-003' },
          { codasig: 'PP-008', prelacion: 'PP-007' },
          { codasig: 'PP-011', prelacion: 'PP-010' },
          { codasig: 'PP-015', prelacion: 'PP-014' },
          { codasig: 'PP-019', prelacion: 'PP-008' },
          { codasig: 'PP-019', prelacion: 'PP-015' },
          { codasig: 'PP-020', prelacion: 'PP-016' },
          { codasig: 'PP-021', prelacion: 'PP-019' },
          { codasig: 'PP-022', prelacion: 'PP-020' },
          { codasig: 'PP-022', prelacion: 'PP-021' },
        ];
        for (const s of sedes) {
          for (const p of prelsDefinition) {
            await this.prisma.pensum_prelaciones.upsert({
              where: {
                codsede_codopest_codasig_prelacion: {
                  codsede: s,
                  codopest: 'MC-PP',
                  codasig: p.codasig,
                  prelacion: p.prelacion,
                },
              },
              update: {},
              create: {
                codsede: s,
                codopest: 'MC-PP',
                codasig: p.codasig,
                prelacion: p.prelacion,
              },
            });
          }
        }
      }

      // 3. Asegurar que las prelaciones de Doctorados (DR-CS, DR-SC, DR-SM) existan en todas las sedes
      const doctoradosList = ['DR-CS', 'DR-SC', 'DR-SM'];
      const docPrelsDefinition = [
        { codasig: 'DSC-002', prelacion: 'DSC-001' },
        { codasig: 'DSC-004', prelacion: 'DSC-003' },
        { codasig: 'DSC-008', prelacion: 'DSC-007' },
        { codasig: 'DSC-006', prelacion: 'DSC-005' },
        { codasig: 'DSC-009', prelacion: 'DSC-008' },
        { codasig: 'DSC-014', prelacion: 'DSC-013' },
        { codasig: 'DSC-010', prelacion: 'DSC-009' },
        { codasig: 'DSC-015', prelacion: 'DSC-014' },
        { codasig: 'DSC-011', prelacion: 'DSC-010' },
        { codasig: 'DSC-016', prelacion: 'DSC-015' },
        { codasig: 'DSC-012', prelacion: 'DSC-011' },
        { codasig: 'DSC-017', prelacion: 'DSC-012' },
      ];
      const allSedes = ['COC', 'COC1', 'COC2', 'CPL', 'CUM', 'EV', 'MAT', 'OCC', 'OCC1', 'ORN', 'PPAL'];

      for (const doc of doctoradosList) {
        const existingDoc = await this.prisma.pensum_prelaciones.count({
          where: { codopest: doc },
        });
        if (existingDoc === 0) {
          for (const s of allSedes) {
            for (const p of docPrelsDefinition) {
              await this.prisma.pensum_prelaciones.upsert({
                where: {
                  codsede_codopest_codasig_prelacion: {
                    codsede: s,
                    codopest: doc,
                    codasig: p.codasig,
                    prelacion: p.prelacion,
                  },
                },
                update: {},
                create: {
                  codsede: s,
                  codopest: doc,
                  codasig: p.codasig,
                  prelacion: p.prelacion,
                },
              });
            }
          }
        }
      }

      // 4. Corregir caracteres corruptos en nombres de asignaturas de Doctorado si existen
      await this.prisma.pensumEstudios.updateMany({
        where: { codasig: 'DSC-001', asignatura: { contains: 'Filosof' } },
        data: { asignatura: 'Filosofía de la Ciencia I' },
      });
      await this.prisma.pensumEstudios.updateMany({
        where: { codasig: 'DSC-002' },
        data: { asignatura: 'Filosofía de la Ciencia II' },
      });
      await this.prisma.pensumEstudios.updateMany({
        where: { codasig: 'DSC-003' },
        data: { asignatura: 'Formación de Actitudes como Ciencia I' },
      });
      await this.prisma.pensumEstudios.updateMany({
        where: { codasig: 'DSC-004' },
        data: { asignatura: 'Formación de Actitudes como Ciencia II' },
      });
      await this.prisma.pensumEstudios.updateMany({
        where: { codasig: 'DSC-005' },
        data: { asignatura: 'Estadística Aplicada' },
      });
      await this.prisma.pensumEstudios.updateMany({
        where: { codasig: 'DSC-006' },
        data: { asignatura: 'Métodos Avanzados de Investigación' },
      });
    } catch (e: any) {
      console.warn('Error inicializando datos académicos en OnModuleInit:', e.message);
    }
  }

  async findAllAsignaturasCatalogo(search?: string) {
    const where: any = {};
    if (search) {
      where.OR = [
        { codasig: { contains: search } },
        { asignatura: { contains: search } },
      ];
    }
    return this.prisma.asignaturasCatalogo.findMany({
      where,
      orderBy: { asignatura: 'asc' },
    });
  }

  async generateSubjectCode(name: string): Promise<string> {
    const stopWords = new Set(['de', 'la', 'en', 'y', 'a', 'del', 'el', 'los', 'las', 'un', 'una', 'con', 'para', 'por', 'sobre', 'mencion']);
    
    let baseName = name.trim();
    let preferredNum = 1;
    
    const matchRoman = baseName.match(/^(.*?)\s+([IVXLCDM]+)$/i);
    if (matchRoman) {
      baseName = matchRoman[1].trim();
      const romanStr = matchRoman[2].toUpperCase();
      const romanMap: { [key: string]: number } = { I: 1, V: 5, X: 10, L: 50, C: 100, D: 500, M: 1000 };
      let total = 0;
      for (let i = 0; i < romanStr.length; i++) {
        const current = romanMap[romanStr[i]];
        const next = romanMap[romanStr[i + 1]];
        if (next && current < next) {
          total -= current;
        } else {
          total += current;
        }
      }
      preferredNum = total || 1;
    }
    
    const words = baseName
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/[^a-z0-9\s]/g, "")
      .split(/\s+/)
      .filter(w => w && !stopWords.has(w));
      
    const initials = words.map(w => w[0].toUpperCase()).join('') || 'MAT';
    
    let num = preferredNum;
    let finalCode = `${initials}-${String(num).padStart(3, '0')}`;
    let exists = await this.prisma.asignaturasCatalogo.findUnique({
      where: { codasig: finalCode }
    });
    
    while (exists) {
      num++;
      finalCode = `${initials}-${String(num).padStart(3, '0')}`;
      exists = await this.prisma.asignaturasCatalogo.findUnique({
        where: { codasig: finalCode }
      });
    }
    
    return finalCode;
  }

  async createAsignaturaCatalogo(data: { asignatura: string; creditos?: number }) {
    const name = (data.asignatura || '').trim();
    if (!name) {
      throw new BadRequestException('El nombre de la asignatura es requerido.');
    }

    const existing = await this.prisma.asignaturasCatalogo.findFirst({
      where: {
        asignatura: {
          equals: name
        }
      }
    });

    if (existing) {
      return existing;
    }

    const codasig = await this.generateSubjectCode(name);

    return this.prisma.asignaturasCatalogo.create({
      data: {
        codasig,
        asignatura: name,
        creditos: data.creditos || 0,
      },
    });
  }

  // ==========================================
  // OPORTUNIDADES DE ESTUDIO
  // ==========================================

  async findAllOportunidades(params: { skip?: number; take?: number; search?: string }) {
    const { skip, take, search } = params;
    const where: any = {};

    if (search) {
      where.OR = [
        { codopest: { contains: search } },
        { mencion_especialidad: { contains: search } },
        { titulo_a_otorgar: { contains: search } },
      ];
    }

    const allItems = await this.prisma.oportunidadesEstudio.findMany({
      where,
      orderBy: { codopest: 'asc' },
    });

    // Deduplicar en memoria por codopest
    const uniqueMap = new Map<string, any>();
    allItems.forEach((item) => {
      if (!uniqueMap.has(item.codopest)) {
        uniqueMap.set(item.codopest, item);
      }
    });

    const uniqueItems = Array.from(uniqueMap.values());
    const total = uniqueItems.length;

    // Aplicar paginación (skip y take) manual
    const skipNum = skip ? Number(skip) : 0;
    const takeNum = take ? Number(take) : undefined;
    const items = takeNum !== undefined 
      ? uniqueItems.slice(skipNum, skipNum + takeNum) 
      : uniqueItems.slice(skipNum);

    return { items, total };
  }

  async findOneOportunidad(codsede: string, codopest: string) {
    const record = await this.prisma.oportunidadesEstudio.findUnique({
      where: {
        codsede_codopest: { codsede, codopest },
      },
    });
    if (!record) {
      throw new NotFoundException(`No se encontró programa con sede ${codsede} y código ${codopest}`);
    }
    return record;
  }

  async createOportunidad(dto: CreateOportunidadDto) {
    // Verificar si ya existe
    const exists = await this.prisma.oportunidadesEstudio.findUnique({
      where: {
        codsede_codopest: { codsede: dto.codsede, codopest: dto.codopest },
      },
    });
    if (exists) {
      throw new BadRequestException(
        `El programa con sede ${dto.codsede} y código ${dto.codopest} ya existe.`,
      );
    }

    return this.prisma.oportunidadesEstudio.create({
      data: dto as any,
    });
  }

  async updateOportunidad(codsede: string, codopest: string, dto: UpdateOportunidadDto) {
    await this.findOneOportunidad(codsede, codopest);

    return this.prisma.oportunidadesEstudio.update({
      where: {
        codsede_codopest: { codsede, codopest },
      },
      data: dto as any,
    });
  }

  async deleteOportunidad(codsede: string, codopest: string) {
    await this.findOneOportunidad(codsede, codopest);

    return this.prisma.oportunidadesEstudio.delete({
      where: {
        codsede_codopest: { codsede, codopest },
      },
    });
  }

  // ==========================================
  // PENSUM DE ESTUDIOS
  // ==========================================

  async findAllPensum(params: {
    skip?: number;
    take?: number;
    codsede?: string;
    codopest?: string;
    search?: string;
  }) {
    const { skip, take, codsede, codopest, search } = params;
    const where: any = {};

    if (codsede) where.codsede = codsede;
    if (codopest) where.codopest = codopest;

    if (search) {
      where.OR = [
        { codasig: { contains: search } },
        { asignatura: { contains: search } },
      ];
    }

    const [items, total] = await Promise.all([
      this.prisma.pensumEstudios.findMany({
        where,
        skip: skip ? Number(skip) : undefined,
        take: take ? Number(take) : undefined,
        orderBy: [{ periodos: 'asc' }, { codasig: 'asc' }],
      }),
      this.prisma.pensumEstudios.count({ where }),
    ]);

    // Consultar prelaciones en lote (batch query) para mayor rendimiento
    const codsSede = [...new Set(items.map((i) => i.codsede))];
    const codsOpest = [...new Set(items.map((i) => i.codopest))];
    const codsAsig = items.map((i) => i.codasig);
    const codsImp = items.map((i) => i.codasig_imp).filter(Boolean) as string[];
    const allQueryCodes = [...new Set([...codsAsig, ...codsImp])];

    const prelaciones = await this.prisma.pensum_prelaciones.findMany({
      where: {
        codsede: { in: codsSede },
        codopest: { in: codsOpest },
        codasig: { in: allQueryCodes },
      },
    });

    const enrichedItems = items.map((item) => {
      const itemPrels = Array.from(new Set(
        prelaciones
          .filter(
            (p) =>
              p.codsede === item.codsede &&
              p.codopest === item.codopest &&
              (p.codasig === item.codasig || (item.codasig_imp && p.codasig === item.codasig_imp)),
          )
          .map((p) => p.prelacion),
      ));
      return {
        ...item,
        prelaciones: itemPrels,
      };
    });

    enrichedItems.sort(sortSubjectsCurricularly);

    return { items: enrichedItems, total };
  }

  async findOnePensum(codsede: string, codopest: string, codasig: string) {
    const record = await this.prisma.pensumEstudios.findUnique({
      where: {
        codsede_codopest_codasig: { codsede, codopest, codasig },
      },
    });
    if (!record) {
      throw new NotFoundException(
        `No se encontró la asignatura ${codasig} en el pensum de sede ${codsede} y programa ${codopest}`,
      );
    }
    return record;
  }

  async createPensum(dto: CreatePensumDto) {
    // Verificar si existe la oportunidad de estudio primero
    await this.findOneOportunidad(dto.codsede, dto.codopest);

    // Verificar si ya existe la asignatura en el pensum
    const exists = await this.prisma.pensumEstudios.findUnique({
      where: {
        codsede_codopest_codasig: {
          codsede: dto.codsede,
          codopest: dto.codopest,
          codasig: dto.codasig,
        },
      },
    });
    if (exists) {
      throw new BadRequestException(
        `La asignatura con código ${dto.codasig} ya existe en el pensum del programa.`,
      );
    }

    // Asegurar que la asignatura esté registrada en el catálogo general
    const catalogExists = await this.prisma.asignaturasCatalogo.findUnique({
      where: { codasig: dto.codasig }
    });
    if (!catalogExists) {
      await this.prisma.asignaturasCatalogo.create({
        data: {
          codasig: dto.codasig,
          asignatura: dto.asignatura || '',
          creditos: dto.creditos || 0
        }
      });
    }

    const { prelaciones, ...pensumData } = dto;

    const created = await this.prisma.pensumEstudios.create({
      data: pensumData as any,
    });

    if (prelaciones && Array.isArray(prelaciones)) {
      for (const pre of prelaciones) {
        if (pre && pre.trim()) {
          await this.prisma.pensum_prelaciones.create({
            data: {
              codsede: dto.codsede,
              codopest: dto.codopest,
              codasig: dto.codasig,
              prelacion: pre.trim(),
            },
          });
        }
      }
    }

    return created;
  }

  async updatePensum(codsede: string, codopest: string, codasig: string, dto: UpdatePensumDto) {
    await this.findOnePensum(codsede, codopest, codasig);

    const { prelaciones, ...pensumData } = dto;

    const updated = await this.prisma.pensumEstudios.update({
      where: {
        codsede_codopest_codasig: { codsede, codopest, codasig },
      },
      data: pensumData as any,
    });

    if (prelaciones && Array.isArray(prelaciones)) {
      // Borrar prelaciones viejas
      await this.prisma.pensum_prelaciones.deleteMany({
        where: { codsede, codopest, codasig },
      });

      // Crear prelaciones nuevas
      for (const pre of prelaciones) {
        if (pre && pre.trim()) {
          await this.prisma.pensum_prelaciones.create({
            data: {
              codsede,
              codopest,
              codasig,
              prelacion: pre.trim(),
            },
          });
        }
      }
    }

    return updated;
  }

  async deletePensum(codsede: string, codopest: string, codasig: string) {
    await this.findOnePensum(codsede, codopest, codasig);

    // Eliminar prelaciones asociadas
    await this.prisma.pensum_prelaciones.deleteMany({
      where: { codsede, codopest, codasig },
    });

    return this.prisma.pensumEstudios.delete({
      where: {
        codsede_codopest_codasig: { codsede, codopest, codasig },
      },
    });
  }

  async generatePensumPdf(codsede: string, codopest: string): Promise<Buffer> {
    console.log(`[AcademicoService] Generando Pensum PDF v2 para programa: "${codopest}", sede: "${codsede}"`);
    // 1. Obtener la oportunidad de estudio (programa)
    let programa = codsede
      ? await this.prisma.oportunidadesEstudio.findUnique({
          where: { codsede_codopest: { codsede, codopest } },
        })
      : null;

    if (!programa) {
      programa = await this.prisma.oportunidadesEstudio.findFirst({
        where: { codopest },
      });
    }

    if (!programa) {
      throw new NotFoundException(`No se encontró programa con código ${codopest}`);
    }

    // 2. Obtener todas las asignaturas asociadas
    let asigs = codsede
      ? await this.prisma.pensumEstudios.findMany({
          where: { codsede, codopest },
          orderBy: [{ periodos: 'asc' }, { codasig: 'asc' }],
        })
      : [];

    if (asigs.length === 0) {
      asigs = await this.prisma.pensumEstudios.findMany({
        where: { codopest },
        orderBy: [{ periodos: 'asc' }, { codasig: 'asc' }],
      });
    }

    // Deduplicar asignaturas por código visible (codasig_imp si existe y no es 'NULL', o codasig)
    const uniqueAsigMap = new Map<string, any>();
    asigs.forEach((a) => {
      const key =
        a.codasig_imp && a.codasig_imp.trim() !== '' && a.codasig_imp !== 'NULL'
          ? a.codasig_imp.trim()
          : a.codasig.trim();

      if (!uniqueAsigMap.has(key)) {
        uniqueAsigMap.set(key, a);
      } else {
        const current = uniqueAsigMap.get(key);
        // Preferir el registro con texto limpio sin caracteres dañados
        if (
          current.asignatura &&
          current.asignatura.includes('??') &&
          a.asignatura &&
          !a.asignatura.includes('??')
        ) {
          uniqueAsigMap.set(key, a);
        }
      }
    });
    const asignaturas = Array.from(uniqueAsigMap.values());

    // 3. Consultar prelaciones para este programa
    const allInternalCodes = asigs.map((a) => a.codasig);
    const allImpCodes = asigs.map((a) => a.codasig_imp).filter(Boolean) as string[];
    const queryCodes = [...new Set([...allInternalCodes, ...allImpCodes])];

    const prelaRecords = await this.prisma.pensum_prelaciones.findMany({
      where: {
        codopest,
        codasig: { in: queryCodes },
      },
    });

    // Mapeo de códigos para mostrar el código visible en las prelaciones
    const codeToDisplayMap = new Map<string, string>();
    asigs.forEach((a) => {
      const disp =
        a.codasig_imp && a.codasig_imp.trim() !== '' && a.codasig_imp !== 'NULL'
          ? a.codasig_imp.trim()
          : a.codasig.trim();
      codeToDisplayMap.set(a.codasig.trim(), disp);
      if (a.codasig_imp) codeToDisplayMap.set(a.codasig_imp.trim(), disp);
    });

    const enrichedAsignaturas = asignaturas.map((a) => {
      const displayCode =
        a.codasig_imp && a.codasig_imp.trim() !== '' && a.codasig_imp !== 'NULL'
          ? a.codasig_imp.trim()
          : a.codasig.trim();

      const matchingPrels = prelaRecords.filter((p) => {
        const pAsig = p.codasig.trim();
        return (
          pAsig === a.codasig.trim() ||
          (a.codasig_imp && pAsig === a.codasig_imp.trim()) ||
          pAsig === displayCode
        );
      });

      const uniquePrelCodes = Array.from(
        new Set(
          matchingPrels.map((p) => {
            const rawPrel = p.prelacion.trim();
            return codeToDisplayMap.get(rawPrel) || rawPrel;
          }),
        ),
      );

      return {
        ...a,
        displayCode,
        prelacionText: uniquePrelCodes.length > 0 ? uniquePrelCodes.join(', ') : '-',
      };
    });

    enrichedAsignaturas.sort(sortSubjectsCurricularly);

    // 4. Agrupar por período
    const periodsMap: { [key: string]: any[] } = {};
    enrichedAsignaturas.forEach((a) => {
      const pKey = a.periodos !== null ? `Período ${a.periodos}` : 'Sin Período Definido';
      if (!periodsMap[pKey]) periodsMap[pKey] = [];
      periodsMap[pKey].push(a);
    });

    Object.keys(periodsMap).forEach((pKey) => {
      periodsMap[pKey].sort(sortSubjectsCurricularly);
    });

    const sortedPeriods = Object.keys(periodsMap).sort((a, b) => {
      if (a.includes('Definido')) return 1;
      if (b.includes('Definido')) return -1;
      const numA = parseInt(a.replace(/[^0-9]/g, ''), 10);
      const numB = parseInt(b.replace(/[^0-9]/g, ''), 10);
      return numA - numB;
    });

    return new Promise((resolve, reject) => {
      const doc = new PDFDocument({ margin: 50, size: 'A4' });
      const buffers: Buffer[] = [];

      doc.on('data', (chunk) => buffers.push(chunk));
      doc.on('end', () => resolve(Buffer.concat(buffers)));
      doc.on('error', (err) => reject(err));

      // --- DISEÑO DEL PDF ---
      // Logo institucional
      if (fs.existsSync('logo.png')) {
        doc.image('logo.png', 50, 42, { width: 60 });
      }

      // Encabezado institucional
      doc.font('Helvetica-Bold').fillColor('#000000').fontSize(10);
      doc.text('Centro de Investigaciones Psiquiátricas, Psicológicas y', 120, 50, {
        align: 'center',
        width: 350,
      });
      doc.text('Sexológicas de Venezuela', 120, 62, { align: 'center', width: 350 });
      doc.font('Helvetica').fontSize(9);
      doc.text('Coordinación Académica', 120, 74, { align: 'center', width: 350 });
      doc.text('Oficina de Control de Estudios', 120, 86, { align: 'center', width: 350 });

      const yHeaderEnd = doc.y;

      // Título del Pensum
      doc
        .font('Helvetica-Bold')
        .fillColor('#002855')
        .fontSize(14)
        .text('PENSUM DE ESTUDIOS', 50, yHeaderEnd + 15, { align: 'center' });
      doc.moveDown(1.5);

      // Detalles del programa
      const yMeta = doc.y;
      doc.font('Helvetica-Bold').fontSize(9.5).fillColor('#000000');
      doc
        .text('Programa: ', 50, yMeta, { lineBreak: false } as any)
        .font('Helvetica')
        .text(cleanProgramName(programa.titulo_a_otorgar) || 'No especificado');
      doc
        .font('Helvetica-Bold')
        .text('Mención/Especialidad: ', 50, yMeta + 12, { lineBreak: false } as any)
        .font('Helvetica')
        .text(cleanProgramName(programa.mencion_especialidad) || 'No especificada');
      doc
        .font('Helvetica-Bold')
        .text('Tipo: ', 50, yMeta + 24, { lineBreak: false } as any)
        .font('Helvetica')
        .text(
          `${cleanHtmlEntities(programa.tipo) || 'No especificado'} | Código: ${programa.codopest}`,
        );
      doc
        .font('Helvetica-Bold')
        .text('Créditos Requeridos: ', 50, yMeta + 36, { lineBreak: false } as any)
        .font('Helvetica')
        .text(String(programa.creditos || 'No especificado'));
      doc.moveDown(2);

      // Tabla de Asignaturas
      const startX = 50;
      let currentY = doc.y;

      const drawTableHeader = (y: number) => {
        doc.fillColor('#002855').rect(startX, y, 500, 18).fill();
        doc.font('Helvetica-Bold').fillColor('#ffffff').fontSize(8.5);
        doc.text('Código', startX + 10, y + 5);
        doc.text('Nombre de la Asignatura', startX + 80, y + 5);
        doc.text('Crédito', startX + 355, y + 5, { width: 55, align: 'center' });
        doc.text('Prelación', startX + 420, y + 5, { width: 75, align: 'center' });
      };

      drawTableHeader(currentY);
      currentY += 18;

      sortedPeriods.forEach((period) => {
        const periodNotes = periodsMap[period];

        // Verificar si cabe el bloque de período
        if (currentY > 720) {
          doc.addPage();
          currentY = 50;
          drawTableHeader(currentY);
          currentY += 18;
        }

        // Título del Período
        doc.fillColor('#eef2f6').rect(startX, currentY, 500, 14).fill();
        doc.strokeColor('#cbd5e1').lineWidth(0.5);
        doc.rect(startX, currentY, 500, 14).stroke();
        doc.font('Helvetica-Bold').fontSize(8).fillColor('#1e3a8a');
        doc.text(period.toUpperCase(), startX + 10, currentY + 3.5);
        currentY += 14;

        periodNotes.forEach((asig) => {
          doc.font('Helvetica').fontSize(8);
          const nameHeight = doc.heightOfString(asig.asignatura || '', { width: 260 });
          doc.fontSize(7.5);
          const prelaHeight = doc.heightOfString(asig.prelacionText || '-', { width: 75 });
          const rowHeight = Math.max(16, Math.max(nameHeight, prelaHeight) + 6);

          if (currentY + rowHeight > 740) {
            doc.addPage();
            currentY = 50;
            drawTableHeader(currentY);
            currentY += 18;
          }

          // Fondo blanco
          doc.fillColor('#ffffff').rect(startX, currentY, 500, rowHeight).fill();

          // Textos
          doc.fillColor('#000000').font('Helvetica').fontSize(8);
          doc.text(asig.displayCode, startX + 10, currentY + 4);
          doc.text(asig.asignatura || '', startX + 80, currentY + 4, { width: 260 });
          doc.text(asig.creditos !== null ? String(asig.creditos) : '0', startX + 355, currentY + 4, {
            width: 55,
            align: 'center',
          });

          doc.fontSize(7.5);
          doc.text(asig.prelacionText || '-', startX + 420, currentY + 4, {
            width: 75,
            align: 'center',
          });

          // Líneas de celda
          doc.strokeColor('#000000').lineWidth(0.5);
          // Borde inferior
          doc.moveTo(startX, currentY + rowHeight).lineTo(startX + 500, currentY + rowHeight).stroke();

          // Líneas verticales
          doc.moveTo(startX, currentY).lineTo(startX, currentY + rowHeight).stroke();
          doc.moveTo(startX + 75, currentY).lineTo(startX + 75, currentY + rowHeight).stroke();
          doc.moveTo(startX + 350, currentY).lineTo(startX + 350, currentY + rowHeight).stroke();
          doc.moveTo(startX + 415, currentY).lineTo(startX + 415, currentY + rowHeight).stroke();
          doc.moveTo(startX + 500, currentY).lineTo(startX + 500, currentY + rowHeight).stroke();

          currentY += rowHeight;
        });

        currentY += 6;
      });

      if (currentY > 720) {
        doc.addPage();
        currentY = 50;
      }
      doc.y = currentY + 10;
      doc
        .font('Helvetica-Oblique')
        .fillColor('#444444')
        .fontSize(7.5)
        .text(
          'Reporte de pensum de estudios oficial generado por SACE. Todos los derechos reservados CIPPSV.',
          { align: 'center' },
        );

      // Finalizar documento
      doc.end();
    });
  }
}
