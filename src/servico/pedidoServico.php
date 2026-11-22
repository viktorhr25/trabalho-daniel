<?php

require_once __DIR__ . '/../modelo/cardapio.php';
require_once __DIR__ . '/../modelo/cliente.php';
require_once __DIR__ . '/../modelo/pedido.php';
require_once __DIR__ . '/../modelo/statusPedido.php';
require_once __DIR__ . '/../pagamento/formaPagamento.php';
require_once __DIR__ . '/../repositorio/pedidoRepositorio.php';
require_once __DIR__ . '/../excecao/transacaoInvalida.php';

class pedidoService
{
    public function __construct(private PedidoRepositorio $repositorio) {}

    public function criarPedido(Cliente $cliente): Pedido #cria um novo pedido para o cliente e salva no repositório
    {
        $pedido = new Pedido($this->repositorio->proximoId(), $cliente);
        $this->repositorio->salvar($pedido);
        return $pedido;
    }

    public function adicionarItemAoPedido(Pedido $pedido,Cardapio $cardapio,int $idItem,int $quantidade): void #adiciona um item ao pedido, verificando se o item existe no cardápio
    {
        $item = $cardapio->buscarPorId($idItem);

        if ($item === null) 
        {
            throw new InvalidArgumentException("O item {$idItem} não existe no cardápio.");
        }

        $pedido->adicionarItem($item, $quantidade);
    }

    public function finalizarPedido(Pedido $pedido, FormaPagamento $formaPagamento): void #finaliza o pedido, verificando se o pedido está no estado correto e se possui itens, e processa o pagamento
    {
        if ($pedido->getStatus() !== StatusPedido::Criado) 
        { 
            throw new TransicaoInvalidaException("Este pedido não pode mais ser pago.");
        }

        if (count($pedido->getItens()) === 0) 
        {
            throw new TransicaoInvalidaException("O pedido não tem itens.");
        }

        $formaPagamento->pagar($pedido->calcularTotal()); #se o pagamento for recusado,trava aqui

        $pedido->marcarComoPago();
        $pedido->avancarStatus(); // Criado para Em preparo
        $this->repositorio->salvar($pedido);
    }

    public function atualizarStatus(Pedido $pedido): void #atualiza o status do pedido, avançando para o próximo estado e salvando no repositório
    {
        $pedido->avancarStatus();
        $this->repositorio->salvar($pedido);
    }

    public function cancelarPedido(Pedido $pedido): void #cancela o pedido, alterando o status para cancelado e salvando no repositório
    {
        $pedido->cancelar();
        $this->repositorio->salvar($pedido);
    }

    public function buscarPedido(int $id): Pedido #busca um pedido pelo id, lançando uma exceção caso não seja encontrado
    {
        $pedido = $this->repositorio->buscarPorId($id);

        if ($pedido === null) {
            throw new InvalidArgumentException("Pedido {$id} não encontrado.");
        }

        return $pedido;
    }

    /** @return Pedido[] */
    public function listarPedidos(): array #lista todos os pedidos registrados no repositório e retorna um array de objetos Pedido
    {
        return $this->repositorio->listar();
    }
}
