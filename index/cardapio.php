<?php

require_once 'item.php';

class Cardapio
{
    private array $itens = [];

    public function adicionarItem(Item $item): void
    {
        $this->itens[] = $item;
    }

    public function listar(): void
    {
        foreach ($this->itens as $item) {
            echo $item->getNome() . " - R$ " . number_format($item->getPreco(), 2, ',', '.') . "\n";
        }
    }
}
