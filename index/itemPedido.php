<?php
 
require_once 'item.php';

class itemPedido 
{
    public function __construct(private Item $item, private int $quantidade )
    {
        if($quantidade <= 0){
            throw new InvalidArgumentException("A quantidade deve ser maior que zero."); #lança uma mensagem de erro caso a quantidade digitada seja menor ou igual a zero
        }

    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function aumentarQuantidade(int $qtd):void
    {
        $this->quantidade += $qtd;
    }

    public function calcularSubtotal(): float
    {
        return $this->item->getPreco() * $this->quantidade;
    }

}
