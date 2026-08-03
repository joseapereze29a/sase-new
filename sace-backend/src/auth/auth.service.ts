import { Injectable, UnauthorizedException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import * as bcrypt from 'bcrypt';
import { JwtService } from '@nestjs/jwt';
import * as fs from 'fs';
import * as path from 'path';
import * as nodemailer from 'nodemailer';
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

  async firstTimeStudent(dto: { cedula: number; email?: string }) {
    const student = await this.prisma.datosPersonales.findUnique({
      where: { cedula: dto.cedula },
    });

    if (!student) {
      throw new BadRequestException('Por favor comuniquese con control de estudio.');
    }

    const registeredEmail = student.email?.trim();

    if (!registeredEmail && !dto.email?.trim()) {
      return {
        status: 'NEED_EMAIL',
        message: 'El estudiante existe pero no tiene correo registrado.',
      };
    }

    const emailToUse = (dto.email?.trim() || registeredEmail) as string;

    if (dto.email?.trim() && dto.email.trim() !== registeredEmail) {
      await this.prisma.datosPersonales.update({
        where: { cedula: dto.cedula },
        data: { email: dto.email.trim() },
      });
    }

    const tempPassword = Math.random().toString(36).substring(2, 8).toUpperCase();
    const hashedPass = await bcrypt.hash(tempPassword, Number(process.env.BCRYPT_ROUNDS) || 10);

    const usernameStr = String(dto.cedula);
    const existingUser = await this.prisma.usuariosSace.findFirst({
      where: { user: usernameStr },
    });

    if (existingUser) {
      await this.prisma.usuariosSace.update({
        where: { id: existingUser.id },
        data: {
          pass: hashedPass,
          rol: Role.ESTUDIANTE,
          cedula: dto.cedula,
          usuario: `${student.nombres} ${student.apellidos}`.substring(0, 50),
        },
      });
    } else {
      await this.prisma.usuariosSace.create({
        data: {
          user: usernameStr,
          pass: hashedPass,
          rol: Role.ESTUDIANTE,
          cedula: dto.cedula,
          usuario: `${student.nombres} ${student.apellidos}`.substring(0, 50),
        },
      });
    }

    let emailSent = false;
    let errorMsg = '';
    try {
      const transporter = nodemailer.createTransport({
        host: process.env.SMTP_HOST || 'smtp.gmail.com',
        port: Number(process.env.SMTP_PORT) || 587,
        secure: process.env.SMTP_SECURE === 'true',
        auth: {
          user: process.env.SMTP_USER || '',
          pass: process.env.SMTP_PASS || '',
        },
      });

      const mailOptions = {
        from: process.env.SMTP_FROM || '"SACE CIPPSV" <no-reply@cippsv.com.ve>',
        to: emailToUse,
        subject: 'Acceso al SACE - Contraseña Provisional',
        html: `
          <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;">
            <h2 style="color: #1e3a8a; text-align: center;">Acceso al SACE (CIPPSV)</h2>
            <p>Estimado(a) <strong>${student.nombres} ${student.apellidos}</strong>,</p>
            <p>Se ha generado una contraseña provisional para tu primer acceso al módulo de estudiantes del SACE.</p>
            <div style="background-color: #f1f5f9; padding: 15px; border-radius: 6px; text-align: center; margin: 20px 0;">
              <span style="font-size: 18px; font-weight: bold; letter-spacing: 2px; color: #0f172a;">${tempPassword}</span>
            </div>
            <p>Puedes iniciar sesión ingresando a <a href="https://sace.cippsv.com.ve" target="_blank">sace.cippsv.com.ve</a> con las siguientes credenciales:</p>
            <ul>
              <li><strong>Usuario:</strong> ${dto.cedula}</li>
              <li><strong>Contraseña:</strong> ${tempPassword}</li>
            </ul>
            <p style="color: #ef4444; font-size: 12px; margin-top: 20px;">* Recuerda cambiar tu contraseña una vez que ingreses al sistema por motivos de seguridad.</p>
            <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 25px 0;">
            <p style="font-size: 11px; color: #64748b; text-align: center;">Centro de Investigaciones Psiquiátricas, Psicológicas y Sexológicas de Venezuela</p>
          </div>
        `,
      };

      if (process.env.SMTP_USER) {
        await transporter.sendMail(mailOptions);
        emailSent = true;
      } else {
        const logContent = `[${new Date().toISOString()}] EMAIL TO: ${emailToUse}\nSUBJECT: Acceso al SACE - Contraseña Provisional\nBODY: Temp Password: ${tempPassword}\n\n`;
        fs.appendFileSync(path.join(__dirname, '..', '..', 'mail_debug.log'), logContent);
        emailSent = true;
      }
    } catch (err: any) {
      errorMsg = err.message;
      const logContent = `[${new Date().toISOString()}] ERROR SENDING TO: ${emailToUse} (${err.message}). Temp Password: ${tempPassword}\n\n`;
      fs.appendFileSync(path.join(__dirname, '..', '..', 'mail_debug.log'), logContent);
    }

    return {
      status: 'SUCCESS',
      email: emailToUse,
      emailSent,
      errorMsg: errorMsg || null,
      message: emailSent 
        ? `Se ha enviado la contraseña provisional a su correo: ${emailToUse}` 
        : `Se ha generado la clave provisional, pero hubo un problema al enviar el correo (Clave: ${tempPassword}).`,
    };
  }
}
