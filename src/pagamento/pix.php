<?php

require_once __DIR__ . '/formaPagamento.php';

class Pix implements FormaPagamento
{
    public function pagar(float $valor): bool
    {
        echo "Pago R$ " . number_format($valor, 2, ',', '.') . " via Pix\n";
        return true;
    }

    public function getNome(): string
    {
        return "Pix";
    }
}