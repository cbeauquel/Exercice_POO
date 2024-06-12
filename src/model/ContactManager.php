<?php
require_once('Contact.php');
require_once('src/lib/DBConnect.php');

class ContactManager
{
    public DBConnect $connection;
    public Contact $contact;

    public function findAll() :array
    {
        $contactStatement = $this->connection->getPDO()->query('SELECT * FROM contact');
        $contacts = [];
        while($row = $contactStatement->fetch()){
            $contact = new Contact;
            $contact->setId($row['id']);
            $contact->setName($row['name']);
            $contact->setEmail($row['email']);
            $contact->setPhone($row['phone_number']);

            $contacts[] = $contact;
        }

        return $contacts;
    }

}