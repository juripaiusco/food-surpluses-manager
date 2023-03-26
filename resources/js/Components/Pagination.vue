<script setup>

import {Link} from "@inertiajs/vue3";

defineProps({
    links: Array,
})

</script>

<template>

    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1">
            <template v-for="(link, k) in links" :key="k">

                <div v-if="link.url === null"
                     class="mr-1 mb-1 px-4 py-2 text-sm leading-4 text-gray-400 border rounded"
                     v-html="link.label.replace('Next', '').replace('Previous', '')" />

                <Link
                    v-else
                    class="mr-1 mb-1 px-4 py-2 text-sm leading-4 border rounded text-gray-500 hover:bg-sky-100 hover:text-sky-400 focus:border-sky-500 focus:text-sky-500"
                    :class="{ 'bg-sky-100 text-sky-400 border-sky-200 hover:border-sky-500': link.active }"
                    :href="link.url"
                    v-html="link.label.replace('Next', '').replace('Previous', '')" />

            </template>
        </div>
    </div>

</template>
