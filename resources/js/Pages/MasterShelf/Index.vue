<template>
<div>
<layout></layout>
</div>
<div v-if="masterShelf">
<h1 class="mt-4 ml-12 text-3xl font-bold">{{ libraryName }} Inventory List<span class="ml-6 text-2xl font-semibold">(Sorted By Call Number)</span></h1>
<div style="width: 100%; height: 100%;">
        <header class="ml-2 mr-2 rounded-lg">
            <div class="items-center justify-start w-full px-4 py-3 ml-6 mr-6 md:flex">
                
                <div class="text-xl">
		<div class="flex">
			
                	<div class="mt-2 mb-2 text-2xl font-semibold"> Set Search Parameters
			<span  v-if="countOfSortSchemes > 1" class="ml-6 text-xl">Change Inventory View:</span>
				</div>
				<div class="w-1/3 mt-2 ml-6 text-xl" v-if="countOfSortSchemes>1">
					
                <div class="w-full" v-if="countOfSortSchemes > 1">
                    <form  class="w-full" @change="chooseSort">
                        <div class="flex w-full px-2 mb-4 text-semibold">

                            <select
                                class="block w-full form-input rounded-md shadow-sm"
                                v-model="form.sort"
                            >
                                <option value="" selected disabled>
                                    Change Inventory
                                </option>
                                <option
                                    v-for="(sort, index) in unloadedService"
                                    :key="index"
                                    :value="sort.sort_scheme_id"
                                >
                                    Inventory: {{ sort.sort_scheme_name }}
                                </option>
                            </select>
                        </div>
                    </form>
                </div>
				</div>
		</div>

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
				<select class="rounded-md" v-model="form.beginningDate">
					<option value="" disabled>Beginning Date</option>
					<option v-for="(date, index) in allDates">
						{{date}}
					</option>
				</select>
                        </div>
                        <div class="ml-4 mr-4">
			   <label for=""></label>
				<select class="rounded-lg" v-model="form.endingDate">
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

<div class="ml-6 text-xl">
	Showing Items	{{ masterShelf.from }} to {{ masterShelf.to }}. Total Items: {{masterShelf.total}}
</div>
    	<div class="flex justify-center mt-6">
		
		<div v-for="error in errors">
			<span class="text-3xl text-red-700">{{ error }}</span>
        <audio autoplay>
            <source src="/assets/beep-02.wav" type="audio/wav" />
        </audio>
		</div>
	</div>
	
	<div class="flex justify-center mt-6" v-if="$page.props.flash.message">
		<span class="text-3xl text-red-700">{{$page.props.flash.message}}</span>
        <audio autoplay>
            <source src="/assets/beep-02.wav" type="audio/wav" />
        </audio>
	</div>
<div class="flex justify-center mt-6" v-if="status != 'Available'">
        <span class="text-3xl text-red-700">{{ statusAlert }}</span>
        <audio autoplay>
            <source src="/assets/beep-02.wav" type="audio/wav" />
        </audio>
    </div>
<div v-if="masterShelf">
<table class="w-11/12">
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
</div>
<div class="mt-4 mb-6 ml-12">
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
</div>
<div v-else>
	<div class="flex justify-center mt-6" v-if="$page.props.flash.message">
		<span class="text-3xl text-red-700">{{$page.props.flash.message}}</span>
	</div>
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
errors: Object,
countOfSortSchemes: Number,
unloadedService: Object,
libraryName: Object,

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
		sort: "",
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

        chooseSort() {
            this.$inertia.post("/change.sort.master.shelf", {
                sort: this.form.sort,
            });
        },
		      },
};
</script>
