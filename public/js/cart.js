class Cart {
    static cartTotalQuantity = 0;
    static cartTotalPrice = 0;
    static SHIPPING_COST = 4.99;
    static updateTimeout = null;

    static debounce(func, wait) {
        clearTimeout(this.updateTimeout);
        this.updateTimeout = setTimeout(func, wait);
    }

    static increaseQuantity() {
        const input = document.getElementById('quantity');
        if (input) {
            input.value = parseInt(input.value) + 1;
        }
    }

    static decreaseQuantity() {
        const input = document.getElementById('quantity');
        if (input) {
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }
    }

    static updateCartBadge(count) {
        const badge = document.getElementById('cart-badge');
        if (!badge) return;
        
        badge.textContent = count;
        badge.classList.toggle('d-none', count === 0);
    }

    static addToCart(productId, quantity = 1) {
        return fetch('/api/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                productId: productId,
                quantity: parseInt(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produit ajouté au panier avec succès');
                this.updateCartBadge(data.cartCount);
            } else {
                alert('Erreur lors de l\'ajout au panier');
            }
            return data;
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
            throw error;
        });
    }


    // A partir du checkout
    static updateQuantity(productId, value = 1) {
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (!row) return;

        const input = row.querySelector('.quantity-input');
        const price = parseFloat(row.dataset.price);
        let quantity = parseInt(input.value);
        let newQuantity = quantity;

        newQuantity += value;

        if (newQuantity < 1) return;

        // Mettre à jour l'affichage immédiatement
        input.value = newQuantity;
        this.updateProductTotal(row, price, newQuantity);
        this.refreshCart();

        // Debounce la mise à jour du serveur
        this.debounce(() => {
            fetch('/api/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    productId: productId,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateCartBadge(data.cartCount);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }, 300);
    }

    static removeItem(productId) {
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (!row) return;
        
        fetch('/api/cart/remove', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                productId: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page si le panier est vide
                if (data.reload) {
                    location.reload();
                }
                row.remove();
                this.refreshCart();
                this.updateCartBadge(this.cartTotalQuantity);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }

    static updateProductTotal(row, price, quantity) {
        const totalCell = row.querySelector('.product-total');
        const total = price * quantity;
        totalCell.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    static refreshCart() {
        // Calcul des totaux
        let subtotal = 0;
        let totalQuantity = 0;

        document.querySelectorAll('tbody tr').forEach(row => {
            const price = parseFloat(row.dataset.price) || 0;
            const quantityInput = row.querySelector('.quantity-input');
            if (quantityInput) {
                const quantity = parseInt(quantityInput.value) || 0;
                subtotal += price * quantity;
                totalQuantity += quantity;
            }
        });

        const total = subtotal + this.SHIPPING_COST;
        
        // Mise à jour du sous-total et total
        const subtotalElement = document.getElementById('subtotal');
        if (subtotalElement) {
            subtotalElement.textContent = subtotal.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }
        
        const cartTotalElement = document.getElementById('cart-total');
        if (cartTotalElement) {
            cartTotalElement.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }

        // Mise à jour des variables statiques
        this.cartTotalPrice = total;
        this.cartTotalQuantity = totalQuantity;
    }

    static init() {
        // Pour la page produit
        const addToCartButton = document.getElementById('add-to-cart');
        if (addToCartButton) {
            addToCartButton.addEventListener('click', () => {
                const productId = addToCartButton.dataset.productId;
                const quantity = document.getElementById('quantity')?.value || 1;
                this.addToCart(productId, quantity);
            });
        }

        // Pour les boutons d'ajout rapide
        document.querySelectorAll('[data-quick-add-to-cart]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.dataset.quickAddToCart;
                this.addToCart(productId, 1);
            });
        });

        // Event listeners pour les boutons de quantité
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                const quantity = parseInt(this.dataset.quantity);
                Cart.updateQuantity(productId, quantity);
            });
        });

        // Event listeners pour les boutons de suppression
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                Cart.removeItem(productId);
            });
        });

        // Initialiser les totaux
        this.refreshCart();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    Cart.init();
});
