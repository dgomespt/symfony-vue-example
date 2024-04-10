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
    <a v-if="modelValue.page > 1" @click.prevent="$emit('changePage', modelValue.page-1)" href="#">&lt;</a> <span>{{ modelValue.page }}/{{ lastPage() }}</span> <a v-if="modelValue.page<lastPage()" href="#" @click.prevent="$emit('changePage', modelValue.page+1)">&gt;</a> <br />
    Showing {{ modelValue.start }}-{{ modelValue.end }} of {{ modelValue.total }} servers
</template>