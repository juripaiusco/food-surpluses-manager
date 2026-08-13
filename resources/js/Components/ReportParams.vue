<script setup>

defineProps({
    paramsSchema: {
        type: Array,
        default: () => []
    },
    routeSearch: String,
    filters: Object,
    disabled: {
        type: Boolean,
        default: false
    }
});

</script>

<template>

    <div class="inline-flex gap-2">

        <div v-for="p in paramsSchema" :key="p.name">

            <label class="text-xs block">{{ p.label }}</label>

            <input :type="p.type === 'date' ? 'date' : 'text'"
                   :disabled="disabled"
                   class="form-control"
                   v-model="params.params[p.name]" />

        </div>

    </div>

</template>

<script>
export default {
    data() {

        const initial = {};

        (this.paramsSchema || []).forEach(p => {
            initial[p.name] = this.filters?.params?.[p.name] ?? '';
        });

        return {
            params: {
                s: this.filters.s,
                orderby: this.filters.orderby,
                ordertype: this.filters.ordertype,
                params: initial,
            }
        }
    },
    watch: {
        params: {
            handler() {

                let params = this.params;

                Object.keys(params).forEach(k => {
                    if (params[k] === '' || params[k] === null) {
                        delete params[k];
                    }
                })

                if (params.params) {
                    Object.keys(params.params).forEach(k => {
                        if (params.params[k] === '' || params.params[k] === null) {
                            delete params.params[k];
                        }
                    })
                }

                this.$inertia.get(
                    this.routeSearch.includes('/') === true ? this.routeSearch : this.route(this.routeSearch),
                    params,
                    {
                        replace: true,
                        preserveState: true,
                        preserveScroll: true,
                    }
                );
            },
            deep: true,
        }
    }
}
</script>
