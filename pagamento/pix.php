<?php

require_once __DIR__ . '/interface.php';

class pix implements formaPagamento
{
   public function pagar(float $valor): void
    {
        echo "Pago R$ {$valor} via Pix\n";
    }
}