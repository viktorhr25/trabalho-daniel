<?php
class cliente
{
    public function __construct(
        private string $nome,
        private string $telefone,
        private string $endereco
    ) {}

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getTelefone(): string
    {
        return $this->telefone;
    }

    public function getEndereco(): string
    {
        return $this->endereco;
    }
}

