<?php include 'templates/header.php'; ?>

<div x-data="posApp()" x-init="fetchProducts()">

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left/Main Column: Product Selection -->
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg shadow">
                <!-- Search Bar -->
                <div>
                    <label for="search" class="block text-sm font-medium leading-6 text-gray-900">Search Products</label>
                    <div class="relative mt-2 flex items-center">
                        <input type="text" x-model="searchQuery" placeholder="Search by name or brand..." id="search" class="block w-full rounded-md border-0 py-1.5 pr-14 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <div class="absolute inset-y-0 right-0 flex py-1.5 pr-1.5">
                            <kbd class="inline-flex items-center rounded border border-gray-200 px-1 font-sans text-xs text-gray-400">⌘K</kbd>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="mt-6">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" style="max-height: 70vh; overflow-y: auto;">
                        <template x-for="product in filteredProducts" :key="product.id">
                            <div @click="addToCart(product)" 
                                 :class="{ 'opacity-50 cursor-not-allowed': product.stock_quantity <= 0 }"
                                 class="group relative flex flex-col justify-between rounded-lg border bg-white p-3 shadow-sm hover:shadow-md transition-shadow cursor-pointer">
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm" x-text="product.name"></p>
                                    <p class="text-xs text-gray-500" x-text="product.brand"></p>
                                </div>
                                <div class="mt-2 flex justify-between items-center">
                                    <p class="font-bold text-indigo-600" x-text="`$${product.price.toFixed(2)}`"></p>
                                    <p class="text-xs font-medium" 
                                       :class="{ 'text-red-600': product.stock_quantity <= 5 && product.stock_quantity > 0, 'text-gray-400': product.stock_quantity <= 0, 'text-green-600': product.stock_quantity > 5 }">
                                       Stock: <span x-text="product.stock_quantity"></span>
                                    </p>
                                </div>
                                <div x-show="product.stock_quantity <= 0" class="absolute inset-0 bg-gray-100 bg-opacity-50 flex items-center justify-center">
                                    <span class="text-red-500 font-bold text-sm">Out of Stock</span>
                                </div>
                            </div>
                        </template>
                         <template x-if="filteredProducts.length === 0">
                            <div class="col-span-full text-center py-10 text-gray-500">
                                <p>No products match your search.</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Cart -->
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg shadow sticky top-6">
                <h2 class="text-lg font-semibold text-gray-900">Current Sale</h2>
                <!-- Cart Items -->
                <div class="mt-4 space-y-3" style="max-height: 45vh; overflow-y: auto;">
                    <template x-for="item in cart" :key="item.product_id">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium text-sm text-gray-800" x-text="item.name"></p>
                                <p class="text-xs text-gray-500" x-text="`$${item.price.toFixed(2)}`"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="updateQuantity(item.product_id, -1)" class="rounded-full h-6 w-6 flex items-center justify-center bg-gray-200 hover:bg-gray-300">-</button>
                                <span class="w-6 text-center" x-text="item.quantity"></span>
                                <button @click="updateQuantity(item.product_id, 1)" class="rounded-full h-6 w-6 flex items-center justify-center bg-gray-200 hover:bg-gray-300">+</button>
                                <p class="font-semibold w-16 text-right" x-text="`$${(item.price * item.quantity).toFixed(2)}`"></p>
                            </div>
                        </div>
                    </template>
                    <template x-if="cart.length === 0">
                        <div class="text-center py-10 text-gray-500">
                            <p>Your cart is empty.</p>
                            <p class="text-xs mt-1">Click on a product to add it.</p>
                        </div>
                    </template>
                </div>

                <!-- Cart Summary -->
                <div class="mt-6 border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm font-medium text-gray-600">
                        <span>Subtotal</span>
                        <span x-text="`$${cartSubtotal.toFixed(2)}`"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span x-text="`$${cartTotal.toFixed(2)}`"></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 grid grid-cols-2 gap-3">
                    <button @click="clearCart()" :disabled="cart.length === 0" class="rounded-md bg-red-100 text-red-700 px-4 py-2 text-sm font-semibold shadow-sm hover:bg-red-200 disabled:opacity-50 disabled:cursor-not-allowed">Clear</button>
                    <button @click="completeSale()" :disabled="cart.length === 0" class="rounded-md bg-indigo-600 text-white px-4 py-2 text-sm font-semibold shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">Complete Sale</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Success Modal -->
    <div x-show="showSuccessModal" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showSuccessModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showSuccessModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" @click.away="showSuccessModal = false" class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Sale Completed!</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">The sale has been recorded successfully.</p>
                                    <p class="text-sm text-gray-500 mt-2">Transaction ID: <strong x-text="lastSaleId"></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button @click="showSuccessModal = false" type="button" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Start New Sale</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function posApp() {
    return {
        products: [],
        searchQuery: '',
        cart: [],
        showSuccessModal: false,
        lastSaleId: '',

        fetchProducts() {
            fetch('api/products_api.php')
                .then(response => response.json())
                .then(data => {
                    this.products = data;
                })
                .catch(error => console.error('Error fetching products:', error));
        },

        get filteredProducts() {
            if (this.searchQuery === '') {
                return this.products;
            }
            return this.products.filter(p =>
                p.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                p.brand.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },

        addToCart(product) {
            if (product.stock_quantity <= 0) {
                return; // Can't add out-of-stock items
            }
            const cartItem = this.cart.find(item => item.product_id === product.id);
            if (cartItem) {
                // Check if adding another exceeds stock
                if(cartItem.quantity < product.stock_quantity) {
                    cartItem.quantity++;
                } else {
                    alert('Cannot add more than available stock.');
                }
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1
                });
            }
        },

        updateQuantity(productId, change) {
            const cartItem = this.cart.find(item => item.product_id === productId);
            if (!cartItem) return;

            const product = this.products.find(p => p.id === productId);
            const newQuantity = cartItem.quantity + change;

            if (newQuantity > 0) {
                if (product && newQuantity > product.stock_quantity) {
                    alert('Cannot add more than available stock.');
                    return;
                }
                cartItem.quantity = newQuantity;
            } else {
                // Remove item if quantity is 0 or less
                this.cart = this.cart.filter(item => item.product_id !== productId);
            }
        },
        
        clearCart() {
            if(confirm('Are you sure you want to clear the cart?')) {
                this.cart = [];
            }
        },

        get cartSubtotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
        },

        get cartTotal() {
            // In a real app, you might add tax here. For now, it's the same as subtotal.
            return this.cartSubtotal;
        },

        completeSale() {
            if (this.cart.length === 0) return;

            const saleData = {
                items: this.cart.map(item => ({
                    product_id: item.product_id,
                    name: item.name,
                    quantity: item.quantity,
                    price_at_sale: item.price
                })),
                total_amount: this.cartTotal
            };

            fetch('api/sales_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(saleData)
            })
            .then(response => {
                if (!response.ok) throw new Error('Sale failed to process.');
                return response.json();
            })
            .then(data => {
                this.lastSaleId = data.sale_id;
                this.showSuccessModal = true;
                this.cart = []; // Clear cart on success
                this.fetchProducts(); // Refresh product list for updated stock
            })
            .catch(error => {
                console.error('Error completing sale:', error);
                alert('There was an error processing the sale.');
            });
        }
    }
}
</script>

<?php include 'templates/footer.php'; ?>
