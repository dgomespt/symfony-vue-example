<script setup lang="ts">
import { onMounted } from 'vue';
import CompareCard from './CompareCard.vue'
import type { Server } from '../types';

const props = defineProps<{
    a: Server,
    b: Server
}>()



function hls(): string[] {
    const hls: string[] = [];
    props.a.ram !== props.b.ram ? hls.push('ram') : null
    props.a.hdd !== props.b.hdd ? hls.push('hdd') : null
    props.a.location !== props.b.location ? hls.push('location') : null
    props.a.price !== props.b.price ? hls.push('price') : null
    return hls
}

</script>

<template>

    <div class="text-center">
        <h1><b>Server Comparison</b></h1>
        <div class="flex">
            
            <div class="w-32 border-rounded border-2 mb-5 p-5">
                <p><b>Model</b></p>
                <p><b>Ram</b></p>
                <p><b>HDD</b></p>
                <p><b>Location</b></p>
                <p><b>Price</b></p>
            </div>
            <CompareCard :server="a" :highlights="hls()"/>
            <CompareCard :server="b" :highlights="hls()"/>
        </div>
    </div>
    
</template>