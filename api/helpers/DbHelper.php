<?php

class DbHelper
{
    protected $pdo;

    public function __construct($host, $name, $user, $pass)
    {
        $this->pdo = new PDO('mysql:host=' . $host . ';dbname=' . $name, $user, $pass);
    }

    /**
     * @param string $q
     * @param string $ip
     * @param string $referer
     */
    public function logSearch($q, $ip, $referer)
    {
        $sql = 'INSERT INTO search_log SET q = :q, ip = :ip, referer = :referer, dt = :dt';

        $this->pdo->prepare($sql)
            ->execute([
                'q'  => $q,
                'ip' => $ip,
                'referer' => $referer,
                'dt' => date('Y-m-d H:i:s')
            ]);
    }
}

