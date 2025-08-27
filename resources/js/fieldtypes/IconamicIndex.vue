<template>
    <div>
        <div class="w-auto h-4" v-html="value"></div>
    </div>
</template>

<script>

import {IndexFieldtypeMixin} from '@statamic/cms';

export default {
    mixins: [IndexFieldtypeMixin],
}
</script>