<?php
interface FormaPagamento
{
    public function pagar(float $valor): bool;
}

class Pix implements FormaPagamento
{
    public function pagar(float $valor): bool
    {
        echo "Pago R$ $valor via Pix\n";
        return true;
    }
}
?>