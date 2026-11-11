<script setup>
import {Fieldtype} from '@statamic/cms';
import {Combobox} from '@statamic/cms/ui';
import {computed, onMounted, ref} from 'vue';
import {loadIcons} from '../iconCache.js';

const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);
const {isReadOnly, defineReplicatorPreview, update} = Fieldtype.use(emit, props);

// Start with just the selected icon(s) so the value shows straight away, then swap in the shared full list.
const icons = ref({...(props.meta.selected ?? {})});

onMounted(() => {
    loadIcons(props.meta.url).then((all) => {
        icons.value = all;
    });
});

const selectedOptions = computed(() => {
    let selections = props.value === null ? [] : props.value;

    if (typeof selections === 'string' || typeof selections === 'number') {
        selections = [selections];
    }

    return selections.map((value) => {
        return options.value.find((option) => option.value === value) ?? {label: value, value};
    });
});

const options = computed(() => {
    let options = [];

    for (let svg in icons.value) {
        options.push({
            value: svg,
            label: svg,
            svg: icons.value[svg]
        });
    }

    return options;
});

defineReplicatorPreview(() => selectedOptions.value.map((option) => option.label).join(', '));

function comboboxUpdated(value) {
    update(value || null);
}
</script>

<template>

    <Combobox
        :id="id"
        :clearable="config.clearable"
        :disabled="config.disabled"
        :label-html="config.label_html"
        :max-selections="config.max_items"
        :model-value="value"
        :multiple="config.multiple"
        :options="options"
        :placeholder="__(config.placeholder)"
        :read-only="isReadOnly"
        :searchable="true"
        class="w-full"
        @update:modelValue="comboboxUpdated"
    >
        <template #selected-option="{ option }">
            <div class="flex items-center gap-x-4">
                <span class="flex-none iconamic-is-svg block size-5" v-html="option.svg"></span>
                <span class="text-xs truncate">{{ option.label }}</span>
            </div>
        </template>
        <template #no-options>
            <div class="px-4">
                {{ __('No options to choose from.') }}
            </div>
        </template>
        <template #option="option">
            <div class="flex items-center gap-x-4">
                <span class="flex-none iconamic-is-svg block size-5" v-html="option.svg"></span>
                <span class="text-xs truncate">{{ option.label }}</span>
            </div>
        </template>
    </Combobox>

</template>

<style>
.iconamic-is-svg svg {
    width: 100%;
    height: 100%;
    object-fit: contain;
}
</style>
