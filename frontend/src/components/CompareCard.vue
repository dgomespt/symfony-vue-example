<script setup lang="ts">
import type { Server } from '../types';

defineProps<{
    server: Server,
    highlights: string[]
}>()

</script>

<template>
    <div v-if="server" class="border-rounded border-2 mb-5 p-5">
        <h3>{{ server.model }}</h3>
        <p :class="{ 'text-red-500': highlights.includes('ram')}">{{ server.ram }}</p>
        <p :class="{ 'text-red-500': highlights.includes('hdd')}">{{ server.hdd }}</p>
        <p :class="{ 'text-red-500': highlights.includes('location')}">{{ server.location }}</p>
        <p :class="{ 'text-red-500': highlights.includes('price')}">{{ server.price }}</p>
    </div>
    <div v-else>
        <p>Pick a server from the list</p>
    </div>
</template>