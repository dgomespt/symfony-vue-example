<script setup lang="ts">
import axios from 'axios'
import type { Meta, RequestParams, Server } from '../types';
import { ref } from 'vue'

import Filters from './Filters.vue'

const apiUrl = 'http://localhost/servers'
const apiServerPresent = ref<boolean>(false)

const currentRequest = ref<RequestParams>({
  'page': 1,
  'itemsPerPage': 10
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
</script>

<template>
  <div>
    <Filters @filtersChanged="applyFilters"/>
    <table v-if="apiServerPresent">
      <thead>
        <tr>
          <th @click="order('model')">Model</th>
          <th @click="order('ram')">RAM</th>
          <th @click="order('hdd')">HDD</th>
          <th @click="order('location')">Location</th>
          <th @click="order('price')">Price</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="server in servers" :key="server.id">
          <td>{{ server.model }}</td>
          <td>{{ server.ram }}</td>
          <td>{{ server.hdd }}</td>
          <td>{{ server.location }}</td>
          <td>{{ server.price }}</td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5">Showing {{ servers.length }} of {{ meta.total || servers.length }} servers</td>
        </tr>
      </tfoot>
    </table>
    <div v-else>
      Unable to fetch server list<br/>
      Make sure you are running the backend at <a :href="apiUrl">{{apiUrl}}</a>
    </div>
  </div>
</template>
