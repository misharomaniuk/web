<?php
// models/StreetLight.php
require_once 'DB.php';

class StreetLight {
    private $db;

    public function __construct() {
        $this->db = DB::getInstance()->getConnection();
    }

    /**
     * Отримати всі світильники.
     * @return array
     */
    public function getAllLights() {
        $stmt = $this->db->query("SELECT * FROM street_lights");
        return $stmt->fetchAll();
    }

    /**
     * Оновити статус світильника (ON/OFF/ERROR).
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE street_lights SET status = :status, last_update = NOW() WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Отримати загальну статистику для дашборду.
     * @return array
     */
    public function getDashboardStats() {
        // Кількість світильників за статусом
        $stats = $this->db->query("SELECT status, COUNT(*) as count FROM street_lights GROUP BY status")->fetchAll();
        $result = [
            'on_count' => 0, 
            'off_count' => 0, 
            'error_count' => 0, 
            'daily_kwh' => 0
        ];
        foreach ($stats as $stat) {
            $key = strtolower($stat['status']) . '_count';
            $result[$key] = $stat['count'];
        }

        // Сума споживання за останню добу
        $stmt = $this->db->query("SELECT SUM(kwh_reading) FROM energy_readings WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
        $result['daily_kwh'] = $stmt->fetchColumn() ?? 0;
        
        return $result;
    }
    
    /**
     * Отримати світильники з останніми показниками.
     * Використовується для дашборду.
     * @return array
     */
    public function getLightsWithLatestReadings() {
        // Складний запит для ефективного отримання останнього показника для кожного світильника
        $sql = "SELECT 
                    sl.*, 
                    er.kwh_reading, 
                    er.timestamp 
                FROM street_lights sl
                LEFT JOIN energy_readings er 
                ON sl.id = er.light_id
                WHERE er.id IN (
                    SELECT MAX(id) 
                    FROM energy_readings 
                    GROUP BY light_id
                ) OR er.id IS NULL";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}