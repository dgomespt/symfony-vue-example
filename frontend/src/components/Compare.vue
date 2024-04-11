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

    if(props.a !== undefined && props.b !== undefined) {
        props.a.ram !== props.b.ram ? hls.push('ram') : null
        props.a.hdd !== props.b.hdd ? hls.push('hdd') : null
        props.a.location !== props.b.location ? hls.push('location') : null
        props.a.price !== props.b.price ? hls.push('price') : null
    }
    return hls
}

</script>

<template>

    <div class="text-center text-sm mb-10">
        <h1><b>Server Comparison</b></h1>
        <div class="flex">
            
            <div v-if="!a || !b" class="mt-5 border-2 p-10">
                <p>Pick any two servers from the table</p>
            </div>
            <div v-else class="flex mt-5 border-t-2 border-r-2 border-b-2">
                <div class="border-l-2 p-5">
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
    </div>
    
</template>