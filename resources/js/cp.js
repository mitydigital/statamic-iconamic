import IconamicField from './fieldtypes/Iconamic.vue';
import IconamicIndexField from './fieldtypes/IconamicIndex.vue';

Statamic.booting(() => {
    Statamic.$components.register('iconamic-fieldtype', IconamicField);
    Statamic.$components.register('iconamic-fieldtype-index', IconamicIndexField);
});
