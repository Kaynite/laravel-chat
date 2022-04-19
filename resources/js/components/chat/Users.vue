<template>
    <div class="border border-gray-200 col-span-1 p-4 rounded-lg row-span-6">
        <p class="font-bold w-full text-center pb-2 border-b mb-4">
            Online Now
        </p>
        <ul>
            <li v-for="user in users" :key="user.id">
                {{ user.name }}
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: "Users",
    props: ["users"],
};
</script>

<style></style>
