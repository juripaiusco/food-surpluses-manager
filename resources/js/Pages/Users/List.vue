<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
</script>

<template>

    <Head title="Volontari" />

    <AuthenticatedLayout>

        <template #header>
            <div class="inline-flex w-full">
                <div class="w-1/2">
                    <h2 class="text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        <span class="font-semibold">Volontari</span>
                    </h2>
                </div>
                <div class="text-right w-1/2">

                </div>
            </div>
        </template>

        <div class="container mt-8 p-3 bg-white dark:bg-gray-800 dark:text-white shadow-sm rounded-lg">

            <div class="p-6">

                lista volontari

            </div>

        </div>

    </AuthenticatedLayout>

</template>
