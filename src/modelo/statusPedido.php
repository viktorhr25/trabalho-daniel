<?php

enum StatusPedido: string
{
    case Criado = 'Criado';
    case EmPreparo = 'Em preparo';
    case SaiuParaEntrega = 'Saiu para entrega';
    case Entregue = 'Entregue';
    case Cancelado = 'Cancelado';

    public function proximo(): self #Função que retorna o próximo estado do pedido
    {
        return match ($this) {
            self::Criado => self::EmPreparo,
            self::EmPreparo => self::SaiuParaEntrega,
            self::SaiuParaEntrega => self::Entregue,
            default => throw new TransicaoInvalidaException(
                "Não há próximo estado a partir de '{$this->value}'." #lança uma mensagem de erro caso o pedido esteja no estado Entregue ou Cancelado
            ),
        };
    }

    public function podeCancelar(): bool
    {
        return match ($this) {
            self::Criado, self::EmPreparo, self::SaiuParaEntrega => true,
            default => false,
        };
    }
}
