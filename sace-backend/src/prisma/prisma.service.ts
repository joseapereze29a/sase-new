import { Injectable, OnModuleInit, OnModuleDestroy } from '@nestjs/common';
import { PrismaClient } from '@prisma/client';

function fixEncoding(str: string): string {
  let decoded = str
    .replace(/&aacute;/g, 'á')
    .replace(/&eacute;/g, 'é')
    .replace(/&iacute;/g, 'í')
    .replace(/&oacute;/g, 'ó')
    .replace(/&uacute;/g, 'ú')
    .replace(/&ntilde;/g, 'ñ')
    .replace(/&Aacute;/g, 'Á')
    .replace(/&Eacute;/g, 'É')
    .replace(/&Iacute;/g, 'Í')
    .replace(/&Oacute;/g, 'Ó')
    .replace(/&Uacute;/g, 'Ú')
    .replace(/&Ntilde;/g, 'Ñ')
    .replace(/&ldquo;/g, '"')
    .replace(/&rdquo;/g, '"')
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&');

  if (/Ã/.test(decoded)) {
    try {
      const bytes = new Uint8Array(decoded.split('').map((c) => c.charCodeAt(0)));
      const utf8Decoded = new TextDecoder('utf-8').decode(bytes);
      if (!utf8Decoded.includes('\uFFFD')) {
        decoded = utf8Decoded;
      }
    } catch (e) {
      // ignore
    }
  }
  return decoded;
}

function cleanObject(obj: any): any {
  if (obj === null || obj === undefined) return obj;
  if (typeof obj === 'string') {
    return fixEncoding(obj);
  }
  if (Array.isArray(obj)) {
    return obj.map((item) => cleanObject(item));
  }
  if (typeof obj === 'object') {
    if (obj instanceof Date) return obj;
    const cleaned: any = {};
    for (const key of Object.keys(obj)) {
      cleaned[key] = cleanObject(obj[key]);
    }
    return cleaned;
  }
  return obj;
}

@Injectable()
export class PrismaService extends PrismaClient implements OnModuleInit, OnModuleDestroy {
  async onModuleInit() {
    await this.$connect();

    this.$use(async (params: any, next: any) => {
      const result = await next(params);
      return cleanObject(result);
    });
  }

  async onModuleDestroy() {
    await this.$disconnect();
  }
}
