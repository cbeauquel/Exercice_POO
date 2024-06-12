<?php
class DBConnect
{
    public ?PDO $database = null;

    public function getPDO(): PDO
    {
        if ($this->database === null){
            $this->database = new PDO('mysql:host=localhost;dbname=contact_management','root', '',);
        }

        return $this->database;
    }
}
