<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cita</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .navbar { background-color: rgba(0,0,0,0.1); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-radius: 10px; }
        .navbar a { color: white; text-decoration: none; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        h2 { color: #333; margin-bottom: 20px; text-align: center; }
        .errors { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .errors ul { margin: 0; padding-left: 20px; }
        .info-box { background-color: #e7f3ff; border-left: 4px solid #667eea; padding: 14px; margin-bottom: 20px; border-radius: 4px; color: #004085; font-size: 13px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 500; font-size: 14px; }
        select, input[type="datetime-local"], textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.3s; }
        select:focus, input:focus, textarea:focus { outline: none; border-color: #667eea; box-shadow: 0 0 5px rgba(102,126,234,0.3); }
        textarea { resize: vertical; min-height: 100px; }
        .error-msg { color: #dc3545; font-size: 12px; margin-top: 5px; }
        .service-details { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; display: none; }
        .service-details.show { display: block; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .detail-row:last-child { margin-bottom: 0; }
        .detail-label { color: #666; }
        .detail-value { font-weight: bold; color: #333; }
        .btn-group { display: flex; gap: 10px; margin-top: 25px; }
        .btn { flex: 1; padding: 12px; border: none; border-radius: 5px; font-size: 15px; font-weight: bold; cursor: pointer; transition: all 0.3s; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .btn-save { background-color: #667eea; color: white; }
        .btn-save:hover { background-color: #764ba2; }
        .btn-cancel { background-color: #6c757d; color: white; }
        .btn-cancel:hover { background-color: #5a6268; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Editar Cita</h1>
        <a href="{{ route('appointment.show', $appointment->id) }}">← Ver cita</a>
    </div>

    <div class="container">
        <h2>✏️ Editar Cita</h2>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="info-box">
            Solo puedes editar citas que aún no han sido confirmadas.
        </div>

        <form action="{{ route('appointment.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="service_id">Servicio *</label>
                <select id="service_id" name="service_id" required onchange="showServiceDetails()">
                    <option value="">-- Selecciona un servicio --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}"
                                data-price="{{ $service->price }}"
                                data-duration="{{ $service->duration }}"
                                {{ old('service_id', $appointment->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} — ${{ number_format($service->price, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('service_id') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="service-details" id="serviceDetails">
                <div class="detail-row">
                    <span class="detail-label">Duración:</span>
                    <span class="detail-value" id="duration">—</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Precio:</span>
                    <span class="detail-value" id="price">—</span>
                </div>
            </div>

            <div class="form-group">
                <label for="appointment_date">Fecha y hora *</label>
                <input type="datetime-local"
                       id="appointment_date"
                       name="appointment_date"
                       value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d\TH:i')) }}"
                       required>
                @error('appointment_date') <span class="error-msg">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notas adicionales</label>
                <textarea id="notes" name="notes"
                          placeholder="Información adicional sobre tu cita...">{{ old('notes', $appointment->notes) }}</textarea>
                <small style="color:#999; margin-top:5px; display:block;">Máximo 500 caracteres</small>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-save">💾 Guardar Cambios</button>
                <a href="{{ route('appointment.show', $appointment->id) }}" class="btn btn-cancel">← Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function showServiceDetails() {
            const select = document.getElementById('service_id');
            const option = select.options[select.selectedIndex];
            const details = document.getElementById('serviceDetails');
            if (option.value) {
                document.getElementById('duration').textContent = option.dataset.duration + ' minutos';
                document.getElementById('price').textContent = '$' + parseFloat(option.dataset.price).toFixed(2);
                details.classList.add('show');
            } else {
                details.classList.remove('show');
            }
        }
        // Mostrar detalles del servicio actual al cargar
        window.addEventListener('load', showServiceDetails);
    </script>
</body>
</html>
