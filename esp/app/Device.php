<?php

class Device
{
    /**
     * @var string
     */
    private $hostname;

    public function __construct($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @throws Exception
     */
    private function get($path)
    {
        // URl összeállítása
        $url = 'http://' . $this->hostname . '/' . $path;

        // Inicializálás
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Kérés elküldése
        $response = curl_exec($ch);

        // Hibakezelés
        if (curl_errno($ch)) {
            throw new Exception('Hiba: ' . curl_error($ch));
        }

        // Kapcsolat lezárása
        curl_close($ch);

        // JSON konvertálás
        return json_decode($response, true);
    }

    /**
     * @throws Exception
     */
    private function post($path, $data = null)
    {
        // URL összeállítás
        $url = 'http://' . $this->hostname . '/' . $path;
        if ($data != null) {
            $url .= '?' . http_build_query($data);
        }

        // Inicializálás
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);

        // Kérés elküldése
        curl_exec($ch);

        // Hibakezelés
        if (curl_errno($ch)) {
            throw new Exception('Hiba: ' . curl_error($ch));
        }

        // Kapcsolat lezárása
        curl_close($ch);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPhotoresistorStatus(): array
    {
        $response = $this->get("sensor/photoresistor");
        return [
            'state' => $response['value'] / 100
        ];
    }

    /**
     * LED státusz lekérés
     *
     * @return array
     * @throws Exception
     */
    public function getLedStatus(): array
    {
        $response = $this->get("switch/on-board_led");
        return [
            'state' => strtolower($response['state'])
        ];
    }

    /**
     * LED státusz beállítása
     *
     * @param $state
     * @return array
     * @throws Exception
     */
    public function setLedStatus($state): array
    {
        $this->post("switch/on-board_led/turn_$state");

        return $this->getLedStatus();
    }

    /**
     * RGB státusz lekérés
     *
     * @return array
     * @throws Exception
     */
    public function getRGBStatus(): array
    {
        $response = $this->get("light/on-board_rgb");
        return [
            'state' => strtolower($response['state']),
            'color' => [
                'red' => $response['color']['r'],
                'green' => $response['color']['g'],
                'blue' => $response['color']['b'],
            ]
        ];
    }

    /**
     * RGB státusz beállítása
     *
     * @param $state
     * @param $red
     * @param $green
     * @param $blue
     * @return array
     * @throws Exception
     */
    public function setRGBStatus($state, $red, $green, $blue): array
    {
        $this->post("light/on-board_rgb/turn_$state", [
            'r' => $red,
            'g' => $green,
            'b' => $blue,
            'transition' => 0
        ]);

        return $this->getRGBStatus();
    }

}