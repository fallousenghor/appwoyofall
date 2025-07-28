<?php

interface ClientRepositoryInterface
{
    public function find(int $id): ?Client;
    public function save(Client $client): void;
}
