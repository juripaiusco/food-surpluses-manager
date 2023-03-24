<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-gray-100 dark:bg-gray-900 pt-[100px]">
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500 m-auto" />
                <br>
                <h1 class="text-3xl">Emporio<span class="font-bold">App</span></h1>
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
            <slot />
        </div>
    </div>
</template>

<script>

export default {
    data() {

        console.log('');

        return {
        }

    }
}

</script>
