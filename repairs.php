<?php include 'templates/header.php'; ?>

<div x-data="repairsApp()" x-init="fetchRepairs()">

    <!-- Main content -->
    <div class="flex flex-col lg:flex-row gap-8">
    
        <!-- Repairs List -->
        <div class="w-full lg:w-2/3">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h2 class="text-base font-semibold leading-6 text-gray-900">Repair Jobs</h2>
                        <p class="mt-2 text-sm text-gray-700">A list of all current and past repair jobs.</p>
                    </div>
                </div>
                <div class="mt-8 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Customer & Watch</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Issue</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Price</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Edit</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <template x-for="repair in repairs" :key="repair.repair_id">
                                        <tr>
                                            <td class="whitespace-nowrap py-5 pl-4 pr-3 text-sm sm:pl-0">
                                                <div class="font-medium text-gray-900" x-text="repair.customer_name"></div>
                                                <div class="mt-1 text-gray-500" x-text="repair.watch_details"></div>
                                            </td>
                                            <td class="whitespace-normal px-3 py-5 text-sm text-gray-500" style="max-width: 200px;" x-text="repair.issue_description"></td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                                      :class="{
                                                          'bg-blue-50 text-blue-700 ring-blue-600/20': repair.status === 'Received',
                                                          'bg-yellow-50 text-yellow-800 ring-yellow-600/20': repair.status === 'In Progress' || repair.status === 'Awaiting Parts',
                                                          'bg-green-50 text-green-700 ring-green-600/20': repair.status === 'Completed',
                                                          'bg-gray-50 text-gray-600 ring-gray-500/10': repair.status === 'Collected'
                                                      }"
                                                      x-text="repair.status">
                                                </span>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500" x-text="`$${repair.price.toFixed(2)}`"></td>
                                            <td class="relative whitespace-nowrap py-5 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <button @click="startEdit(repair)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="repairs.length === 0">
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-gray-500">No repair jobs found. Add one to get started.</td>
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
                <h2 class="text-base font-semibold leading-7 text-gray-900" x-text="formTitle">Log a new repair job</h2>
                
                <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                    <!-- This section is for adding a new repair -->
                    <template x-if="!isEditing">
                        <div class="space-y-6">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium leading-6 text-gray-900">Customer Name</label>
                                <input type="text" x-model="formData.customer_name" id="customer_name" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                            </div>
                            <div>
                                <label for="customer_contact" class="block text-sm font-medium leading-6 text-gray-900">Customer Contact (Phone/Email)</label>
                                <input type="text" x-model="formData.customer_contact" id="customer_contact" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                            </div>
                             <div>
                                <label for="watch_details" class="block text-sm font-medium leading-6 text-gray-900">Watch Details (Brand, Model)</label>
                                <input type="text" x-model="formData.watch_details" id="watch_details" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
                            </div>
                            <div>
                                <label for="issue_description" class="block text-sm font-medium leading-6 text-gray-900">Issue Description</label>
                                <textarea x-model="formData.issue_description" id="issue_description" rows="3" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300"></textarea>
                            </div>
                        </div>
                    </template>

                    <!-- This section is for editing an existing repair -->
                    <template x-if="isEditing">
                        <div class="space-y-6">
                             <div>
                                <h3 class="font-medium text-gray-900" x-text="`Editing Job for: ${formData.customer_name}`"></h3>
                                <p class="text-sm text-gray-500" x-text="formData.watch_details"></p>
                             </div>
                             <div>
                                <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                                <select x-model="formData.status" id="status" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option>Received</option>
                                    <option>In Progress</option>
                                    <option>Awaiting Parts</option>
                                    <option>Completed</option>
                                    <option>Collected</option>
                                </select>
                             </div>
                             <div>
                                <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Repair Price</label>
                                <div class="relative mt-2 rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" x-model="formData.price" id="price" step="0.01" min="0" class="block w-full rounded-md border-0 py-1.5 pl-7 pr-2 text-gray-900 ring-1 ring-inset ring-gray-300">
                                </div>
                             </div>
                        </div>
                    </template>

                    <!-- Form Buttons -->
                    <div class="flex items-center justify-end gap-x-4 pt-4">
                        <button type="button" x-show="isEditing" @click="resetForm()" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500" x-text="submitButtonText">Log Job</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function repairsApp() {
    return {
        repairs: [],
        isEditing: false,
        formTitle: 'Log a new repair job',
        submitButtonText: 'Log Job',
        formData: {
            repair_id: null,
            customer_name: '',
            customer_contact: '',
            watch_details: '',
            issue_description: '',
            status: 'Received',
            price: 0
        },
        fetchRepairs() {
            fetch('api/repairs_api.php')
                .then(response => response.json())
                .then(data => {
                    // Sort by date received, newest first
                    this.repairs = data.sort((a, b) => new Date(b.date_received) - new Date(a.date_received));
                })
                .catch(error => console.error('Error fetching repairs:', error));
        },
        startEdit(repair) {
            this.isEditing = true;
            this.formTitle = 'Update Repair Status & Price';
            this.submitButtonText = 'Update Job';
            // Clone data to avoid reactive issues
            this.formData = { ...repair };
        },
        resetForm() {
            this.isEditing = false;
            this.formTitle = 'Log a new repair job';
            this.submitButtonText = 'Log Job';
            this.formData = { repair_id: null, customer_name: '', customer_contact: '', watch_details: '', issue_description: '', status: 'Received', price: 0 };
        },
        submitForm() {
            const isUpdating = this.isEditing;
            const url = 'api/repairs_api.php';
            const method = isUpdating ? 'PUT' : 'POST';
            
            // For PUT, we only need to send the ID, status, and price
            const body = isUpdating 
                ? { repair_id: this.formData.repair_id, status: this.formData.status, price: this.formData.price }
                : this.formData;

            fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body)
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(() => {
                this.fetchRepairs();
                this.resetForm();
            })
            .catch(error => console.error('Error submitting form:', error));
        }
    }
}
</script>

<?php include 'templates/footer.php'; ?>
