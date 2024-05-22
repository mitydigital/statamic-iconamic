<template>

    <div class="flex">
        <v-select
            ref="input"
            :calculate-position="positionOptions"
            :clearable="config.clearable"
            :close-on-select="true"
            :create-option="(value) => ({ value, label: value })"
            :disabled="config.disabled || isReadOnly"
            :multiple="false"
            :name="name"
            :options="options"
            :placeholder="__(config.placeholder)"
            :searchable="config.searchable"
            :value="this.value"
            append-to-body
            class="flex-1"
            @input="vueSelectUpdated"
            @search:focus="$emit('focus')"
            @search:blur="$emit('blur')">
            <template #option="{ label, svg }">
                <div class="flex items-center">
                    <span class="flex-none iconamic-is-svg block w-5 h-5" v-html="svg"></span>
                    <span class="text-xs ml-4 truncate">{{ label }}</span>
                </div>
            </template>
            <template #selected-option="{ label }">
                <span class="flex items-center">
                <span class="flex-none iconamic-is-svg block w-5 h-5" v-html="meta.icons[label]"></span>
                <span class="text-xs ml-4 truncate">{{ label }}</span>
                </span>
            </template>
            <template #no-options>
                <div class="text-sm text-gray-700 rtl:text-right ltr:text-left py-2 px-4"
                     v-text="__('No options to choose from.')"/>
            </template>
        </v-select>
    </div>

</template>

<script>

import PositionsSelectOptions from './../../../vendor/statamic/cms/resources/js/mixins/PositionsSelectOptions';

export default {

    mixins: [Fieldtype, PositionsSelectOptions],

    computed: {
        options() {
            let options = [];

            for (let svg in this.meta.icons) {
                options.push({
                    value: svg,
                    label: svg,
                    svg: this.meta.icons[svg]
                });
            }

            return options;
        }
    },

    methods: {
        focus() {
            this.$refs.input.focus();
        },

        vueSelectUpdated(value) {
            if (value) {
                this.update(value.value)
            } else {
                this.update(null);
            }
        },

    }

};
</script>
<style>
.iconamic-is-svg svg {
    width: 28px;
    height: 28px;
    object-fit: contain;
}
</style>
