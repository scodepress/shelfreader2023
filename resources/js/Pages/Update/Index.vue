<template>
    <layout />
    <div class="m-16">
    <Link href="/update.users">Load users table.</Link>
  
    <div class="mt-4">
    	<Link href="/load.ias">Load InstitutionApiService table.</Link>
    </div>
    <div class="mt-4">
    	<Link href="/load.ias">Load InstitutionApiService table.</Link>
    </div>
    <div class="mt-4">
    	<Link href="/load.alerts">Load Alerts.</Link>
    </div>
    <div class="mt-4">
    	<Link href="/load.ems">Load EMS items.</Link>
    </div>

    <div class="mt-4">
    	<Link href="/load.engineering">Load Engineering items.</Link>
    </div>
    
    <div class="mt-4">
    	<Link href="/create.map.user">Add Maps User</Link>
    </div>
    <div class="mt-4">
    	<Link href="/load.shelves.table">Load Shelves Table</Link>
    </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/inertia-vue3";
import Layout from "@/Layouts/Layout";
export default {
components: {
	Layout,
	Link,
},
    props: {

	    },
	methods: {
			
		},
};
</script>
