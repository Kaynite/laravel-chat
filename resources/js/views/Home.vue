<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div
                    class="p-6 bg-white border-b border-gray-200 grid grid-cols-4 gap-4 grid-rows-6 chat-grid"
                >
                    <div
                        class="border border-gray-200 col-span-3 rounded-lg row-span-5 overflow-hidden"
                    >
                        <p
                            class="text-center py-2 bg-blue-500 text-white"
                            v-show="isLoading"
                        >
                            Loading...
                        </p>
                        <ul
                            class="m-4 overflow-y-auto h-96 pr-2"
                            ref="messages"
                            @scroll="loadMoreMessages"
                        >
                            <li
                                class="flex"
                                v-for="(message, index) in messages"
                                :key="index"
                            >
                                <div
                                    class="mb-3 flex flex-col items-start"
                                    :class="{
                                        'ml-auto':
                                            currentUser.id == message.userId,
                                    }"
                                >
                                    <small
                                        v-if="currentUser.id != message.userId"
                                        class="block mx-2 text-gray-500 mb-1"
                                        >{{ message.name }}</small
                                    >
                                    <span
                                        :class="{
                                            'bg-gray-200':
                                                currentUser.id !==
                                                message.userId,
                                            'bg-blue-500 text-white':
                                                currentUser.id ==
                                                message.userId,
                                        }"
                                        class="px-4 py-1 rounded-full"
                                        >{{ message.message }}</span
                                    >
                                </div>
                            </li>
                        </ul>
                    </div>

                    <chat-users :users="users" />
                    <chat-input />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import ChatUsers from "@/components/chat/Users.vue";
import ChatInput from "@/components/chat/Input.vue";
export default {
    name: "Home",
    components: {
        ChatUsers,
        ChatInput,
    },
    data() {
        return {
            messages: [],
            users: [],
            message: "",
            currentPage: 1,
            isLoading: false,
            hasMoreMessages: true,
        };
    },
    methods: {
        async loadMoreMessages(event) {
            const target = event.target;
            if (target.scrollTop == 0 && this.hasMoreMessages) {
                this.isLoading = true;
                this.currentPage += 1;
                const currentHeight = target.scrollHeight;
                await axios
                    .get(`/api/messages?page=${this.currentPage}`)
                    .then((res) => {
                        for (let message of res.data.data.reverse()) {
                            this.messages.unshift(message);
                        }
                        if (res.data.last_page == this.currentPage) {
                            this.hasMoreMessages = false;
                        }
                    });
                const newHeight = target.scrollHeight;
                target.scrollTop = newHeight - currentHeight;
                this.isLoading = false;
            }
        },
        handleTyping(e) {
            this.message = e.tagret.value;
        },
    },
    computed: {
        currentUser() {
            return this.$store.getters.user;
        },
    },
    mounted() {
        Echo.join("chat")
            .here((users) => {
                this.users = users;
            })
            .joining((user) => {
                this.users.push(user);
            })
            .leaving((user) => {
                this.users = this.users.filter((cUser) => {
                    return cUser.id !== user.id;
                });
            })
            .listen("MessageSent", (e) => {
                this.messages.push(e);
                this.$nextTick(() => {
                    const div = this.$refs.messages;
                    div.scrollTop = div.scrollHeight - div.clientHeight;
                });
            });

        // Get Latest 20 Messages
        axios.get("/api/messages/").then((res) => {
            for (let message of res.data.data.reverse()) {
                this.messages.push(message);
            }
            this.$nextTick(() => {
                const div = this.$refs.messages;
                console.log(div);
                div.scrollTop = div.scrollHeight - div.clientHeight;
            });
        });
    },
    beforeRouteLeave(to, from, next) {
        Echo.leave("chat");
        next();
    },
};
</script>

<style scoped>
::-webkit-scrollbar {
    width: 5px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: rgb(193, 193, 193);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgb(157, 156, 156);
}
</style>
