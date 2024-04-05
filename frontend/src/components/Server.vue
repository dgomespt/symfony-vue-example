<script setup lang="ts">
import axios from 'axios'
import { Meta, Server, SortStates} from '../types'
import { ref, onMounted } from 'vue'

const apiUrl = 'http://localhost:44000/servers'

const apiServerPresent = ref<boolean>(false)

const meta: Meta = ref({
  itemsPerPage: 500
})

const sortStates = ref<SortStates>({
  'model': 1,
  'ram': 1,
  'hdd': 1,
  'location': 1
})

const filterValues = ref({})
const servers = ref([])

const beforeFiltering = ref([])

getPage().then((data) => {
  apiServerPresent.value=true
  servers.value = data
  filterValues.value = {
    model: getFilterValues('model'),
    ram: getFilterValues('ram'),
    hdd: getFilterValues('hdd'),
    location: getFilterValues('location')
  }
  beforeFiltering.value = data
}).catch((error) => apiServerPresent.value=false)

function fetchServers(page): Promise<any> {
  return axios.get(apiUrl + '?page=' + page + '&itemsPerPage=' + meta.value.itemsPerPage, {
    headers: {
      Accept: 'application/vnd.api+json'
    }
  })
}

function sort(field: string, parser?: any): void {
  if (sortStates.value[field] === undefined) {
    sortStates.value[field] = 1
  } else {
    sortStates.value[field] = -sortStates.value[field]
  }

  servers.value.sort((a, b) => {
    let val1 = a[field]
    let val2 = b[field]

    if (parser) {
      val1 = parser(val1)
      val2 = parser(val2)
    }

    return val1 > val2 ? sortStates.value[field] : -sortStates.value[field]
  })
}

function getFilterValues(field: string) {
  const unique = new Set()

  servers.value.forEach((server) => {
    unique.add(server[field])
  })

  return Array.from(unique).sort()
}

function filterByValue(field: string, event: Event) {
  servers.value = beforeFiltering.value
  if (event.target.value === 'all') {
    return
  }
  servers.value = servers.value.filter((server) => server[field] === event.target.value)
}

function priceParser(price: string) {
  return parseFloat(price.replace(/€/g, ''))
}

function ramParser(ram: string) {
  try {
    return parseInt(ram.match(/\d+/g)[0])
  }
  catch (error) {
    return ram;
  }

}

async function getPage(page: number = 1): Promise<Server[]> {
    const response = await fetchServers(page)
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
    <table v-if="apiServerPresent">
      <thead>
        <tr>
          <th @click="sort('model')">Model</th>
          <th @click="sort('ram', ramParser)">RAM</th>
          <th @click="sort('hdd')">HDD</th>
          <th @click="sort('location')">Location</th>
          <th @click="sort('price', priceParser)">Price</th>
        </tr>
        <tr>
          <th>
            <select @change="filterByValue('model', $event)">
              <option value="all"></option>
              <option v-for="value in filterValues.model" :key="value">{{ value }}</option>
            </select>
          </th>
          <th>
            <select @change="filterByValue('ram', $event)">
              <option value="all"></option>
              <option v-for="value in filterValues.ram" :key="value">{{ value }}</option>
            </select>
          </th>
          <th>
            <select @change="filterByValue('hdd', $event)">
              <option value="all"></option>
              <option v-for="value in filterValues.hdd" :key="value">{{ value }}</option>
            </select>
          </th>
          <th>
            <select @change="filterByValue('location', $event)">
              <option value="all"></option>
              <option v-for="value in filterValues.location" :key="value">{{ value }}</option>
            </select>
          </th>
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
        <td colspan="5">Showing {{ servers.length }} of {{ meta.totalItems || 0 }} servers</td>
      </tfoot>
    </table>
    <div v-else>
      Unable to fetch server list<br/>
      Make sure you are running the backend at <a :href="apiUrl">{{apiUrl}}</a>
    </div>
  </div>
</template>
