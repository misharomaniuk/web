<?php 
// views/analytics.php
include 'includes/header.php'; // HTML, Bootstrap, Ваш CSS
?>

<div class="container mt-4">
    <h1 class="mb-4 text-primary">📊 Аналітика та Звіти</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <form method="GET" action="/analytics">
                <label for="periodSelect" class="form-label">Обрати Період:</label>
                <select class="form-select" id="periodSelect" name="period" onchange="this.form.submit()">
                    <option value="day" <?php if(($_GET['period'] ?? 'week') === 'day') echo 'selected'; ?>>Сьогодні</option>
                    <option value="week" <?php if(($_GET['period'] ?? 'week') === 'week') echo 'selected'; ?>>Останні 7 днів</option>
                    <option value="month" <?php if(($_GET['period'] ?? 'week') === 'month') echo 'selected'; ?>>Цей місяць</option>
                </select>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Сумарне Споживання (кВт·год)</h5>
                    <p class="card-text fs-3"><?php echo number_format($data['total_consumption'] ?? 0, 2); ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 d-flex align-items-center">
            <a href="/report.php?type=csv&period=<?php echo $_GET['period'] ?? 'week'; ?>" class="btn btn-success btn-lg w-100">
                ⬇️ Згенерувати Звіт (CSV/PDF)
            </a>
        </div>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white">Графік Споживання Енергії за Період</div>
        <div class="card-body">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chartData = <?php echo json_encode($data['chart_data']); ?>;
        const period = document.getElementById('periodSelect').value;
        const chartType = (period === 'day' ? 'bar' : 'line');

        const ctx = document.getElementById('analyticsChart').getContext('2d');
        new Chart(ctx, {
            type: chartType,
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Споживання (кВт·год)',
                    data: chartData.data,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)', // Блакитний
                    borderColor: 'rgba(41, 128, 185, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: chartType === 'line' ? true : false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'КВт·год'
                        }
                    }
                }
            }
        });
    });
</script>