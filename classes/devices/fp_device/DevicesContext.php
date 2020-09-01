<?php
/**
 * Created by PhpStorm.
 * User: sentinel
 * Date: 11/1/18
 * Time: 1:23 PM
 */

class DevicesContext implements IDevices {
    private $strategicDictionary;
    private static $instance;

    private function __construct() {
        $this->strategicDictionary['TIP S'] = new PPAparat();
        $this->strategicDictionary['TIP CO2'] = new PPAparat();
        $this->strategicDictionary['TIP HL'] = new PPAparat();
        $this->strategicDictionary['TIP NAF'] = new PPAparat();
        $this->strategicDictionary['TIP Foxer'] = new PPAparat();
        $this->strategicDictionary['TIP Pz'] = new PPAparat();
        $this->strategicDictionary['TIP Fe36kg'] = new PPAparat();
        $this->strategicDictionary['CeilingFE'] = new PPAparat();
        $this->strategicDictionary['Hydrants'] = new Hidrant();
        $this->strategicDictionary['UPP'] = new UPP();
    }

    public static function getInstance() {
        if (self::$instance === null)
            self::$instance = new DevicesContext();

        return self::$instance;
    }

    public function pickDevice($deviceType) {
        return $this->strategicDictionary[$deviceType];
    }

}