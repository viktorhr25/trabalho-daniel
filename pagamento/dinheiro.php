<?php

require_once __DIR__ . '/interface.php';

class dinheiro implements formaPagamento
{
    public function pagar(float $valor): bool
    {
        echo "Pago R$ {$valor} em dinheiro\n";
        return true;
    }

    public function getNome(): string
    {
        return "Dinheiro";
    }
}