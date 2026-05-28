<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;">
    <div style="background: linear-gradient(135deg, #27a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="margin: 0; font-size: 28px;">📢 Actualización en tu Denuncia</h1>
    </div>

    <div style="background: white; padding: 30px; border: 1px solid #e0e0e0;">
        <p>Estimado(a) usuario,</p>

        <p>Tu denuncia ha sido actualizada. A continuación, se muestran los detalles:</p>

        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;">
            <p style="margin: 0 0 10px;"><strong>Folio</strong> {{ $denuncia->folio }}</p>
            <p style="margin: 0 0 10px;"><strong>Acción:</strong> {{ $accion }}</p>
            <p style="margin: 0;"><strong>Descripción:</strong></p>
            <p style="margin: 10px 0 0; color: #555;">{{ $descripcion }}</p>
        </div>

        <div style="background: #f0f7ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0 0 10;"><strong>Estado Actual:</strong>
                <span style="background: #e7f3ff; padding: 4px 10px; border-radius: 4px; color: #0066cc;">
                    {{ ucfirst(str_replace('_', ' ', $denuncia->estado)) }}
                </span>
            </p>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $consulta_url }}" style="display: inline-block; background: #28a745; color: white; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                Ver Detalles de mi Denuncia
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e0e0e0; margin: 30px 0;">

        <p style="font-size: 12px; color: #999; margin: 0;">
            Este es un mensaje automático. Por favor, no respondas a este correo.
        </p>
    </div>
</div>