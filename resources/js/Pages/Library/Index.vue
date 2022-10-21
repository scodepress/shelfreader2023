<template>
<layout />
    <div class="flex justify-center w-2/3 mx-auto mt-10 text-3xl">
        Library Stats
    </div>
    <div class="w-2/3 mx-auto mt-6">
        <div class="text-2xl">Error Data</div>
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
    },
};
</script>
