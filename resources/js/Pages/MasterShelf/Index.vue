<template>
<div>
<layout></layout>
</div>

<h1 class="ml-6 text-3xl font-bold">Inventory List</h1>
<div style="width: 100%; height: 100%;">
        <header class="ml-2 mr-2 rounded-lg">
            <div class="items-center justify-start px-4 py-3 ml-6 mr-6 md:flex">
                
                <div class="text-xl">
                	<div class="mt-2 mb-2 text-2xl font-semibold">
                		Set Search Parameters                	</div>
                    <form class="flex" @submit.prevent="inventorySearch">
                        <div class="mr-6">
			   <input
                                class="block pl-1 border-2 w-80 form-input rounded-md shadow-sm"
				v-model="form.beginningBarcode"
				placeholder="Beginning Barcode"
                            />
                        </div>
                        <div>
			   <input
                                class="block pl-2 ml-4 border-2 w-80 form-input rounded-md shadow-sm"
				v-model="form.endingBarcode"
				placeholder="Ending Barcode"
                            />
                        </div>
                        <div class="ml-4 mr-4">
			   <label for=""></label>
				<select v-model="form.beginningDate">
					<option value="" disabled>Beginning Date</option>
					<option v-for="(date, index) in allDates">
						{{date}}
					</option>
				</select>
                        </div>
                        <div class="ml-4 mr-4">
			   <label for=""></label>
				<select v-model="form.endingDate">
					<option value="" disabled>Ending Date</option>
					<option v-for="(date, index) in allDates">
						{{date}}
					</option>
				</select>
                        </div>
			<div>
				
				<button class="block w-40 pl-1 ml-2 border-2 form-input rounded-md shadow-sm" type="submit">Search</button>
			</div>
                    </form>
			<div>
			
			</div>
                </div>
		
            </div>
        </header>

    </div>
<div class="ml-6">

<div v-if="beginningDate || beginningCallNumber" class="flex">

<div v-if="beginningDate" class="ml-6 font-semibold">
	Date Range: &nbsp;{{beginningDate}} &nbsp; to &nbsp; {{endingDate}}.
</div>

<div v-if="beginningCallNumber" class="ml-4 font-semibold">
	Call Number Range:&nbsp; {{beginningCallNumber}} &nbsp; to &nbsp;{{endingCallNumber}}.
</div>
	<div class="ml-4 mr-2 font-semibold">Download Results:</div> 
			<div v-if="beginningCallNumber || beginningDate" class="ml-4 mr-2"> 
				<a :href="'/export/' + 'excel'">

			<span class="text-blue-800">Excel file</span></a>
			</div>

			  <div v-if="beginningCallNumber || beginningDate" class="ml-4 mr-2">
			  <a :href="'/export/' + 'csv'">

			  <span class="text-blue-800">CSV file</span></a>
			
			  </div>

			 
			  <div class="ml-4">
			  <a :href="'/clear.search'"><span class="text-red-800">Clear Search</span></a>
			  </div>
</div>
<div v-else class="flex">
<div class="ml-6 mr-2 font-semibold">Download Results:</div> 
 <div v-if="!beginningCallNumber && !beginningDate" class="ml-4 mr-2">
			   	<a :href="'/export.master/' + 'excel' + '/' + sortSchemeId">
				<span class="text-blue-800">Excel file</span></a>

			  </div>

			  <div v-if="!beginningCallNumber && !beginningDate" class="ml-4 mr-2">
			   	<a :href="'/export.master/' + 'csv' + '/' + sortSchemeId">
				<span class="text-blue-800">CSV file</span></a>
			  </div>

</div>

<div class="ml-4 text-xl">
	Showing Items	{{ masterShelf.from }} to {{ masterShelf.to }}.
</div>
<table class="w-11/12 mt-4">
<tr class="text-xl font-semibold">
<td class="w-12"></td>
<td class="w-96">Title</td>
<td class="w-48">Barcode</td>
<td class="w-64">Call Number</td>
<td class="w-48">Date</td>
</tr>
</table>
<div>

<table class="w-11/12">
<tr v-for="(shelf, index) in masterShelf.data" class="even:bg-white odd:bg-blue-100">
	<td class="w-12">{{ index + 1 }}.</td>

<td class="w-96">{{ shelf.title.slice(0,30) }}</td>
<td class="w-48">{{ shelf.barcode }}</td>
<td class="w-64">{{ shelf.call_number }}</td>
<td class="w-48">{{ shelf.date }} </td>
</tr>
</table>
</div>
<div class="md:hidden">
	<table class="w-11/12">
            <tr v-for="(shelf, index) in masterShelf.data"
                        class="even:bg-white odd:bg-blue-100"
	    >
               <tr> <td>{{ index + 1 }}.</td></tr>
               <tr> <td>{{ shelf.title.slice(0, 50) }}</td></tr>
               <tr> <td>{{ shelf.call_number }}</td></tr>
               <tr> <td>{{ shelf.barcode }}</td></tr>
               <tr> <td>{{ shelf.status }}</td></tr>
            </tr>
        </table>	
	</div>
</div>
<div class="mt-4 ml-6">
	<template v-for="link in masterShelf.links"> 
	<Link
		v-if="link.url"
		:href="link.url"
		v-html="link.label"
		class="px-1"

			/>
				<span class="text-gray-500" v-else v-html="link.label"></span>
			
			</template>
</div>
</template>

<script>
import Layout from "@/Layouts/Layout";
import Datepicker from 'vue3-datepicker';
import { Link } from '@inertiajs/inertia-vue3';

export default {
components: { 
	    Layout,
	    Datepicker,
	    Link,
    },

props: {
masterShelf: Object,
beginningDate: String,
endingDate: String,
beginningCallNumber: Object,
endingCallNumber: Object,
allDates: Array,
sortSchemeId: Number,

},
       data() {
	       return { 
		form: this.$inertia.form({
                beginningDate: "",
                endingDate: "",
                beginningBarcode: "",
               	endingBarcode: "",
		dateFileFormat: "",
		showAlerts: "",
		locationName: "",
            }),
	    picked: new Date(),
			};

       },
	      
	      methods: {

        inventorySearch() {
            this.$inertia.post("/result.search", {
                beginningDate: this.form.beginningDate,
                endingDate: this.form.endingDate,
                beginningBarcode: this.form.beginningBarcode,
                endingBarcode: this.form.endingBarcode,
                sortSchemeId: this.sortSchemeId,
                showAlerts: this.form.showAlerts,
            });
		      },

		      },
};
</script>
