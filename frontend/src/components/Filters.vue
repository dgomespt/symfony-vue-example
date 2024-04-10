<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits(['filtersChanged'])

const appliedFilters = ref({
    'storage': 'any',
    'ram': ['any'],
    'hddType': 'any',
    'location': 'any'
})

const filterValues = {
    'storage': {
        'any': 'Any',
        '250': '250GB',
        '500': '500GB',
        '1000': '1TB',
        '2000': '2TB',
        '3000': '3TB',
        '4000': '4TB',
        '8000': '8TB',
        '12000': '12TB',
        '24000': '24TB',
        '48000': '48TB',
        '72000': '72TB'
    },
    'ram' : {
        'any': 'Any',
        '2': '2GB',
        '4': '4GB',
        '8': '8GB',
        '12': '12GB',
        '16': '16GB',
        '24': '24GB',
        '32': '32GB',
        '48': '48GB',
        '64': '64GB',
        '96': '96GB'
    },
    'hddType' : {
        'any': 'Any',
        'SAS': 'SAS',
        'SATA': 'SATA',
        'SSD': 'SSD'
    },
    'location' : {
        'any': 'Any',
        'AMS': 'Amsterdam',
        'DAL': 'Dallas',
        'FRA': 'Frankfurt',
        'HKG': 'Hong Kong',
        'SFO': 'San Francisco',
        'SIN': 'Singapore',
        'WDC': 'Washington'
    }
}

function handleRamSelection(target: HTMLInputElement) {

    const checkboxValue = target.value;

    if(target.value=='any'){
        appliedFilters.value.ram = ['any'];
    }else{

        if(target.checked){

            if(checkboxValue!='any'){
                const index = appliedFilters.value.ram.indexOf('any');
                if (index !== -1) {
                    appliedFilters.value.ram.splice(index, 1);
                }
            }
            
        }

        if(appliedFilters.value.ram.length === 0){
            appliedFilters.value.ram = ['any'];
        }
        

    }

    filter();

}

function filter(){
    emit('filtersChanged', appliedFilters.value);
}

</script>

<template>
    <div class="border-2 mb-5 p-5 align-top">
        <form>
        <div>
            <label for="storage">Storage: </label>
            <select v-model="appliedFilters.storage" @change="filter">
                <option v-for="(label, value) in filterValues.storage" :key="value" :value="value">{{ label }}</option>
            </select>
        </div>
        <div>
            <label>RAM: </label>
                <label v-for="(option, index) in filterValues.ram" :key="index">
                <input type="checkbox" :value="index" v-model="appliedFilters.ram" @change="handleRamSelection($event.target as HTMLInputElement)"/> {{ option }}
            </label>
        </div>
        <div>
            <label for="hddType">HDD Type: </label>
            <select v-model="appliedFilters.hddType" @change="filter">
                <option v-for="(label, value) in filterValues.hddType" :key="value" :value="value">{{label}}</option>
            </select>
        </div>
        <div>
            <label for="location">Location: </label>
            <select v-model="appliedFilters.location" @change="filter">
                <option v-for="(label, value) in filterValues.location" :key="value" :value="value">{{label}}</option>
            </select>
        </div>
    </form>
    </div>
    
</template>