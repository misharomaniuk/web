<?php 
// views/control.php 
// Використовує Bootstrap 5

include 'includes/header.php'; // HTML, Bootstrap, Ваш CSS

$lights = $data['lights']; 
?>

<div class="container mt-4">
    <h1 class="mb-4 text-primary">💡 Керування Вуличним Освітленням</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">Статус та Керування Світильниками</div>
                <div class="card-body">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Локація</th>
                                <th>Потужність (Вт)</th>
                                <th>Статус</th>
                                <th>Дії</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lights as $light): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($light['unique_id']); ?></td>
                                <td><?php echo htmlspecialchars($light['location_name']); ?></td>
                                <td><?php echo number_format($light['power_watts'], 0); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php echo $light['status'] === 'ON' ? 'bg-success' : ''; ?>
                                        <?php echo $light['status'] === 'OFF' ? 'bg-secondary' : ''; ?>
                                        <?php echo $light['status'] === 'ERROR' ? 'bg-danger' : ''; ?>
                                    ">
                                        <?php echo $light['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($_SESSION['role'] === 'administrator' || $_SESSION['role'] === 'operator'): ?>
                                        <form method="POST" action="/lighting-control" style="display:inline;">
                                            <input type="hidden" name="light_id" value="<?php echo $light['id']; ?>">
                                            <input type="hidden" name="action" value="<?php echo $light['status'] === 'ON' ? 'OFF' : 'ON'; ?>">
                                            <button type="submit" class="btn btn-sm 
                                                <?php echo $light['status'] === 'ON' ? 'btn-warning' : 'btn-success'; ?>"
                                                <?php echo $light['status'] === 'ERROR' ? 'disabled' : ''; ?>
                                            >
                                                <?php echo $light['status'] === 'ON' ? 'ВИМКНУТИ' : 'УВІМКНУТИ'; ?>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Тільки перегляд</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">Налаштування Графіків Освітлення</div>
                <div class="card-body">
                    <form action="/lighting-control" method="POST">
                        <p class="text-muted">Тут можна створити графік для групи або окремого світильника.</p>
                        
                        <div class="mb-3">
                            <label for="selectLight" class="form-label">Світильник/Група</label>
                            <select class="form-select" id="selectLight" name="light_id">
                                <option value="group_all">Всі світильники</option>
                                <?php foreach ($lights as $light): ?>
                                    <option value="<?php echo $light['id']; ?>"><?php echo htmlspecialchars($light['location_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="startTime" class="form-label">Час початку (Ввімк)</label>
                            <input type="time" class="form-control" id="startTime" name="start_time" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="endTime" class="form-label">Час кінця (Вимк)</label>
                            <input type="time" class="form-control" id="endTime" name="end_time" required>
                        </div>
                        
                        <button type="submit" name="action" value="set_schedule" class="btn btn-primary w-100"
                            <?php echo $_SESSION['role'] !== 'administrator' ? 'disabled' : ''; ?>>
                            Зберегти Графік
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>