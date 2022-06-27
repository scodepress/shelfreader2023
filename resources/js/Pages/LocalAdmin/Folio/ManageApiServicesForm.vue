<template>

<form @submit.prevent="">
<div class="max-w-md grid grid-cols-1 gap-6">
	<label class="block">
	<span class="text-gray-700">Select a Service to Add</span>
	<select
		class="block w-full mt-1 form-multiselect"
		v-model="apiService"
	>
		<option v-for="service in availableServices" key="index" :value="service.id">{{service.service_name}}</option>
	</select>
	</label>
	</div>
		<br>
                    <button class="justify-center max-w-md px-4 py-2 text-sm font-medium text-white bg-indigo-600 border nline-flex ui button toggle rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Add</button>
</form>
</template>

<script>
export default {
    props: ['availableServices'],
};
</script>
