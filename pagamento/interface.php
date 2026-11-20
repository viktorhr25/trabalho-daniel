<?php
interface formaPagamento
{
    public function pagar(float $valor): bool;
    public function getNome(): string;
}


?>
