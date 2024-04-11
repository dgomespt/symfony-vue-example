<script setup lang="ts">
import axios from 'axios'
import type { Meta, RequestParams, Server } from '../types';
import { ref, watch } from 'vue'

import Filters from './Filters.vue'
import Pagination from './Pagination.vue'
import Compare from './Compare.vue'

import { ChevronUpIcon, ChevronDownIcon } from '@heroicons/vue/16/solid'

const apiUrl = (import.meta.env?.API_BASE_URL || 'http://localhost') + '/servers'

const apiServerPresent = ref<boolean>(false)

const currentRequest = ref<RequestParams>({
  'page': 1,
  'itemsPerPage': 25
})

const meta = ref<Meta>({})
const servers = ref<Server[]>([])

function loadPage(request: RequestParams){

    getPage(request).then((data) => {
      apiServerPresent.value=true
      servers.value = data
  }).catch((error) => {
    console.error(error)
    apiServerPresent.value=false
  })

};

loadPage(currentRequest.value);


function fetchServers(requestParams: RequestParams): Promise<any> {

  const url: URL = new URL(apiUrl);
  url.searchParams.append('page', requestParams?.page.toString());
  url.searchParams.append('itemsPerPage', requestParams?.itemsPerPage?.toString() || '10');

  if (requestParams.filters != null) {
      for(const [key, value] of Object.entries(requestParams.filters)){
      url.searchParams.append('filters[' + key + ']', value);
    }
  }
  

  if (requestParams.order) {
    Object.keys(requestParams.order).forEach((key) => {
      url.searchParams.append('order[' + key + ']', requestParams.order?.[key] || 'asc');
    });
  }

  return axios.get(url.href)
}

function order(field: string): void {

  let r = currentRequest.value;

  if (r.order) {
    r.order = {
      [field]: r.order[field] === 'asc' ? 'desc' : 'asc'
    };
  } else {
    r.order = {
      [field]: 'asc'
    };
  }

  loadPage(r);

}

function applyFilters(appliedFilters: any): void {

  let r = currentRequest.value;
  r.filters = {}

  for(const [key, value] of Object.entries(appliedFilters)){
    r.filters[key] = value as string
  }
  
  loadPage(r)
}

const selectedServers = ref<Server[]>([])


async function getPage(requestParams: RequestParams): Promise<Server[]> {
    const response = await fetchServers(requestParams)

    if(response.status !== 200){
      throw new Error(response.statusText)
    }

    meta.value = response.data.meta
    const results = response.data.data
    const servers = results.map((server: any) => ({
      id: server.id,
      model: server.model,
      ram: server.ram,
      hdd: server.hdd,
      location: server.location,
      price: server.price
    }))

    return servers
}

function requestPage(page: number){

  const r = currentRequest.value;
  r.page = page;
  loadPage(r)
}


watch<Server[]>(selectedServers, async (servers) => {
  
  if(servers.length > 2){
    selectedServers.value.splice(0,servers.length-2);
  }

});

</script>

<template>
  <div class="flex flex-col items-center justify-center align-top">
    <Filters @filtersChanged="applyFilters"/>
    <Pagination 
      @changePage="requestPage"
      v-model="meta"
    />
    <table v-if="apiServerPresent" class="table-fixed text-sm">
      <thead>
        <tr class="cursor-pointer">
          <th></th>
          <th @click.prevent="order('model')">Model<ChevronUpIcon class="h-4 w-4 inline ml-5" :class="{ 'stroke-blue-500': currentRequest.order?.model === 'asc' }" /><ChevronDownIcon class="h-4 w-4 inline" :class="{ 'stroke-blue-500': currentRequest.order?.model === 'desc' }" /></th>
          <th @click.prevent="order('ram')">RAM<ChevronUpIcon class="h-4 w-4 inline ml-5" :class="{ 'stroke-blue-500': currentRequest.order?.ram === 'asc' }" /><ChevronDownIcon class="h-4 w-4 inline" :class="{ 'stroke-blue-500': currentRequest.order?.ram === 'desc' }" /></th>
          <th @click.prevent="order('hdd')">HDD<ChevronUpIcon class="h-4 w-4 inline ml-5" :class="{ 'stroke-blue-500': currentRequest.order?.hdd === 'asc' }" /><ChevronDownIcon class="h-4 w-4 inline" :class="{ 'stroke-blue-500': currentRequest.order?.hdd === 'desc' }" /></th>
          <th @click.prevent="order('location')">Location<ChevronUpIcon class="h-4 w-4 inline ml-5" :class="{ 'stroke-blue-500': currentRequest.order?.location === 'asc' }" /><ChevronDownIcon class="h-4 w-4 inline" :class="{ 'stroke-blue-500': currentRequest.order?.location === 'desc' }" /></th>
          <th @click.prevent="order('price')">Price<ChevronUpIcon class="h-4 w-4 inline ml-2" :class="{ 'stroke-blue-500': currentRequest.order?.price === 'asc' }" /><ChevronDownIcon class="h-4 w-4 inline" :class="{ 'stroke-blue-500': currentRequest.order?.price === 'desc' }" /></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="server in servers" :key="server.id">
          <td class="pr-2"><input type="checkbox" v-model="selectedServers" :value="server"></td>
          <td class="pr-2">{{ server.model }}</td>
          <td class="pr-2">{{ server.ram }}</td>
          <td class="pr-2">{{ server.hdd }}</td>
          <td class="pr-2">{{ server.location }}</td>
          <td class="pr-2">{{ server.price }}</td>
        </tr>
      </tbody>
    </table>
    <div v-else>
      Unable to fetch server list
    </div>
    <Pagination class="mt-5"
      @changePage="requestPage"
      v-model="meta"
    />

    <Compare class="flex flex-col items-center justify-center align-top" :a="selectedServers?.[0]" :b="selectedServers?.[1]" />
    
  </div>

</template>
