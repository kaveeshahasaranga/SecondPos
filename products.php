<?php include 'templates/header.php'; ?>

<div x-data="productsApp()" x-init="fetchProducts()">

    <!-- Main content -->
    <div class="flex flex-col lg:flex-row gap-8">
    
        <!-- Products List -->
        <div class="w-full lg:w-2/3">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Products</h2>
                        <p class="mt-2 text-sm text-gray-700">A list of all the watches in your inventory.</p>
                    </div>
                </div>
                <div class="mt-8 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Name / Brand</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Price</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Stock</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <template x-for="product in products" :key="product.id">
                                        <tr>
                                            <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                <div class="font-medium text-gray-900" x-text="product.name"></div>
                                                <div class="mt-1 text-gray-500" x-text="product.brand"></div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500" x-text="`$${product.price.toFixed(2)}`"></td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                                <span x-text="product.stock_quantity" :class="{'text-red-600 font-bold': product.stock_quantity <= 5, 'text-green-600': product.stock_quantity > 5}"></span>
                                            </td>
                                            <td class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <button @click="startEdit(product)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="products.length === 0">
                                        <tr>
                                            <td colspan="4" class="text-center py-5 text-gray-500">No products found. Add one to get started.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Form -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-base font-semibold leading-7 text-gray-900" x-text="formTitle">Add a new product</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600" x-text="formDescription">Fill in the details below.</p>
                
                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Product Name</label>
                        <div class="mt-2">
                            <input type="text" x-model="formData.name" id="name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    <div>
                        <label for="brand" class="block text-sm font-medium leading-6 text-gray-900">Brand</label>
                        <div class="mt-2">
                            <input type="text" x-model="formData.brand" id="brand" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-1/2">
                            <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Price</label>
                            <div class="relative mt-2 rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" x-model="formData.price" id="price" required step="0.01" min="0" class="block w-full rounded-md border-0 py-1.5 pl-7 pr-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                        <div class="w-1/2">
                             <label for="stock" class="block text-sm font-medium leading-6 text-gray-900">Stock Quantity</label>
                            <div class="mt-2">
                                <input type="number" x-model="formData.stock_quantity" id="stock" required min="0" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description (Optional)</label>
                        <div class="mt-2">
                            <textarea x-model="formData.description" id="description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="flex items-center justify-end gap-x-4">
                        <button type="button" x-show="isEditing" @click="resetForm()" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600" x-text="submitButtonText">Save Product</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
function productsApp() {
    return {
        products: [],
        isEditing: false,
        formTitle: 'Add a new product',
        formDescription: 'Fill in the details below.',
        submitButtonText: 'Save Product',
        formData: {
            id: null,
            name: '',
            brand: '',
            price: '',
            stock_quantity: '',
            description: ''
        },
        fetchProducts() {
            fetch('api/products_api.php')
                .then(response => response.json())
                .then(data => {
                    this.products = data;
                })
                .catch(error => console.error('Error fetching products:', error));
        },
        startEdit(product) {
            this.isEditing = true;
            this.formTitle = 'Edit Product';
            this.formDescription = `You are now editing '${product.name}'.`;
            this.submitButtonText = 'Update Product';
            // Clone the product data to avoid reactivity issues
            this.formData = { ...product };
        },
        resetForm() {
            this.isEditing = false;
            this.formTitle = 'Add a new product';
            this.formDescription = 'Fill in the details below.';
            this.submitButtonText = 'Save Product';
            this.formData = { id: null, name: '', brand: '', price: '', stock_quantity: '', description: '' };
        },
        submitForm() {
            const url = this.isEditing ? `api/products_api.php` : 'api/products_api.php';
            const method = this.isEditing ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(this.formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(() => {
                this.fetchProducts(); // Refresh the list
                this.resetForm();     // Clear the form
            })
            .catch(error => console.error('Error submitting form:', error));
        }
    }
}
</script>

<?php include 'templates/footer.php'; ?>
