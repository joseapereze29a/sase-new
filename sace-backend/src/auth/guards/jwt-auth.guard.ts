import { Injectable, ExecutionContext, UnauthorizedException } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { AuthService } from '../auth.service';

@Injectable()
export class JwtAuthGuard extends AuthGuard('jwt') {
  constructor(private readonly authService: AuthService) {
    super();
  }

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const request = context.switchToHttp().getRequest();
    const authHeader = request.headers.authorization;

    if (authHeader && authHeader.toLowerCase().startsWith('basic ')) {
      try {
        const credentials = authHeader.substring(6);
        const decoded = Buffer.from(credentials, 'base64').toString('ascii');
        const [username, password] = decoded.split(':');

        const user = await this.authService.validateUser(username, password);
        if (!user) {
          throw new UnauthorizedException('Credenciales básicas incorrectas');
        }

        // Registrar el usuario en la request con el mismo formato que jwt
        request.user = {
          userId: user.id,
          username: user.user,
          role: user.rol,
        };
        return true;
      } catch (e) {
        if (e instanceof UnauthorizedException) {
          throw e;
        }
        throw new UnauthorizedException('Formato de autorización inválido');
      }
    }

    // Si no es Basic Auth, delegar al flujo JWT normal
    return super.canActivate(context) as Promise<boolean>;
  }
}
