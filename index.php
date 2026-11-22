<?php

require_once __DIR__ . '/src/modelo/item.php';
require_once __DIR__ . '/src/modelo/cardapio.php';
require_once __DIR__ . '/src/modelo/cliente.php';
require_once __DIR__ . '/src/modelo/pedido.php';
require_once __DIR__ . '/src/modelo/statusPedido.php';
require_once __DIR__ . '/src/pagamento/formaPagamento.php';
require_once __DIR__ . '/src/pagamento/pix.php';
require_once __DIR__ . '/src/pagamento/cartao.php';
require_once __DIR__ . '/src/pagamento/dinheiro.php';
require_once __DIR__ . '/src/servico/pedidoServico.php';
require_once __DIR__ . '/src/repositorio/pedidoRepositorio.php';
require_once __DIR__ . '/src/excecao/pagRecusado.php';
require_once __DIR__ . '/src/excecao/transacaoInvalida.php';

function lerEntrada(string $mensagem): string #Função que lê a entrada do usuário e retorna a string digitada
{
    echo $mensagem;
    $linha = fgets(STDIN);
    return $linha === false ? '' : trim($linha);
}

function formatarMoeda(float $valor): string #Função para formatar R$
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function exibirMenu(): void #Função que exibe o menu de opções para o usuário 
{
    echo "\n=========== LANCHONETE ===========\n";
    echo "1 - Ver cardápio\n";
    echo "2 - Fazer pedido\n";
    echo "3 - Pagar pedido\n";
    echo "4 - Avançar status do pedido\n";
    echo "5 - Cancelar pedido\n";
    echo "6 - Listar pedidos\n";
    echo "0 - Sair\n";
    echo "==================================\n";
}

function exibirCardapio(Cardapio $cardapio): void #Função que exibe o cardápio para o usuário
{
    echo "\n--- Cardápio ---\n";
    foreach ($cardapio->listar() as $item) {
        echo $item->getId() . " - " . $item->getNome() . " - " . formatarMoeda($item->getPreco()) . "\n";
    }
}

function exibirPedido(Pedido $pedido): void #Função que exibe os detalhes de um pedido específico
{
    echo "\nPedido #" . $pedido->getId()
        . " | Cliente: " . $pedido->getCliente()->getNome()
        . " | Status: " . $pedido->getStatus()->value
        . " | Pago: " . ($pedido->isPago() ? 'Sim' : 'Não') . "\n";

    foreach ($pedido->getItens() as $itemPedido) {
        echo "   " . $itemPedido->getQuantidade() . "x " . $itemPedido->getItem()->getNome()
            . " = " . formatarMoeda($itemPedido->calcularSubtotal()) . "\n";
    }

    echo "   Total: " . formatarMoeda($pedido->calcularTotal()) . "\n";
}

function criarFormaPagamento(string $opcao): FormaPagamento #Função que cria a forma de pagamento 
{
    switch ($opcao) {
        case '1':
            return new Pix();
        case '2':
            return new Cartao(lerEntrada("Número do cartão (16 dígitos): "));
        case '3':
            $valor = str_replace(',', '.', lerEntrada("Valor entregue em dinheiro: R$ "));
            return new Dinheiro((float) $valor);
        default:
            throw new InvalidArgumentException("Forma de pagamento inválida.");
    }
}
#inserção de dados no cardápio
$cardapio = new Cardapio();
$cardapio->adicionarItem(new Item(1, "X-Burger", 15.50));
$cardapio->adicionarItem(new Item(2, "X-Salada", 17.00));
$cardapio->adicionarItem(new Item(3, "Batata Frita", 12.00));
$cardapio->adicionarItem(new Item(4, "Refrigerante", 6.00));
$cardapio->adicionarItem(new Item(5, "Suco Natural", 8.50));

$servico = new PedidoService(new pedidoRepositorio());

#loop inicial do programa

while (true) {
    exibirMenu();
    $opcao = lerEntrada("Escolha uma opção: ");

    if ($opcao === '' && feof(STDIN)) {
        break;
    }

    try {
        switch ($opcao) {
            case '1': #opção 1 mostra o cardápio
                exibirCardapio($cardapio);
                break;

            case '2': #opção 2 adiciona um cliente, cria um pedido e adiciona itens ao pedido
                $nome = lerEntrada("Nome do cliente: ");
                $telefone = lerEntrada("Telefone: ");
                $endereco = lerEntrada("Endereço: ");

                $pedido = $servico->criarPedido(new Cliente($nome, $telefone, $endereco));
                echo "Pedido #" . $pedido->getId() . " criado.\n";

                exibirCardapio($cardapio);
                while (true) {
                    $idItem = lerEntrada("\nId do item (Enter para terminar): ");
                    if ($idItem === '') {
                        break;
                    }
                    $quantidade = lerEntrada("Quantidade: ");

                    try {
                        $servico->adicionarItemAoPedido($pedido, $cardapio, (int) $idItem, (int) $quantidade); #int sinaliza que a entrada é um número inteiro
                        echo "Item adicionado.\n";
                    } catch (InvalidArgumentException $e) {
                        echo "Erro: " . $e->getMessage() . "\n";
                    }
                }

                exibirPedido($pedido);
                break;

            case '3': #opção 3 busca o pedido pelo id, exibe o pedido, solicita a forma de pagamento e finaliza o pedido
                $pedido = $servico->buscarPedido((int) lerEntrada("Id do pedido: "));
                exibirPedido($pedido);

                echo "\nForma de pagamento:\n1 - Pix\n2 - Cartão\n3 - Dinheiro\n";
                $forma = criarFormaPagamento(lerEntrada("Escolha: "));
                $servico->finalizarPedido($pedido, $forma);
                echo "Pedido #" . $pedido->getId() . " agora está: " . $pedido->getStatus()->value . "\n";
                break;
 
            case '4': #opção 4 busca o pedido pelo id, atualiza o status do pedido e exibe o novo status
                $pedido = $servico->buscarPedido((int) lerEntrada("Id do pedido: "));
                $servico->atualizarStatus($pedido);
                echo "Pedido #" . $pedido->getId() . " agora está: " . $pedido->getStatus()->value . "\n";
                break;

            case '5': #opção 5 busca o pedido pelo id, cancela o pedido e exibe uma mensagem de confirmação
                $pedido = $servico->buscarPedido((int) lerEntrada("Id do pedido: "));
                $servico->cancelarPedido($pedido);
                echo "Pedido #" . $pedido->getId() . " cancelado.\n";
                break;

            case '6': #opção 6 lista todos os pedidos registrados e exibe uma mensagem caso não haja nenhum pedido
                $pedidos = $servico->listarPedidos();
                if (count($pedidos) === 0) {
                    echo "Nenhum pedido registrado.\n";
                }
                foreach ($pedidos as $pedido) {
                    exibirPedido($pedido);
                }
                break;

            case '0': #finaliza
                echo "Até logo!\n";
                break 2;

            default: 
                echo "Opção inválida.\n";
        }
    #tratamento de exceções
    } catch (PagamentoRecusadoException $e) {
        echo "Pagamento recusado: " . $e->getMessage() . "\n";
    } catch (TransicaoInvalidaException $e) {
        echo "Operação não permitida: " . $e->getMessage() . "\n";
    } catch (InvalidArgumentException $e) {
        echo "Erro: " . $e->getMessage() . "\n";
    }
}

