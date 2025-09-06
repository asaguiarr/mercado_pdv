// ...

let cart = [];

$('#add-to-cart').on('click', function() {
    let productId = $('#product_id').val();
    let quantity = parseInt($('#quantity').val());

    $.ajax({
        type: 'GET',
        url: '/pdv/product/' + productId,
        success: function(product) {
            let existingItem = cart.find(item => item.product_id == productId);

            if (existingItem) {
                existingItem.quantity += quantity;
            } else {
                cart.push({
                    product_id: productId,
                    name: product.name,
                    price: product.price,
                    quantity: quantity
                });
            }

            updateCart();
        }
    });
});

function updateCart() {
    $('#cart-table tbody').empty();

    let total = 0;

    cart.forEach((item, index) => {
        let subtotal = item.price * item.quantity;
        total += subtotal;

        $('#cart-table tbody').append(`
            <tr>
                <td>${item.name}</td>
                <td>${item.quantity}</td>
                <td>R$ ${item.price.toFixed(2)}</td>
                <td>R$ ${subtotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">Remover</button>
                </td>
            </tr>
        `);
    });

    // Adicionei um elemento para exibir o total
    if ($('#total').length === 0) {
        $('#cart-table').after(`<p id="total">Total: R$ ${total.toFixed(2)}</p>`);
    } else {
        $('#total').text(`Total: R$ ${total.toFixed(2)}`);
    }
}

function removeItem(index) {
    cart.splice(index, 1);
    updateCart();
}

$('#finalize-sale').on('click', function() {
    let customerId = $('#customer_id').val();
    let paymentMethod = $('#payment_method').val();
    let discount = parseFloat($('#discount').val());

    $.ajax({
        type: 'POST',
        url: '/pdv/sale',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            cart: cart,
            customer_id: customerId,
            payment_method: paymentMethod,
            discount: discount
        },
        success: function(response) {
            alert(response.message);
            // Redirecione para a página de vendas
            window.location.href = '/pdv/sales';
        },
        error: function(xhr) {
            alert(xhr.responseJSON.error);
        }
    });
});