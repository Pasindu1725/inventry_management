<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    items: Object,
    search: String,
});

const form = useForm({
    search: props.search || '',
});

const performSearch = () => {
    router.get(route('items.index'), { search: form.search }, {
        preserveState: true,
        replace: true,
    });
};

const showAddStockModal = ref(false);
const showDeductStockModal = ref(false);
const selectedItem = ref(null);

const addStockForm = useForm({
    quantity: 0,
    notes: '',
});

const deductStockForm = useForm({
    quantity: 0,
    notes: '',
});

const openAddStockModal = (item) => {
    selectedItem.value = item;
    addStockForm.quantity = 0;
    addStockForm.notes = '';
    showAddStockModal.value = true;
};

const openDeductStockModal = (item) => {
    selectedItem.value = item;
    deductStockForm.quantity = 0;
    deductStockForm.notes = '';
    showDeductStockModal.value = true;
};

const submitAddStock = () => {
    addStockForm.post(route('items.add-stock', selectedItem.value.id), {
        onSuccess: () => {
            showAddStockModal.value = false;
            selectedItem.value = null;
            addStockForm.reset();
            // Refresh the page data
            router.reload({ preserveState: true });
        },
        onError: (errors) => {
            console.error('Add stock error:', errors);
        }
    });
};

const submitDeductStock = () => {
    deductStockForm.post(route('items.deduct-stock', selectedItem.value.id), {
        onSuccess: () => {
            showDeductStockModal.value = false;
            selectedItem.value = null;
            deductStockForm.reset();
            // Refresh the page data
            router.reload({ preserveState: true });
        },
        onError: (errors) => {
            console.error('Deduct stock error:', errors);
        }
    });
};
</script>

<template>
    <Head title="Inventory Items" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Items</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <form @submit.prevent="performSearch" class="flex items-center">
                                <TextInput
                                    id="search"
                                    type="text"
                                    class="mt-1 block w-64"
                                    v-model="form.search"
                                    placeholder="Search by item name..."
                                />
                                <PrimaryButton type="submit" class="ml-2">Search</PrimaryButton>
                            </form>
                            <Link :href="route('items.create')">
                                <PrimaryButton>Add New Item</PrimaryButton>
                            </Link>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="item in items.data" :key="item.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ item.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ item.measurement_unit?.symbol }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <PrimaryButton @click="openAddStockModal(item)" class="mr-2">Add Stock</PrimaryButton>
                                            <PrimaryButton @click="openDeductStockModal(item)" class="mr-2">Deduct Stock</PrimaryButton>
                                            <Link :href="route('items.show', item.id)" class="text-indigo-600 hover:text-indigo-900">View History</Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex space-x-1">
                            <template v-for="link in items.links" :key="link.label">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-2 text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:border-indigo-300 focus:ring focus:ring-indigo-200 active:bg-gray-50 active:text-gray-800 transition ease-in-out duration-150"
                                    :class="{ 'bg-indigo-500 text-white hover:text-white': link.active }"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="px-3 py-2 text-sm leading-4 font-medium rounded-md text-gray-400 bg-white cursor-not-allowed"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Stock Modal -->
        <div v-if="showAddStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Add Stock to {{ selectedItem?.name }}</h3>
                <form @submit.prevent="submitAddStock">
                    <div class="mb-4">
                        <InputLabel for="add_quantity" value="Quantity" />
                        <TextInput
                            id="add_quantity"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full"
                            v-model="addStockForm.quantity"
                            required
                        />
                        <InputError :message="addStockForm.errors.quantity" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="add_notes" value="Notes (Optional)" />
                        <TextInput
                            id="add_notes"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="addStockForm.notes"
                        />
                        <InputError :message="addStockForm.errors.notes" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-4 mt-4">
                        <PrimaryButton type="submit" :disabled="addStockForm.processing">Add Stock</PrimaryButton>
                        <PrimaryButton @click="showAddStockModal = false" type="button" class="bg-gray-500 hover:bg-gray-700">Cancel</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- Deduct Stock Modal -->
        <div v-if="showDeductStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="relative p-5 border w-96 shadow-lg rounded-md bg-white">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Deduct Stock from {{ selectedItem?.name }}</h3>
                <form @submit.prevent="submitDeductStock">
                    <div class="mb-4">
                        <InputLabel for="deduct_quantity" value="Quantity" />
                        <TextInput
                            id="deduct_quantity"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full"
                            v-model="deductStockForm.quantity"
                            required
                        />
                        <InputError :message="deductStockForm.errors.quantity" class="mt-2" />
                    </div>
                    <div class="mb-4">
                        <InputLabel for="deduct_notes" value="Notes (Optional)" />
                        <TextInput
                            id="deduct_notes"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="deductStockForm.notes"
                        />
                        <InputError :message="deductStockForm.errors.notes" class="mt-2" />
                    </div>
                    <div class="flex justify-end gap-4 mt-4">
                        <PrimaryButton type="submit" :disabled="deductStockForm.processing">Deduct Stock</PrimaryButton>
                        <PrimaryButton @click="showDeductStockModal = false" type="button" class="bg-gray-500 hover:bg-gray-700">Cancel</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>