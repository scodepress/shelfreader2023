<template>
    <div>
        <layout></layout>
        <div style="margin: 50px">
            <div class="ml-10 text-3xl font-bold">Alerts 
            	</div>
	    <div>
	    </div>
	    <div class="ml-10 text-xl">Total: {{alerts.data.length}}  
	Showing Items	{{ alerts.from }} to {{ alerts.to }} (Ordered by date).
	</div>
            <div class="flex justify-center w-full">
                <table class="w-11/12 mt-10">
                    <tr class="text-xl font-semibold">
                        <td></td>
                        <td class="ml-10">Barcode</td>
                        <td class="ml-10">Title</td>
                        <td class="ml-10">Call Number</td>
                        <td class="ml-10">Alert</td>
                        <td class="ml-10">Date</td>
                    </tr>
                    <tr
                        class="even:bg-blue-100"
                        v-for="(alert, index) in alerts.data"
                    >
                        <td>{{ index + 1 }}.</td>
                        <td class="ml-10">{{ alert.barcode }}</td>
                        <td v-if="alert.title" class="ml-10">{{ alert.title.slice(0,30) }}</td>
                        <td v-else class="ml-10">Title Unavailable</td>
                        <td class="ml-10">{{ alert.call_number }}</td>
                        <td class="ml-10">{{ alert.alert }}</td>
                        <td class="ml-10">{{ alert.created_at.slice(0,10) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="ml-24 mt-14">
            <template v-for="link in alerts.links">
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
</template>

<script>
import Layout from "@/Layouts/Layout";
import { Link } from "@inertiajs/inertia-vue3";
export default {
    components: {
        Layout,
        Link,
    },
    props: {
        alerts: Object,
    },
};
</script>
