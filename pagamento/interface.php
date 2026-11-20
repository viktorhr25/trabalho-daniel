<?php
class Pagamento
{
    public function pagar(string $tipo, float $valor): void
    {
        if ($tipo === 'pix') {
            echo "Pago via Pix\n";
        } elseif ($tipo === 'cartao') {
            echo "Pago no cartão\n";
        } elseif ($tipo === 'dinheiro') {
            echo "Pago em dinheiro\n";
        }
    }
}
