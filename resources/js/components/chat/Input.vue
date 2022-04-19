<template>
    <div class="col-span-3 row-span-1">
        <textarea
            v-model="message"
            @keydown.prevent.enter="sendMessage"
            placeholder="Type your message here"
            class="border border-gray-200 col-span-3 rounded w-full"
        ></textarea>
    </div>
</template>

<script>
export default {
    name: "Input",
    data() {
        return {
            message: ""
        }
    },
    methods: {
        sendMessage() {
            if (this.message != "") {
                axios
                    .post("/api/messages", { message: this.message })
                    .then(() => {
                        this.message = "";
                    });
            }
        },
    }
};
</script>

<style></style>
