<?php

require_once __DIR__ . '/formaPagamento.php';

class Cartao implements formaPagamento
{
    public function pagar(float $valor): bool
    {
        echo "Pago R$ " . number_format($valor, 2, ',', '.') . " via Cartão\n";
        return true;
    }

    public function getNome(): string
    {
        return "Cartão";
    }
}