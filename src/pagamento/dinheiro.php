<?php

require_once __DIR__ . '/formaPagamento.php';

class dinheiro implements formaPagamento
{
    public function pagar(float $valor): bool
    {
        echo "Pago R$ " . number_format($valor, 2, ',', '.') . " em dinheiro\n";        
        return true;
    }

    public function getNome(): string
    {
        return "Dinheiro";
    }
}