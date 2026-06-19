<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.1);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-radius: 10px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .errors {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        select, input[type="date"], input[type="time"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        select:focus, input[type="date"]:focus, input[type="time"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .service-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
        }

        .service-details.show {
            display: block;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            color: #666;
            font-size: 14px;
        }

        .detail-value {
            color: #333;
            font-weight: bold;
        }

        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            resize: vertical;
            min-height: 100px;
        }

        textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit {
            background-color: #667eea;
            color: white;
        }

        .btn-submit:hover {
            background-color: #764ba2;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }

        .info-box {
            background-color: #e7f3ff;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #004085;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Agendar Cita</h1>
        <a href="{{ route('appointment.index') }}">← Mis Citas</a>
    </div>

    <div class="container">
        <h2>📅 Agendar Nueva Cita</h2>

        @if ($errors->any())
            <div class="errors">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="info-box">
            Selecciona un servicio, fecha y hora disponible para agendar tu cita.
        </div>

        <form action="{{ route('appointment.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="service_id">Selecciona Servicio *</label>
                <select id="service_id" name="service_id" required onchange="showServiceDetails()">
                    <option value="">-- Selecciona un servicio --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-duration="{{ $service->duration }}">
                            {{ $service->name }} - ${{ number_format($service->price, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="service-details" id="serviceDetails">
                <div class="detail-row">
                    <span class="detail-label">Duración:</span>
                    <span class="detail-value" id="duration">-</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Precio:</span>
                    <span class="detail-value" id="price">-</span>
                </div>
            </div>

            <div class="form-group">
                <label for="appointment_date">Fecha y Hora *</label>
                <input
                    type="datetime-local"
                    id="appointment_date"
                    name="appointment_date"
                    required
                >
                @error('appointment_date')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="notes">Notas Adicionales</label>
                <textarea
                    id="notes"
                    name="notes"
                    placeholder="Comparte alguna información adicional sobre tu cita..."
                >{{ old('notes') }}</textarea>
                <small style="color: #999;">Máximo 500 caracteres</small>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-submit">📅 Agendar Cita</button>
                <a href="{{ route('appointment.index') }}" class="btn-cancel" style="text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">← Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        function showServiceDetails() {
            const select = document.getElementById('service_id');
            const option = select.options[select.selectedIndex];
            const details = document.getElementById('serviceDetails');

            if (option.value) {
                const duration = option.dataset.duration;
                const price = option.dataset.price;

                document.getElementById('duration').textContent = duration + ' minutos';
                document.getElementById('price').textContent = '$' + parseFloat(price).toFixed(2);
                details.classList.add('show');
            } else {
                details.classList.remove('show');
            }
        }
    </script>
</body>
</html>
