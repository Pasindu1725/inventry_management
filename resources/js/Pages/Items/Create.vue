<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    measurementUnits: Array,
});

const form = useForm({
    name: '',
    description: '',
    quantity: 0,
    measurement_unit_id: '',
});

const submit = () => {
    form.post(route('items.store'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Create Item" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create New Item</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="max-w-xl mx-auto">
                            <div class="mb-4">
                                <InputLabel for="name" value="Item Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="description" value="Description (Optional)" />
                                <textarea
                                    id="description"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    v-model="form.description"
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="quantity" value="Initial Quantity" />
                                <TextInput
                                    id="quantity"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    v-model="form.quantity"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.quantity" />
                            </div>

                            <div class="mb-4">
                                <InputLabel for="measurement_unit_id" value="Measurement Unit" />
                                <select
                                    id="measurement_unit_id"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                                    v-model="form.measurement_unit_id"
                                    required
                                >
                                    <option value="" disabled>Select a unit</option>
                                    <option v-for="unit in measurementUnits" :key="unit.id" :value="unit.id">
                                        {{ unit.name }} ({{ unit.symbol }})
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="form.errors.measurement_unit_id" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <Link :href="route('items.index')" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                    Cancel
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                    Add Item
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
