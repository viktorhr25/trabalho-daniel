<?php

require_once 'cliente.php';
require_once 'itemPedido.php';
require_once 'statusPedido.php';
require_once 'item.php';

class pedido
{
     /** @var ItemPedido[] */ #comentario de documentação para indicar que a variável $itens é um array de objetos ItemPedido
    private array $itens = [];
    private StatusPedido $status;
    private bool $pago = false;

    public function __construct(private int $id, private Cliente $cliente) #Função construtora que inicializa o pedido com o status "Criado"
    {
        $this->status = StatusPedido::Criado;
    }

    public function adicionarItem(Item $item, int $quantidade):void #Função que adiciona um item ao pedido
    {
        $this -> garantirEstadoCriado();
        #Se o item já existir no pedido, apenas aumenta a quantidade
        foreach($this -> itens as $itemPedido)
            {
                if($itemPedido -> gerItem() -> getId() === $item -> getId())
                    {
                        $itemPedido -> aumentarQuantidade($quantidade);
                        return;
                    }
            }

        $this -> itens[] = new itemPedido($item, $quantidade);
    }

    public function removerItem(int $idItem):void #Função que remove um item do pedido
    {
        $this -> garantirEstadoCriado();
        foreach($this -> itens as $index => $itemPedido)
            {
                if($itemPedido -> getItem() -> getId() === $idItem)
                    {
                        unset($this -> itens[$index]);
                        return;
                    }
            }
        throw new InvalidArgumentException("Item não encontrado no pedido.");
    }

      public function calcularTotal(): float #Função que calcula o total do pedido somando os subtotais de cada item
    {
        $total = 0.0;
            foreach ($this->itens as $itemPedido) {
                $total += $itemPedido->getSubtotal();
            }
        return $total;
    }

     public function marcarComoPago(): void #Função que impede de marcar o pedido como pago caso não tenha itens
    {
        $this->garantirEstadoCriado();

            if (count($this->itens) === 0) {
                throw new TransicaoInvalidaException("Não é possível pagar um pedido sem itens.");
            }

        $this->pago = true;
    }

    public function avancarStatus(): void #Função que impede de avançar o status do pedido caso não esteja pago ou não tenha itens
    {
        if ($this->status === StatusPedido::Criado) {
            if (count($this->itens) === 0) {
                throw new TransicaoInvalidaException("Não é possível avançar um pedido sem itens.");
            }
            if (!$this->pago) {
                throw new TransicaoInvalidaException("O pedido precisa ser pago antes de ir para preparo.");
            }
        }

        $this->status = $this->status->proximo();
    }

    public function cancelar(): void #Função que impede de cancelar pedido se o status for diferente de Criado, Em preparo ou Saiu para entrega
    {
        if (!$this->status->podeCancelar()) {
            throw new TransicaoInvalidaException("Não é possível cancelar um pedido com status '{$this->status->value}'."
            );
        }

        $this->status = StatusPedido::Cancelado;
    }

    private function garantirEstadoCriado(): void #Função para impedir alterações caso o pedido não esteja no estado Criado
    {
        if ($this->status !== StatusPedido::Criado) {
            throw new TransicaoInvalidaException("Só é possível alterar pedidos com status 'Criado' (atual: '{$this->status->value}')."
            );
        }
    }

    public function getId(): int 
    {
        return $this->id;
    }

    public function getCliente(): Cliente
    {
        return $this->cliente;
    }

    /** @return ItemPedido[] */ #comentario de documentação para indicar que o retorno é um array de objetos ItemPedido
    public function getItens(): array
    {
        return $this->itens;
    }

    public function getStatus(): StatusPedido
    {
        return $this->status;
    }

    public function isPago(): bool
    {
        return $this->pago;
    }

}