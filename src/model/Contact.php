<?php

class Contact
{
    private int $id;
    private ?string $name;
    private ?string $email;
    private ?string $phone;
    
    public function setId(int $id): void
    {
        if(!isset($id) && $id = 0) {
            trigger_error(
                'l\'identifiant est absent',
                E_USER_ERROR
            );
        }
        $this->id = $id;
    }

    public function getId(): int{
      return $this->id;     
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string{
      return $this->name;     
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): ?string{
      return $this->email;     
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPhone(): ?string{
      return $this->phone;     
    }

    public function toString(): void
    {
        $contactId = $this->getId();
        $contactName = $this->getName();
        echo "$contactId - $contactName\n";
    }

    public function toStringDetail(): void
    {
        $contactId = $this->getId();
        $contactName = $this->getName();
        $contactEmail = $this->getEmail();
        $contactPhone = $this->getPhone();
        echo "$contactId - $contactName - $contactEmail - $contactPhone\n";
    }
}