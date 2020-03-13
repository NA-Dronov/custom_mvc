<?php

class MysqlDataComponent extends Component implements IDataComponent
{
    protected $dbHost = "";
    protected $dbName = "";
    protected $dbUser = "";
    protected $dbPassword = "";
    private $connection = null;

    public function __construct(string $dbHost, string $dbName, string $dbUser, string $dbPassword)
    {
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->connection = new PDO($this->getDsn(), $this->dbUser, $this->dbPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public static function createFromConfiguration(AConfigComponent $configuration): self
    {
        return new self(
            $configuration->db_host,
            $configuration->db_name,
            $configuration->db_user,
            $configuration->db_password
        );
    }

    public function getDsn(): string
    {
        return "mysql:host={$this->dbHost};dbname={$this->dbName}";
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
