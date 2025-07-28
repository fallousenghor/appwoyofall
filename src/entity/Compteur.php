<?php

namespace App\Entity;

class Compteur
{
    private string $numero;
    private Client $client;

    public function __construct(string $numero, Client $client)
    {
        $this->numero = $numero;
        $this->client = $client;
    }

    public function getNumero(): string { return $this->numero; }
    public function getClient(): Client { return $this->client; }

    public function setClient(Client $client): void { $this->client = $client; }
}
