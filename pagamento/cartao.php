<?php

require_once __DIR__ . '/interface.php';

class Cartao implements formaPagamento
{
    public function pagar(float $valor): void
    {
        echo "Pago R$ {$valor} via Cartão\n";
    }
}