<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 1/15/19
 * Time: 1:45 PM
 */

class ReportsContext implements IReports {
    private $strategicDictionary;
    private static $instance;

    private function __construct() {
        $this->strategicDictionary['zapis-aparati'] = new ZapisAparati();
        $this->strategicDictionary['zapis-kontrolisanje'] = new ZapisKontrolisanje();
        $this->strategicDictionary['obr-38-2'] = new OBR_38_2();
        $this->strategicDictionary['obr-r08-35-1'] = new OBR_R08_35_1();
    }

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new ReportsContext();

        return self::$instance;
    }

    public function pickReport($reportType) {
        return $this->strategicDictionary[$reportType];
    }
}