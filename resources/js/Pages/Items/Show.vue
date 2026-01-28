<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { format } from 'date-fns';

const props = defineProps({
    item: Object,
});

const formattedDate = (dateString) => {
    return format(new Date(dateString), 'PPPppp');
};
</script>

<template>
    <Head :title="item.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Item: {{ item.name }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Item Details</h3>
                            <p><strong>Name:</strong> {{ item.name }}</p>
                            <p><strong>Description:</strong> {{ item.description || 'N/A' }}</p>
                            <p><strong>Current Quantity:</strong> {{ item.quantity }} {{ item.measurement_unit?.symbol }}</p>
                            <Link :href="route('items.index')" class="text-indigo-600 hover:text-indigo-900 mt-4 block">
                                &larr; Back to Items
                            </Link>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory History</h3>
                            <div v-if="item.inventory_transactions && item.inventory_transactions.length > 0">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performed By</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="transaction in item.inventory_transactions" :key="transaction.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium"
                                                :class="{
                                                    'text-green-600': transaction.type === 'addition',
                                                    'text-red-600': transaction.type === 'deduction',
                                                }"
                                            >
                                                {{ transaction.type === 'addition' ? 'Addition' : 'Deduction' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.quantity }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.notes || 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ transaction.user?.name || 'Unknown' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formattedDate(transaction.transaction_date) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p v-else class="text-gray-500">No inventory transactions found for this item.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
