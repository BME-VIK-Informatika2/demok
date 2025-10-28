<?php
require 'Database.php';
require 'Device.php';

class Application
{
    /**
     * @var array
     */
    private $env;

    /**
     * @var Database
     */
    private $database;
    /**
     * @var Device
     */
    private $device;

    function __construct()
    {
        // Környezeti változók betöltése
        $this->initEnv();

        // Adatbázis kapcsolat
        $db = $this->env['db'];
        $this->database = new Database($db['host'], $db['port'], $db['user'], $db['pass'], $db['name']);

        // ESP eszköz kapcsolat
        $dev = $this->env['device'];
        $this->device = new Device($dev['host']);
    }

    /**
     * Betölti a környezeti változókat a .env fájlból, ha az létezik
     *
     * @return void
     */
    private function initEnv(): void
    {
        $env = [];
        if(file_exists(__DIR__ . '/../.env'))
            $env = parse_ini_file(__DIR__ . '/../.env');
        $this->env = [
            "db" => [
                "host" => $env["DB_HOST"] ?? "localhost",
                "port" => $env["DB_POST"] ?? "3306",
                "user" => $env["DB_USER"] ?? "root",
                "pass" => $env["DB_PASS"] ?? "",
                "name" => $env["DB_NAME"] ?? "esp"],
            "device" => [
                "host" => $env["DEVICE_HOST"] ?? "localhost",
            ]
        ];
    }

    /**
     * Inicializálja az adatbázist az alkalmazás első elindulásakor
     *
     * @throws Exception
     */
    public function initDatabase()
    {
        $this->database->init();
    }

    /**
     * Frissíti a photoresistor állapotát az adatbázisban
     *
     * @throws Exception
     */
    public function updatePhotoresistorStatus()
    {
        $value = $this->device->getPhotoresistorStatus()['state'];
        $this->database->insertStatusValue($value);
        return $value;
    }

    /**
     * Lekéri a photoresistor állapotát
     *
     * @param $request
     * @return array
     * @throws Exception
     */
    public function getPhotoresistorStatus($request): array
    {
        // Paraméterek beolvasása
        $type = $request['type'] ?? 'all';
        $range = intval($request['range'] ?? 5);

        // Ha az összes értéket kérjük le
        if ($type == 'all') {
            // Validáció
            if ($range < 1 || $range > 30) {
                throw new Exception("Érvénytelen tartomány: $range");
            }

            // Státuszok lekérése adatbázisból
            $values = $this->database->getStatusValueInGivenRange($range);

            // Értékek kigyűjtése a sorokból
            $x = [];
            $y = [];
            foreach ($values as $value) {
                $x[] = $value['time'];
                $y[] = $value['value'];
            }

            // Válasz
            return [
                'x' => $x,
                'y' => $y
            ];
        }

        // Ha csak a legutolsó értéket kérjük le
        if ($type === 'last') {
            // Státusz lekérése adatbázisból
            $value = $this->database->getLatestStatusValue();

            // Válasz
            return [
                'x' => $value['time'],
                'y' => $value['value']
            ];
        }

        // Egyéb esetben hibát dobunk
        throw new Exception("Érvénytelen típus: $type");
    }

    /**
     * Lekéri a LED státuszát
     *
     * @throws Exception
     */
    public function getLedStatus(): array
    {
        // ESP állapot lekérdezés
        $data = $this->device->getLedStatus();

        // Válasz
        return $data;
    }

    /**
     * Beállítja a LED státuszát
     *
     * @throws Exception
     */
    public function setLedStatus($request): array
    {
        // Paraméterek beolvasása
        $state = $request['state'] ?? 'off';

        // Validáció
        if (!in_array($state, ['on', 'off'])) {
            throw new Exception("Érvénytelen állapot: $state");
        }

        // Állapot beállítása
        $this->device->setLedStatus($state);
        sleep(1);

        // Állapot lekérése
        $data = $this->device->getLedStatus();

        // Válasz
        return $data;
    }

    /**
     * Lekéri az RGB státuszát
     *
     * @throws Exception
     */
    public function getRGBStatus(): array
    {
        // ESP állapot lekérdezés
        $data = $this->device->getRGBStatus();

        // Válasz
        return $data;
    }

    /**
     * Beállítja az RGB státuszát
     *
     * @throws Exception
     */
    public function setRGBStatus($request): array
    {
        // Paraméterek beolvasása
        $state = $request['state'] ?? 'off';
        $red = intval($request['red'] ?? 0);
        $green = intval($request['green'] ?? 0);
        $blue = intval($request['blue'] ?? 0);

        // Validáció
        if (!in_array($state, ['on', 'off'])) {
            throw new Exception("Érvénytelen állapot: $state");
        }
        if ($red < 0 || $red > 255) {
            throw new Exception("Érvénytelen piros érték: $red");
        }
        if ($green < 0 || $green > 255) {
            throw new Exception("Érvénytelen zöld érték: $green");
        }
        if ($blue < 0 || $blue > 255) {
            throw new Exception("Érvénytelen kék érték: $blue");
        }

        // Állapot beállítása
        $this->device->setRGBStatus($state, $red, $green, $blue);

        // Állapot lekérése
        $data = $this->device->getRGBStatus();

        // Válasz
        return $data;
    }

}