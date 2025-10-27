<?php
// controllers/AnalyticsController.php
require_once '../models/DB.php';
require_once '../models/EnergyReading.php'; 

class AnalyticsController {
    protected $readingModel;

    public function __construct() {
        $this->readingModel = new EnergyReading();
    }

    public function index($period = 'week') {
        $data = [];
        
        // 1. Агреговане споживання за обраний період
        // У реальному проєкті тут була б складна агрегація SQL
        $data['total_consumption'] = $this->readingModel->getAggregatedConsumption($period);
        
        // 2. Дані для графіків (погодинно або подобово)
        $data['chart_data'] = $this->readingModel->getChartData($period);
        
        // 3. (Заглушка) Звіти - зазвичай тут генерується CSV/PDF
        // $data['report_link'] = $this->generateReport($period, 'csv');
        
        return $data;
    }
}