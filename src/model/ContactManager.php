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

    public function findById($contactId) :object
    {
        $contact = new Contact;
        $contact->setId($contactId);
        $contactStatement = $this->connection->getPDO()->prepare('SELECT * FROM contact WHERE id=:id');
        $contactStatement->execute([
            'id' => $contactId,
        ]);

        $row = $contactStatement->fetch();
        if (is_Bool($row)){
            $contact->setName(null);
            $contact->setEmail(null);
            $contact->setPhone(null);
        } else {
        $contact->setName($row['name']);
        $contact->setEmail($row['email']);
        $contact->setPhone($row['phone_number']);
        }
        return $contact;

    }

}
