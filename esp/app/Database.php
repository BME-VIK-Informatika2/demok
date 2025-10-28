<?php

class Database
{
    /**
     * @var string
     */
    private $hostname;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $database;
    /**
     * @var PDO
     */
    private $pdo;
    /**
     * @var string
     */
    private $port;

    public function __construct($hostname, $port, $username, $password, $database)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * Kapcsolódás az adatbázishoz
     *
     * @return void
     */
    private function connect()
    {
        // Ha már létrejött a kapcsolat, akkor nem hozzuk létre úja
        if ($this->pdo != null)
            return;

        // Kapcsolódás
        $this->pdo = new PDO("mysql:host=$this->hostname;port=$this->port;dbname=$this->database;charset=utf8mb4", $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Adatbázis inicializálása
     *
     * @throws Exception
     */
    public function init()
    {
        // Kapcsolódás az adatbázishoz
        $this->connect();

        // Tábla létrehozása, ha nem létezik
        $this->execute('CREATE TABLE IF NOT EXISTS `status` (
              `time` timestamp NOT NULL DEFAULT current_timestamp() PRIMARY KEY,
              `value` float NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;');

        // Tábla kiürítése
        $this->execute("TRUNCATE TABLE `status`");
    }

    /**
     * Általános query futtatás
     *
     * @param $query
     * @param $params
     * @return array
     */
    private function execute($query, $params = null): array
    {
        // Kapcsolódás az adatbázishoz
        $this->connect();

        if ($params == null) {
            // Ha nincs paraméter, akkor lefuttatjuk
            $statement = $this->pdo->query($query);
        } else {
            // Ha van paraméter, akkor prepared statement futtatás
            $statement = $this->pdo->prepare($query);
            foreach ($params as $key => $value) {
                $statement->bindParam($key, $value);
            }
            $statement->execute();
        }
        // Eredményeket kiírjuk asszociatív tömbbe
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Státusz érték beszúrása adatbázisba
     *
     * @param $value
     * @return void
     */
    public function insertStatusValue($value)
    {
        $this->execute('INSERT INTO `status`(`value`) VALUES(:value)', [
            'value' => $value
        ]);
    }

    /**
     * Státusz értékek lekérése adott időtartományból (utolsó x perc)
     *
     * @param $range
     * @return array
     */
    public function getStatusValueInGivenRange($range): array
    {
        return $this->execute('SELECT * FROM `status` WHERE `time` >= NOW() - INTERVAL :range MINUTE ORDER BY `time` DESC ', [
            'range' => $range
        ]);
    }

    /**
     * Legutolsó státusz érték lekérése
     *
     * @return array
     */
    public function getLatestStatusValue(): array
    {
        return $this->execute('SELECT * FROM `status` ORDER BY `time` DESC LIMIT 1')[0];
    }

}