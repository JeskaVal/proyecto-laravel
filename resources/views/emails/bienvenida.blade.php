<div style="font-family: Arial, sans-sefif; max-width: 600px; margin: 0 auto; color: #333;">

    {{-- Encabezado --}}

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 28px;">¡Bienvenido!</h1>
        <p style="margin: 10px 0 0; opacity: 0.9;">
            Tu cuenta ha sido creada exitosamente
        </p>
    </div>

    {{-- Cuerpo --}}
    <div style="background: white; padding: 30px; border: 1px solid #e0e0e0; border-top: none;">
        <p>Estimado(a) <strong>{{ $nombre }}</strong>,</p>

        <p>
            Tu registro en el <strong>Sistema de Denuncias Electrónico</strong> ha sido completado. A partir de ahora puedes ingresar al sistema, presentar denuncias y dar seguimiento a su estado en todo momento.
        </p>

        {{-- Datos de acceso --}}

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea">
            <p style="margin: 0 0 8px;"><strong>Datos de tu cuenta</strong></p>
            <ul style="margin: 0; padding-left: 20px; line-height: 1.8;">
                <li><strong>Correo:</strong> {{ $correo }}</li>
                <li><strong>Acceso:</strong> Con el folio y contraseña que se generó al registrar tu denuncia.</li>
            </ul>
        </div>

        {{-- ¿Qué puedes hacer? --}}
        <h3 style="color: #333; margin-top: 25px;">¿Qué puedes hacer ahora?</h3>
        <ol>
            <li>Ingresar al sistema con tu folio y contraseña</li>
            <li>Presentar una denuncia de forma anónima o con identificación</li>
            <li>Adjuntar documentos e imágenes como evidencias</li>
            <li>Consultar el estado de tus denuncias con tu folio</li>
            <li>Recibir notificaciones sobre el avance de tus casos</li>
        </ol>

        {{-- Botón CTA --}}
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $login_url }}" style="display: inline-block; background: #667eea; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 15px;">
                Ingresar al Sistema
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="font-size: 12px; color: #999; margin: 0;">
            Este es un mensaje automático. Si no usaste tu correo para este proposito, por favor ignora este correo o contáctanos de inmediato. <br>
            <strong>Sistema de Denuncias Electrónico</strong>
        </p>
    </div>
</div>