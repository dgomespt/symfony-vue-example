<script setup lang="ts">
import type { Meta } from '@/types';

defineEmits(['changePage'])

const model = defineModel()

function lastPage(){
    const m = model.value as Meta;
    return Math.ceil(m.total/m.itemsPerPage) || 1;
}

</script>

<template>
    <div class="text-center mb-5 text-sm">
        <p>
           <a v-if="modelValue.page > 1" @click.prevent="$emit('changePage', modelValue.page-1)" href="#">&lt;&lt;</a> <span>Page <b>{{ modelValue.page }}</b> of <b>{{ lastPage() }}</b></span> <a v-if="modelValue.page<lastPage()" href="#" @click.prevent="$emit('changePage', modelValue.page+1)">&gt;&gt;</a>
        </p>
        <p class="text-sm">
            Showing <b>{{ modelValue.start }}-{{ modelValue.end }}</b> of <b>{{ modelValue.total }}</b> servers
        </p>
    </div>
</template>