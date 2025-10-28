<?php

class Simulator
{
    private $status_path = __DIR__ . "/../sim-status.json";

    private $status = [
        "led" => [
            "state" => "off"
        ],
        "rgb" => [
            "state" => "off",
            "color" => [
                "r" => 0,
                "g" => 0,
                "b" => 0
            ]
        ],
        "sensor" => [
            "value" => 25
        ],
    ];

    public function __construct()
    {
        if (file_exists($this->status_path)) {
            $this->status = $this->loadStatus();
        };
    }

    /**
     * Elmenti az állapotot egy JSON fájlba
     *
     * @return void
     */
    private function saveStatus(): void
    {
        file_put_contents($this->status_path, json_encode($this->status, JSON_PRETTY_PRINT));
    }

    /**
     * Visszaolvassa az állapotot egy JSON fájlból
     *
     * @return array
     */
    private function loadStatus(): array
    {
        return json_decode(file_get_contents($this->status_path), true);
    }

    /**
     * LED státusz lekérés
     *
     * @return array
     */
    public function getLedStatus(): array
    {
        return $this->status["led"];
    }

    /**
     * LED státusz beállítása
     *
     * @param $state
     * @return array
     */
    public function setLedStatus($state): array
    {
        $this->status["led"]["state"] = $state;
        $this->saveStatus();
        return $this->getLedStatus();
    }

    /**
     * RGB LED státusz lekérés
     *
     * @return array
     */
    public function getRGBStatus(): array
    {
        return $this->status["rgb"];
    }

    /**
     * RGB LED státusz beállítása
     *
     * @param $state
     * @param $red
     * @param $green
     * @param $blue
     * @return array
     */
    public function setRGBStatus($state, $red, $green, $blue): array
    {
        $this->status["rgb"]["state"] = $state;
        if ($red != null)
            $this->status["rgb"]["color"]["r"] = intval($red);
        if ($green != null)
            $this->status["rgb"]["color"]["g"] = intval($green);
        if ($blue != null)
            $this->status["rgb"]["color"]["b"] = intval($blue);
        $this->saveStatus();
        return $this->getRGBStatus();
    }

    /**
     * Szenzor értékének kiolvasása
     *
     * @return array
     */
    public function getSensorStatus(): array
    {
        return $this->status["sensor"];
    }

    /**
     * Szenzor értékének beállítása
     *
     * @param $value
     * @return array
     */
    public function setSensorStatus($value): array
    {
        if($value != null) {
            $this->status["sensor"]["value"] = $value;
            $this->saveStatus();
        }
        return $this->getSensorStatus();
    }

    /**
     * Összes státusz lekérése
     *
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }
}