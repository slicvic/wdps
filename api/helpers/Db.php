<?php

class Db {
    protected $pdo;

    public function __construct($host, $name, $user, $pass) {
        $this->pdo = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
    }

    /**
     * @param string $q
     * @param string $ip
     */
    public function logSearch($q, $ip)
    {
        $sql = 'INSERT INTO search_log SET q = :q, ip = :ip, dt = :dt';

        $this->pdo->prepare($sql)
            ->execute([
                'q'  => $q,
                'ip' => $ip,
                'dt' => date('Y-m-d H:i:s')
            ]);
    }
}

