<template>
<layout></layout>
<div class="w-11/12 m-20">
    <h1 class="text-3xl text-center">Instructions</h1>
    	<div class="text-2xl">About ShelfReader</div>
    <p class="text-xl">
    	ShelfReader is a tool designed to assist in finding and correcting shelving errors in Library shelves. ShelfReader currently works with
	LCC call numbers and GPS Maps. It alerts the user to items that are out of order and 
	instructs the user in correcting the shelf. It also alerts the user when scanned items are not expected on the shelf, such as those that
	are designated "checked out" or "missing" by the system. It also saves items to an inventory list, ordered by call number, that is searchable 
	by date range or call number range. The the result can then be downloaded as an excel or csv file.
	
		</p>
    	<div class="mt-10 text-2xl">Basic Work Flow:</div>
	<p class="text-xl">
		<ol class="mt-2 ml-8 text-xl list-decimal">
			<li>The user scans the barcode of each item, working from left to right.</li>
			<li>ShelfReader pulls information about each item from the library's Web Service (API).</li>
			<li>As each item is scanned it appears on the screen, on the virtual shelf.</li>
			<li>When there are items out of position, blue text will identify the title of the item to be moved, and a vertical green line appears in the position 
			the item will be moved to. (Example 1 Below)</li>
			<li>The user indicates to the application that the correction is being made by scanning the barcode of the moving item before placing it in the 
				correct position on the shelf.</li>
					<li>If there are additional errors, a new title and destination will be indicated on the shelf.</li>
				
				<li>The user continues to make corrections until all errors are corrected.</li>
				<li>It is possible to delete the last item in the virtual shelf if it is an item that does not belong in the range of items being scanned.
					To do so, resolve all errors in the shelf and click on the title of the last item in the shelf. You will see a link in the dialog
					that appears to delete the item.
					</li>
					<li>You can see information about each item on the virtual shelf by clicking on the title. (Example 2 Below)</li>
		</ol>
	
	</p>
		<div class="mt-4 text-2xl">
			Example 1
		</div>
	
	<p class="mt-4">
		<img src="/assets/images/shelfExample.png" />
	</p>

	<p>
		<div class="mt-4 text-2xl">
			Example 2
		</div>
		<div class="mt-4 ml-4 text-xl text-red-900">
	<div>
		Item Information
	
	</div>
	<div>
		Title: The films of Akira Kurosawa / by Donald Richie.

	</div>
	<div>
	Barcode: 33048001495167
	</div>

		<div>
		Call Number: PN1998.A3K795
	</div>	
	<div>
		
	Effective Shelving Order: PN 41998 A3 K795 0
	</div>
		<div>
			
	Shelving Location: 2nd floor
		</div>	
		</div>
    	<div class="mt-10 text-2xl">Identification of Inventory Errors</div>
		<p class="mt-4 text-xl">
			Along with the title and call number, ShelfReader also accesses information about the status of each item scanned. 
			As a result it is able to alert the user to items that require attention - for example items that are designated "Checked Out"  
			or "Missing" by the sytem. These items will appear as alerts when the user clicks on the "View Alerts" link.

		</p>
	
    	<div class="mt-10 text-2xl">The Inventory List</div>
		<div class="text-xl">
			The Inventory list is a list of all the items scanned by users at a given library. Items are added to the Inventory List at the end of 
			a shelf reading session, when a user clicks
			on the "Clear Shelf" link on the main page of the application. By default the list is displayed sorted by call number, however the list 	
			is searchable by date range or call number range using the form at the top of the page. When a search is completed, a link appears enabling the user  
			to download the result as a call number sorted list, in excel or csv format.

		</div> 
	</p>


</div>
</template>

<script>
import Layout from "@/Layouts/Layout";
export default {
components: { 
		    Layout,
	    },
    props: {
        title: String,
    },
};
</script>
