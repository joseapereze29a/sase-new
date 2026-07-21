import { Injectable, UnauthorizedException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import * as bcrypt from 'bcrypt';
import { JwtService } from '@nestjs/jwt';
import { RegisterDto } from './dto/register.dto';
import { LoginDto } from './dto/login.dto';
import { Role } from './enums/role.enum';

@Injectable()
export class AuthService {
  constructor(
    private readonly prisma: PrismaService,
    private readonly jwtService: JwtService,
  ) {}

  async register(dto: RegisterDto) {
    const hashed = await bcrypt.hash(dto.password, Number(process.env.BCRYPT_ROUNDS) || 10);
    const user = await this.prisma.usuariosSace.create({
      data: {
        user: dto.username,
        pass: hashed,
        rol: dto.role || Role.ESTUDIANTE,
        cedula: dto.cedula || null,
        usuario: dto.usuario || dto.username,
      },
    });
    const { pass, ...result } = user;
    return result;
  }

  async validateUser(username: string, pass: string) {
    const user = await this.prisma.usuariosSace.findFirst({
      where: { user: username },
      include: { datosPersonales: true },
    });
    if (!user || !user.pass) return null;

    let isMatch = false;
    let needsUpgrade = false;

    if (user.pass.startsWith('$2')) {
      isMatch = await bcrypt.compare(pass, user.pass);
    } else {
      isMatch = pass === user.pass;
      needsUpgrade = isMatch;
    }

    if (!isMatch) return null;

    if (needsUpgrade) {
      const hashed = await bcrypt.hash(pass, Number(process.env.BCRYPT_ROUNDS) || 10);
      await this.prisma.usuariosSace.update({
        where: { id: user.id },
        data: { pass: hashed },
      });
    }

    const { pass: password, ...result } = user;
    return result;
  }

  async login(dto: LoginDto) {
    const user = await this.validateUser(dto.username, dto.password);
    if (!user) {
      throw new UnauthorizedException('Credenciales inválidas');
    }

    const payload = {
      sub: user.id,
      username: user.user,
      role: user.rol,
      cedula: user.cedula,
    };

    return {
      access_token: this.jwtService.sign(payload),
      user: {
        id: user.id,
        username: user.user,
        nombre_display: user.usuario,
        rol: user.rol,
        cedula: user.cedula,
        // Datos personales si están vinculados
        perfil: user.datosPersonales
          ? {
              nombres: user.datosPersonales.nombres,
              apellidos: user.datosPersonales.apellidos,
              email: user.datosPersonales.email,
              telefono_celular: user.datosPersonales.telefono_celular,
            }
          : null,
      },
    };
  }

  /**
   * Retorna el perfil completo del usuario autenticado,
   * incluyendo todos sus datos personales vinculados.
   */
  async getProfile(userId: number) {
    const user = await this.prisma.usuariosSace.findUnique({
      where: { id: userId },
      include: { datosPersonales: true },
    });
    if (!user) throw new UnauthorizedException('Usuario no encontrado');
    return {
      userId: user.id,
      username: user.user,
      role: user.rol,
      cedula: user.cedula,
      nombre_display: user.usuario || user.user,
      perfil: user.datosPersonales
        ? {
            nombres: user.datosPersonales.nombres,
            apellidos: user.datosPersonales.apellidos,
            email: user.datosPersonales.email,
            telefono_celular: user.datosPersonales.telefono_celular,
          }
        : null,
    };
  }

  async findAllUsers() {
    return this.prisma.usuariosSace.findMany({
      orderBy: { id: 'asc' },
      select: {
        id: true,
        user: true,
        usuario: true,
        cedula: true,
        rol: true,
      },
    });
  }

  async updateUser(id: number, dto: any) {
    const updateData: any = {};
    if (dto.username) updateData.user = dto.username;
    if (dto.password) {
      updateData.pass = await bcrypt.hash(dto.password, Number(process.env.BCRYPT_ROUNDS) || 10);
    }
    if (dto.role !== undefined) updateData.rol = dto.role;
    if (dto.cedula !== undefined) updateData.cedula = dto.cedula || null;
    if (dto.usuario) updateData.usuario = dto.usuario;

    const updated = await this.prisma.usuariosSace.update({
      where: { id },
      data: updateData,
    });

    const { pass, ...result } = updated;
    return result;
  }

  async deleteUser(id: number) {
    return this.prisma.usuariosSace.delete({
      where: { id },
    });
  }

  async checkCedula(cedula: number) {
    const [dp, user] = await Promise.all([
      this.prisma.datosPersonales.findUnique({
        where: { cedula },
        select: { nombres: true, apellidos: true, cedula: true },
      }),
      this.prisma.usuariosSace.findFirst({
        where: {
          OR: [
            { cedula },
            { user: String(cedula) },
          ],
        },
        select: { id: true, user: true, usuario: true, rol: true, cedula: true },
      }),
    ]);

    return {
      existsInDatosPersonales: !!dp,
      datosPersonales: dp,
      existsInUsuariosSace: !!user,
      usuarioSace: user,
    };
  }
}
