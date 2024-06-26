<?php
require_once('Contact.php');
require_once('src/lib/DBConnect.php');

class ContactManager
{
    //public DBConnect $connection;
    public Contact $contact;

    private $connection;

    public function __construct(DBConnect $connection) {
        $this->connection = $connection;
    }
    public function findAll() :array
    {
        $contacts = [];

        $contactStatement = $this->connection->getPDO()->query('SELECT `id`, `name` FROM contact');
        // Récupération de tous les résultats en une seule fois
        $rows = $contactStatement->fetchAll(PDO::FETCH_ASSOC);

        foreach($rows as $row){
            $contact = new Contact();
            $contact->setId($row['id']);
            $contact->setName($row['name']);
            $contacts[] = $contact;
        }

        return $contacts;
    }

    public function findById($contactId): ?contact
    {
        $contactStatement = $this->connection->getPDO()->prepare('SELECT * FROM contact WHERE id=:id');
        $contactStatement->execute(['id' => $contactId]);

        $row = $contactStatement->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
        return null; //aucun contact trouvé
        } else {
        $contact = new Contact();
        $contact->setId($contactId);
        $contact->setName($row['name'] ?? null);
        $contact->setEmail($row['email'] ?? null);
        $contact->setPhone($row['phone_number'] ?? null);
        return $contact;
        }
    }

    public function createContact($name, $email, $phone) :string
    {
        try{
        $newContactStatement = $this->connection->getPDO()->prepare('INSERT INTO `contact` (`name`, `email`, `phone_number`) VALUES (:name, :email, :phone_number)');
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

    public function deleteContact($idDeleteContact) :void
    {
        try{
        $delContactStatement = $this->connection->getPDO()->prepare('DELETE FROM `contact` WHERE id=:id');
        $delContactStatement->execute([
            'id' => $idDeleteContact,
        ]);

        echo "Le contact a été correctement supprimé\n";

        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            }
    }
}