<?php

require_once 'item.php';

class Cardapio
{
    /** @var Item[] indexados pelo id do item */ #comentario de documentação para indicar que a variável $itens é um array de objetos Item
    private array $itens = [];

    public function adicionarItem(Item $item): void
    {
    $this->itens[$item->getId()] = $item;
    }

    public function buscarPorId(int $id): ?Item
    {
        return $this->itens[$id] ?? null;
    }

    /** @return Item[] */
    public function listar(): array
    {
        return $this->itens;
    }
    }
