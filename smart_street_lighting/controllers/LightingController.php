<?php
// controllers/LightingController.php
require_once __DIR__ . '/../config/database.php'; 
require_once __DIR__ . '/../models/StreetLight.php';
// require_once '../models/Schedule.php'; // Додайте цю модель пізніше

class LightingController {
    protected $lightModel;

    public function __construct() {
        $this->lightModel = new StreetLight();
    }

    public function index() {
        // Отримати всі світильники та їх графіки (якщо реалізовано)
        $lights = $this->lightModel->getAllLights();
        
        // У реальному застосунку тут були б графіки
        // $schedules = (new Schedule())->getAllSchedules(); 
        
        return [
            'lights' => $lights,
            'schedules' => [] // Заглушка для графіків
        ];
    }

    /**
     * Обробляє запит на ввімкнення/вимкнення світильника.
     */
    public function handleToggle($lightId, $action) {
        $status = strtoupper($action) === 'ON' ? 'ON' : 'OFF';
        
        // Перевірка прав
        if ($_SESSION['role'] === 'administrator' || $_SESSION['role'] === 'operator') {
            return $this->lightModel->updateStatus($lightId, $status);
        }
        
        return false;
    }
}