<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд | Моніторинг Освітлення</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="/assets/css/style.css"> 
</head>
<body class="bg-light">
    <?php include 'includes/header.php'; // Навігація ?>

    <div class="container-fluid mt-4">
        <h1 class="mb-4 text-primary">📊 Панель Моніторингу</h1>
        <p>Ласкаво просимо, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo $_SESSION['role']; ?>).</p>

        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Світильників Увімкнено</h5>
                        <p class="card-text fs-2"><?php echo $data['stats']['on_count'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Світильників Вимкнено</h5>
                        <p class="card-text fs-2"><?php echo $data['stats']['off_count'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Помилок</h5>
                        <p class="card-text fs-2"><?php echo $data['stats']['error_count'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-dark bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Споживання за добу (кВт·год)</h5>
                        <p class="card-text fs-2"><?php echo number_format($data['stats']['daily_kwh'] ?? 0, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">Карта Розташування Світильників</div>
                    <div class="card-body">
                        <div id="mapid" style="height: 400px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-info text-white">Погодинне Споживання (Сьогодні)</div>
                    <div class="card-body">
                        <canvas id="consumptionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                 <div class="card shadow mb-4">
                    <div class="card-header bg-dark text-white">Детальний Статус Світильників</div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Локація</th>
                                    <th>Статус</th>
                                    <th>Останні дані кВт·год</th>
                                    <th>Керування</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['lights'] as $light): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($light['unique_id']); ?></td>
                                    <td><?php echo htmlspecialchars($light['location_name']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php echo $light['status'] === 'ON' ? 'bg-success' : ''; ?>
                                            <?php echo $light['status'] === 'OFF' ? 'bg-secondary' : ''; ?>
                                            <?php echo $light['status'] === 'ERROR' ? 'bg-danger' : ''; ?>
                                        ">
                                            <?php echo $light['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($light['kwh_reading'] ?? 0, 4); ?> (<?php echo $light['timestamp'] ?? 'N/A'; ?>)</td>
                                    <td>
                                        <?php if ($_SESSION['role'] !== 'guest'): ?>
                                            <form method="POST" action="/lighting-control" style="display:inline;">
                                                <input type="hidden" name="light_id" value="<?php echo $light['id']; ?>">
                                                <input type="hidden" name="action" value="<?php echo $light['status'] === 'ON' ? 'OFF' : 'ON'; ?>">
                                                <button type="submit" class="btn btn-sm 
                                                    <?php echo $light['status'] === 'ON' ? 'btn-warning' : 'btn-success'; ?>"
                                                    <?php echo $light['status'] === 'ERROR' ? 'disabled' : ''; ?>
                                                >
                                                    <?php echo $light['status'] === 'ON' ? 'Викл' : 'Вкл'; ?>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Немає доступу</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; // Скрипти ?>
    
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="/assets/js/main.js"></script> 
    
    <script>
    // Ініціалізація карти та маркерів
    document.addEventListener('DOMContentLoaded', function() {
        const lightsData = <?php echo json_encode($data['lights']); ?>;
        
        // Встановлення центра карти на першу точку або середнє значення
        const map = L.map('mapid').setView([49.55, 25.59], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        lightsData.forEach(light => {
            let iconColor;
            if (light.status === 'ON') iconColor = 'green';
            else if (light.status === 'ERROR') iconColor = 'red';
            else iconColor = 'gray';

            // Приклад створення кастомної іконки (потрібна бібліотека L.ExtraMarkers або CSS)
            // Простий маркер:
            L.marker([light.latitude, light.longitude]).addTo(map)
                .bindPopup(`<b>${light.location_name}</b><br>Статус: ${light.status}<br>кВт·год: ${light.kwh_reading ?? 'N/A'}`);
        });
        
        // Ініціалізація Chart.js
        const ctx = document.getElementById('consumptionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['09:00', '10:00', '11:00', '12:00', '13:00'], // Приклад міток
                datasets: [{
                    label: 'Споживання (кВт·год)',
                    data: [0.5, 1.0, 0.0, 2.5, 1.2], // Приклад даних
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
    </script>
</body>
</html>