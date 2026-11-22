<?php

require_once __DIR__ . '/../modelo/pedido.php';


# Guarda os pedidos em memória (sem banco de dados).
class PedidoRepositorio
{
    /** @var Pedido[] indexados pelo id */
    private array $pedidos = [];
    private int $ultimoId = 0;

    public function proximoId(): int #Função que gera o próximo id de pedido
    {
        return ++$this->ultimoId;
    }

    public function salvar(Pedido $pedido): void #Função que salva pedido no array de pedidos, indexando pelo id do pedido
    {
        $this->pedidos[$pedido->getId()] = $pedido;
    }

    public function buscarPorId(int $id): ?Pedido #Função que busca pedido pelo id, retornando null caso não seja encontrado
    {
        return $this->pedidos[$id] ?? null;
    }

    /** @return Pedido[] */
    public function listar(): array #Função que lista todos os pedidos registrados no repositório e retorna um array de objetos Pedido
    {
        return $this->pedidos;
    }
    
}
