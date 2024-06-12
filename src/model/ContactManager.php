<?php
require_once('Contact.php');
require_once('src/lib/DBConnect.php');

class ContactManager
{
    public DBConnect $connection;

    public function findAll() :array
    {
        $contactStatement = $this->connection->getPDO()->query('SELECT * FROM contact');
        $contacts = [];
        while($row = $contactStatement->fetch()){
            $contact = new contact();
            $contact->id = $row['id'];
            $contact->name = $row['name'];
            $contact->email = $row['email'];
            $contact->phone = $row['phone_number'];

            $contacts[] = $contact;
        }

        return $contacts;
    }

}