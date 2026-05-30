<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 28px;">✓ Denuncia Recibida</h1>
        <p style="margin: 10px 0 0; opacity: 0.9;">Tu denuncia ha sido registrada en el sistema</p>
    </div>

    <div style="background: white; padding: 30px; border: 1px solid #e0e0e0;">
        <p>Estimado(a) usuario,</p>

        <p>Agradecemos tu participación en la denuncia de hechos irregulares. Tu denuncia ha sido recibida correctamente en nuestro sistema.</p>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea;">
            <p style="margin: 0 0 10px;"><strong>Folio de Referencia:</strong></p>
            <p style="margin: 0; font-size: 20px; font-family: 'Courier New', monospace; letter-spacing: 2px; font-weight: bold; color: #667eea;">
                {{ $denuncia->folio }}
            </p>
            <small style="color: #666;">Guarda este número para consultar el estado de tu denuncia</small>
        </div>

        <div style="background: #fff8e1; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <p style="margin: 0 0 10px;"><strong style="color: #856404;">🔑 Contraseña de Acceso:</strong></p>
            <p style="margin: 0 0 10px; font-size: 22px; font-family: 'Courier New', monospace; letter-spacing: 3px; font-weight: bold; color: #d32f2f; background: white; padding: 10px 15px; border-radius: 6px; border: 2px solid #ffc107; display: inline-block;">
                {{ $contrasena }}
            </p>
            <br>
            <small style="color: #856404; font-weight: 600;">
                ⚠️ Guarda esta contraseña en un lugar seguro. La necesitarás para consultar tu denuncia. No podrá ser recuperada.
            </small>
        </div>

        <div style="background: #f0f7ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 10px;"><strong>Detalles de tu Denuncia:</strong></p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Asunto:</strong> {{ $denuncia->titulo_denuncia }}</li>
                <li><strong>Fecha de Recepción:</strong> {{ $denuncia->fecha_recibida->format('d/m/Y H:i') }}</li>
                <li><strong>Estado:</strong> Recibida</li>
            </ul>
        </div>

        <h3 style="color: #333; margin-top: 25px;">Próximos Pasos:</h3>
        <ol style="color:#555; line-height: 1.8;">
            <li>Tu denuncia será revisada por nuestro equipo de validación</li>
            <li>Recibirás actualizaciones en este correo electrónico</li>
            <li>El proceso de revisión puede tomar entre 5-10 días hábiles</li>
            <li>Puedes consultar el estado en cualquier momento usando tu folio y contraseña de acceso</li>
        </ol>

        <div style="text-align: center; margin: 30px 0">
            <a href="{{ $acuse_url }}" style="display: inline-block; background: #667eea; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                Consultar Estado de tu Denuncia
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="font-size: 12px; color: #999; margin: 0;">
            Este es un mensaje automático. Por favor, no respondas a este correo. Si tienes preguntas, contacta con nosotros a través del portal.
        </p>
    </div>

    <div style="background: #f5f5f5; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; font-size: 12px; color: #666;">
        <p style="margin: 0;">Sistema de Denuncias Electrónico © 2026</p>
        <p style="margin: 5px 0 0;">Todos los derechos reservados</p>
    </div>
</div>
