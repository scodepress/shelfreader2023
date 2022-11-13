<template>
    <layout />
    <div class="flex justify-center w-2/3 mx-auto mt-10 text-3xl">
        <span class="mr-2 font-semibold">Library Stats:</span> {{ libraryName }}
    </div>

    <div class="flex justify-center w-2/3 mx-auto mt-2 text-2xl" v-if="sortSchemeId === 1">
        Showing statistics for Library of Congress Shelfreading
    </div>

    <div class="flex justify-center w-2/3 mx-auto mt-2 text-2xl" v-else>
    	
        Showing statistics for Maps Shelfreading
    </div>

    <div class="w-2/3 mx-auto mt-8">
        <div class="text-2xl">User Activity</div>
        <div v-for="info in userInfo">
            <div class="mt-2 text-xl">
                {{ info }}
            </div>
        </div>
        <div class="mt-4 text-2xl">
            Library Totals
        </div>
        <table class="w-full">
            <tr class="text-xl">
                <td>Total Corrections</td>
                <td>Total Scans</td>
                <td>Error Percentage</td>
            </tr>

            <tr>
                <td>{{ correctionCount }}</td>
                <td>{{ totalScans }}</td>
                <td>{{ errorRate }}%</td>
            </tr>
        </table>
    </div>
</template>

<script>
import Layout from "@/Layouts/Layout";
export default {
    components: {
        Layout,
    },

    props: {
        message: String,
        errorRate: Object,
        totalScans: Object,
        correctionCount: Number,
        userInfo: Array,
        libraryName: String,
        sortSchemeId: Number,
    },
};
</script>
