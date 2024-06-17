<?php
require_once('Contact.php');
require_once('src/lib/DBConnect.php');

class ContactManager
{
    public DBConnect $connection;
    public Contact $contact;

    public function findAll() :array
    {
        $contactStatement = $this->connection->getPDO()->query('SELECT `id`, `name` FROM contact');
        $contacts = [];
        while($row = $contactStatement->fetch()){
            $contact = new Contact;
            $contact->setId($row['id']);
            $contact->setName($row['name']);
            $contact->setEmail(null);
            $contact->setPhone(null);
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

    public function createContact($name, $email, $phone) :string
    {
        try{
        $newContactStatement = $this->connection->getPDO()->prepare('INSERT INTO `contact` (`id`, `name`, `email`, `phone_number`) VALUES (NULL, :name, :email, :phone_number)');
        $newContactStatement->execute([
            'name' => $name,
            'phone_number' => $phone,
            'email' => $email,
        ]);

        $success = $this->connection->getPDO()->LastInsertId();
        echo "Le contact a été correctement créé sous l'ID $success, saisissez la commande \"detail $success\" pour vérifier\n";
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            }
        return $success;
    }

    public function updateContact($idUpdateContact, $name, $email, $phone) :void
    {
        try{
        $updContactStatement = $this->connection->getPDO()->prepare('UPDATE `contact` SET name=:name, email=:email, phone_number=:phone_number WHERE id=:id');
        $updContactStatement->execute([
            'id' => $idUpdateContact,
            'name' => $name,
            'phone_number' => $phone,
            'email' => $email,
        ]);

        echo "Le contact $idUpdateContact a été correctement modifié avec les données suivantes : $name, $email, $phone\n";
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            }
    }

    public function deleteContact($idDleleteContact) :void
    {
        try{
        $delContactStatement = $this->connection->getPDO()->prepare('DELETE FROM `contact` WHERE id=:id');
        $delContactStatement->execute([
            'id' => $idDleleteContact,
        ]);

        echo "Le contact a été correctement supprimé\n";

        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            }
    }
}