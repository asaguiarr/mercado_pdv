<!-- resources/views/pdv/index.blade.php -->
@extends('layouts.app')

@section('title', 'PDV')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- LISTA DE PRODUTOS -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="bi bi-box-seam me-2"></i> Produtos
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" id="search-product" class="form-control" placeholder="Pesquisar produto...">
                        <button class="btn btn-outline-light bg-primary text-white" id="search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>

                    <div class="table-responsive" style="max-height: 400px; overflow-y:auto;">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço</th>
                                    <th>Estoque</th>
                                    <th>Qtd.</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="products-table">
                                <!-- Produtos carregados via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARRINHO -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="bi bi-cart-check me-2"></i> Carrinho
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th>Qtd.</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cart-table">
                                <!-- Itens do carrinho -->
                            </tbody>
                        </table>
                    </div>

                    <!-- RESUMO -->
                    <div class="mt-3 p-3 border rounded bg-light">
                        <p class="mb-1"><strong>Subtotal:</strong> R$ <span id="subtotal">0,00</span></p>
                        <p class="mb-1"><strong>Desconto:</strong> 
                            <input type="number" id="discount" value="0" class="form-control form-control-sm d-inline-block w-25">
                        </p>
                        <p class="fw-bold fs-5">Total: R$ <span id="total">0,00</span></p>
                    </div>

                    <!-- PAGAMENTO -->
                    <div class="mt-3">
                        <label for="payment-method" class="form-label">Forma de Pagamento</label>
                        <select id="payment-method" class="form-select">
                            <option value="dinheiro">💵 Dinheiro</option>
                            <option value="cartao">💳 Cartão</option>
                            <option value="pix">⚡ PIX</option>
                            <option value="misto">🔀 Misto</option>
                        </select>
                    </div>

                    <button class="btn btn-success mt-3 w-100 fw-bold" id="finalize-sale">
                        <i class="bi bi-check-circle me-2"></i> Finalizar Venda
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CONFIRMAÇÃO -->
<div class="modal fade" id="confirmSaleModal" tabindex="-1" aria-labelledby="confirmSaleLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="confirmSaleLabel"><i class="bi bi-bag-check me-2"></i> Confirmar Venda</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Deseja realmente finalizar esta venda?
        <p class="fw-bold mt-2 fs-5">Total: R$ <span id="modal-total"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" id="confirmFinalize">Sim, Finalizar</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function loadProducts(search = '') {
    $.get('{{ route('pdv.products') }}', { search }, function(data) {
        $('#products-table').empty();
        $.each(data, function(_, product) {
            $('#products-table').append(`
                <tr>
                    <td>${product.name}</td>
                    <td>R$ ${parseFloat(product.price).toFixed(2)}</td>
                    <td>${product.stock}</td>
                    <td><input type="number" min="1" max="${product.stock}" value="1" id="quantity-${product.id}" class="form-control form-control-sm"></td>
                    <td><button class="btn btn-sm btn-primary" onclick="addToCart(${product.id}, '${product.name}', ${product.price}, ${product.stock})">
                        <i class="bi bi-cart-plus"></i>
                    </button></td>
                </tr>
            `);
        });
    });
}
loadProducts();

$('#search-btn').on('click', function() {
    loadProducts($('#search-product').val());
});

// ---------------- CARRINHO ----------------
let cart = [];

function updateCart() {
    $('#cart-table').empty();
    let subtotal = 0;
    cart.forEach((item, index) => {
        subtotal += item.price * item.quantity;
        $('#cart-table').append(`
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>R$ ${(item.price * item.quantity).toFixed(2)}</td>
                <td><button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${index})">
                    <i class="bi bi-trash"></i>
                </button></td>
            </tr>
        `);
    });
    let discount = parseFloat($('#discount').val()) || 0;
    let total = subtotal - discount;
    $('#subtotal').text(subtotal.toFixed(2));
    $('#total').text(total.toFixed(2));
    $('#modal-total').text(total.toFixed(2));
}

function addToCart(id, name, price, stock) {
    let quantity = parseInt($('#quantity-' + id).val()) || 1;
    if (quantity > stock) {
        alert('Quantidade maior que estoque disponível!');
        return;
    }
    let existing = cart.find(item => item.id === id);
    if (existing) {
        if (existing.quantity + quantity > stock) {
            alert('Estoque insuficiente!');
            return;
        }
        existing.quantity += quantity;
    } else {
        cart.push({ id, name, price, quantity });
    }
    updateCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    updateCart();
}

$('#discount').on('input', updateCart);

// ---------------- FINALIZAR ----------------
$('#finalize-sale').on('click', function() {
    if (cart.length === 0) {
        alert('Carrinho vazio!');
        return;
    }
    new bootstrap.Modal(document.getElementById('confirmSaleModal')).show();
});

$('#confirmFinalize').on('click', function() {
    $.post('{{ route('pdv.sale') }}', {
        cart: cart,
        payment_method: $('#payment-method').val(),
        discount: $('#discount').val(),
        _token: '{{ csrf_token() }}'
    }, function() {
        cart = [];
        updateCart();
        bootstrap.Modal.getInstance(document.getElementById('confirmSaleModal')).hide();
        alert('Venda finalizada com sucesso!');
    });
});
</script>
@endpush
@endsection
