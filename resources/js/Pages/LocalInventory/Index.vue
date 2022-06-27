<template>
    <div><layout></layout></div>

    <portal-target name="destination"> </portal-target>
    <div v-if="firstBookPosition" class="m-3 md:m-10">
        <div class="w-11/12 mb-2 text-red-700 md:flex">
	<div class="mr-2 md:mr-10">
		Missing Item Count: <span class="text-indigo-900">{{ missingItems.length}}</span>
	</div>
	<div class="mr-2 md:mr-10">
		Location:  <span class="text-indigo-900">{{ initialLocationName[0].effective_location_name }}</span>
	</div>
            <div>
                Possible Mis-Shelved Items: <span class="text-indigo-800">{{ unexpectedItems.length }}</span>
                
            </div>

        </div>
        <div class="mb-2 text-xl font-bold md:text-2xl">
            Missing items expected in this call number range <span class="md:hidden"><br></span>({{
                firstBookCallNumber
            }}
            to {{ lastBookCallNumber }}):
        </div>
	<div class="hidden sm: md:block">
	<table class="w-11/12">
            <tr class="font-bold">
                <td></td>
                <td>Title</td>
                <td>Call Number</td>
                <td>Barcode</td>
                <td>Shelving Order</td>
                <td>Status</td>
                <td>LocationID</td>
            </tr>
            <tr v-for="(missingItem, index) in missingItems">
                <td>{{ index + 1 }}.</td>
                <td>{{ missingItem.title.slice(0, 50) }}</td>
                <td>{{ missingItem.call_number }}</td>
                <td>{{ missingItem.barcode }}</td>
                <td>{{ missingItem.effective_shelving_order }}</td>
                <td>{{ missingItem.status }}</td>
                <td>{{ missingItem.effective_location_id }}</td>
            </tr>
        </table>	
	</div>
        
<div class="md:hidden">
	<table class="w-11/12">
            <tr v-for="(missingItem, index) in missingItems">
               <tr> <td>{{ index + 1 }}.</td></tr>
               <tr> <td>{{ missingItem.title.slice(0, 50) }}</td></tr>
               <tr> <td>{{ missingItem.call_number }}</td></tr>
               <tr> <td>{{ missingItem.barcode }}</td></tr>
               <tr> <td>{{ missingItem.effective_shelving_order }}</td></tr>
               <tr> <td>{{ missingItem.status }}</td></tr>
               <tr> <td>{{ missingItem.effective_location_id }}</td></tr>
            </tr>
        </table>	
	</div>
        <br />
        <div class="text-2xl font-bold">
            Scanned Items not expected in this range:
        </div>

        <div>
            <table class="w-11/12">
                <tr class="font-bold">
                    <td></td>
                    <td>Title</td>
                    <td>Call Number</td>
                    <td>Barcode</td>
                    <td>Location Name</td>
                    <td>Status</td>
                    <td>LocationID</td>
                </tr>
                <tr v-for="(item, index) in unexpectedItems">
                    <td>{{ index + 1 }}.</td>
                    <td>{{ item.title.slice(0, 50) }}</td>
                    <td>{{ item.callnumber }}</td>
                    <td>{{ item.barcode }}</td>
                    <td>{{ item.effective_location_name }}</td>
                    <td>{{ item.status }}</td>
                    <td>{{ item.effective_location_id }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div align="center" v-else class="">
        <span style="color: red; margin-top: 50px; font-size: 2em;"
            >You have not scanned any books.</span
        >
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
export default {
    components: {
        Layout,
    },
    props: {
        missingItems: Object,
        firstBookPosition: Object,
        lastBookPosition: Object,
        firstBookCallNumber: Object,
        lastBookCallNumber: Object,
        itemsWithIncorrectStatus: Object,
        uniqueLocationIds: Object,
        uniqueLocationNames: Object,
        uniqueStatuses: Object,
        initialLocationName: Object,
        unexpectedItems: Object,
    },
    data() {
        return { idOpen: false, nameOpen: false, statusOpen: false };
    },
    methods: {
        showIds() {
            idVisible: false;
        },
    },
};
</script>
