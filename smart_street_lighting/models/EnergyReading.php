<?php
// models/EnergyReading.php
require_once 'DB.php';

class EnergyReading {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
    }

    /**
     * Отримати сумарне споживання за обраний період.
     * @param string $period ('day', 'week', 'month')
     * @return float
     */
    public function getAggregatedConsumption($period) {
        $interval = match($period) {
            'day' => '24 HOUR',
            'week' => '7 DAY',
            'month' => '30 DAY',
            default => '7 DAY',
        };
        
        $stmt = $this->db->prepare("SELECT SUM(kwh_reading) as total FROM energy_readings WHERE timestamp >= DATE_SUB(NOW(), INTERVAL " . $interval . ")");
        $stmt->execute();
        return $stmt->fetchColumn() ?? 0;
    }

    /**
     * Отримати дані для графіка споживання.
     * У цьому спрощеному прикладі повертаємо статичні дані.
     * У реальному проєкті тут була б динамічна SQL-агрегація.
     * @param string $period
     * @return array
     */
    public function getChartData($period) {
        if ($period === 'week') {
            return [
                'labels' => ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Нд'],
                'data' => [50.2, 55.8, 60.1, 49.5, 70.3, 40.0, 35.5] 
            ];
        }
        if ($period === 'month') {
             return [
                'labels' => ['Тиж. 1', 'Тиж. 2', 'Тиж. 3', 'Тиж. 4'],
                'data' => [250.2, 280.8, 310.1, 290.5] 
            ];
        }
        // За день (погодинно)
        return [
            'labels' => ['00', '03', '06', '09', '12', '15', '18', '21'],
            'data' => [0.1, 0.2, 5.5, 10.1, 15.0, 12.5, 20.9, 8.8] 
        ];
    }
}