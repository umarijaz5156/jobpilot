<template>
    <div>
        <div class="tw-flex tw-gap-2.5 tw-items-center tw-mt-6 tw-mb-4">
            <a href="/user/dashboard">
                <LeftArrowIcon />
            </a>
            <h3 class="tw-mb-0 tw-text-2xl tw-text-[#18191C] tw-font-medium">
                {{ __('messenger') }}
                <span v-if="total_unread_count && total_unread_count > 0">({{ total_unread_count }} {{ __('unread')
                    }})</span>
            </h3>
        </div>
        <div class="row g-3 tw-mb-16">
            <div class="col-lg-4">
                <div class="tw-py-5 tw-rounded-lg chat-box-card">
                    <div class="tw-px-4 tw-pb-4">
                        <div class="tw-flex tw-mb-3 tw-justify-between tw-items-center">
                            <h3 class="tw-mb-0 tw-text-lg tw-text-[#161719] tw-font-medium">{{ __('filter_by') }} {{
                                __('job') }}</h3>
                            <div class="custom-checkbox">
                                <input @change="showUnreadList($event)" ref="unreadCheckbox" type="checkbox" id="unread"
                                    hidden>
                                <label for="unread" class="tw-text-[#474C54] tw-text-base tw-cursor-pointer">{{
                                    __('unread') }}</label>
                            </div>
                        </div>
                        <div class="tw-relative">
                            <select @change="filterData()" v-model="filter.job"
                                class="rt-selectactive tw-pl-10 tw-rounded-md select-job" name="" required>
                                <option value="" selected>{{ __('all_job') }}</option>
                                <option :value="job.id" v-for="job in jobs" :key="job.id">{{ job.title }}</option>
                            </select>
                            <span
                                class="tw-absolute tw-inline-flex tw-justify-center tw-items-center tw-z-[998] tw-left-3 tw-top-1/2 -tw-translate-y-1/2">
                                <BriefcaseIcon />
                            </span>
                        </div>
                    </div>
                    <ul class="tw-p-0 tw-m-0 tw-list-none scrollbar-hide tw-overflow-auto tw-h-[856px]">
                        <li v-for="user in users_list" :key="user.id">
                            <div v-if="auth.role == 'company'"
                                class="tw-flex tw-gap-3 tw-px-4 tw-py-3 tw-items-center tw-cursor-pointer   "
                                :class="!user.last_message_from_me && user.unread_count != 0 ? 'tw-bg-[#FAEBEB]':'tw-bg-white'"
                                @click="getMessages(user)">
                                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-object-cover"
                                    :src="user?.candidate?.photo" alt="user image">
                                <div class="tw-flex-grow">
                                    <h3 class="tw-mb-0.5 tw-text-md tw-text-base tw-font-medium tw-text-[#18191C]">
                                        {{ user?.candidate?.user?.name ?? 'No User' }}
                                    </h3>
                                    <p class="tw-mb-0 tw-text-sm tw-text-[#18191C]">
                                        {{ __('job') }}: {{ user?.job?.title ?? 'No Job' }}
                                    </p>
                                    <p class="tw-mb-0 tw-text-xs tw-text-[#474C54] tw-font-medium">
                                        {{ user?.latest_message ?? '' }}
                                    </p>
                                </div>
                                <div class="tw-flex tw-w-[48px] tw-flex-col tw-gap-1.5 tw-items-end tw-justify-end">
                                    <span class="tw-h-[20px]"
                                        :class="!user.last_message_from_me && user.unread_count != 0 ? 'tw-w-[22px] tw-text-white tw-inline-flex tw-justify-center tw-items-center tw-rounded-full tw-text-sm tw-bg-[#C73333]':''">
                                        {{ user.unread_count ? user.unread_count:'' }}
                                    </span>
                                    <span class="tw-text-sm tw-text-[#9199A3]">{{ user.latest_message_humans_time
                                        }}</span>
                                </div>
                            </div>
                            <div v-else class="tw-flex tw-gap-3 tw-px-4 tw-py-3 tw-items-center tw-cursor-pointer   "
                                :class="!user.last_message_from_me && user.unread_count != 0 ? 'tw-bg-[#FAEBEB]':'tw-bg-white'"
                                @click="getMessages(user)">
                                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-object-cover"
                                    :src="user?.company?.logo_url" alt="user image">
                                <div class="tw-flex-grow">
                                    <h3 class="tw-mb-0.5 tw-text-md tw-text-base tw-font-semi-medium tw-text-[#18191C]">
                                        {{ user?.company?.user?.name ?? 'No User' }}
                                    </h3>
                                    <p class="tw-mb-0 tw-text-sm tw-text-[#18191C]">
                                        {{ __('job') }}: {{ user?.job?.title ?? 'No Job' }}
                                    </p>
                                    <p class="tw-mb-0 tw-text-xs tw-text-[#474C54] tw-font-medium">
                                        {{ user?.latest_message ?? '' }}
                                    </p>
                                </div>
                                <div class="tw-flex tw-w-[48px] tw-flex-col tw-gap-1.5 tw-items-end tw-justify-end">
                                    <span class="tw-h-[20px]"
                                        :class="!user.last_message_from_me && user.unread_count != 0 ? 'tw-w-[22px] tw-text-white tw-inline-flex tw-justify-center tw-items-center tw-rounded-full tw-text-sm tw-bg-[#C73333]':''">
                                        {{ user.unread_count ? user.unread_count:'' }}
                                    </span>
                                    <span class="tw-text-sm tw-text-[#9199A3]">{{ user.latest_message_humans_time
                                        }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="chat-box-card chat-box-card_detail tw-rounded-xl tw-relative" id="chat-box-card">
                    <div class="tw-p-6 chat-box__detail-top" v-if="selectedUser">
                        <div v-if="selectedUser">
                            <a :href="'/job/'+selectedUser?.job?.slug" target="_blank"
                                class="tw-text-[#18191C] tw-font-medium tw-underline">
                                {{ selectedUser?.job?.title ?? 'No Title' }}
                            </a>
                        </div>
                        <div class="tw-h-[1px] tw-my-3 tw-bg-[#E4E5E8]"></div>
                        <div>
                            <div v-if="auth.role == 'company'" class="tw-flex tw-gap-3 tw-items-center">
                                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-object-cover"
                                    :src="selectedUser?.candidate?.photo" alt="">
                                <div class="tw-flex-grow">
                                    <h3 class="tw-mb-0.5 tw-text-base tw-font-medium tw-text-[#18191C]">
                                        {{ selectedUser?.candidate?.user?.name ?? 'No User' }}
                                    </h3>
                                    <div class="tw-flex tw-gap-3 tw-items-center">
                                        <a @click="previewResume(selectedUser?.candidate?.user?.username)" href="#"
                                            class="tw-text-sm tw-text-[#0A65CC] tw-font-medium">{{ __('view_profile')
                                            }}</a>
                                        <p class="tw-mb-0 tw-flex tw-items-center tw-text-sm">
                                            <span class="tw-text-[#767F8C]">{{ __('stage') }}: </span>
                                            <span class="tw-font-medium tw-text-[#18191C]">&nbsp;{{
                                                selectedUser.application_status }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div v-if="auth.role == 'candidate'" class="tw-flex tw-gap-3 tw-items-center">
                                <img class="tw-w-10 tw-h-10 tw-rounded-full tw-object-cover"
                                    :src="selectedUser?.company?.logo_url" alt="">
                                <div class="tw-flex-grow">
                                    <h3 class="tw-mb-0.5 tw-text-base tw-font-medium tw-text-[#18191C]">
                                        {{ selectedUser?.company?.user?.name ?? 'No User' }}
                                    </h3>
                                    <div class="tw-flex tw-gap-3 tw-items-center">
                                        <a target="_blank" :href="'/employer/'+selectedUser?.company?.user?.username"
                                            class="tw-text-sm tw-text-[#0A65CC] tw-font-medium">{{ __('view_profile')
                                            }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tw-p-6 tw-overflow-auto tw-h-[696px]" ref="chatbox">
                        <div v-if="selectedUser" class="tw-h-full">
                            <div>
                                <div v-for="message in messages" :key="message.id">
                                    <div v-if="auth.id == message.from" class="sent-message tw-flex tw-justify-end">
                                        <div class="tw-mb-4 md:tw-max-w-[70%]">
                                            <p
                                                class="tw-text-block tw-whitespace-pre-wrap tw-bg-primary-500 tw-text-white tw-rounded-br-none tw-text-base tw-py-2 tw-px-3 tw-mb-1 tw-rounded-md tw-font-medium ">
                                                {{ message.body }}
                                            </p>
                                            <p class="tw-text-xs tw-text-gray-500 tw-text-right">
                                                {{ message.created_time }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="received-message tw-flex tw-justify-start">
                                            <div class="tw-mb-4 md:tw-max-w-[70%]">
                                                <p
                                                    class="tw-text-block tw-whitespace-pre-wrap tw-text-base tw-py-2 tw-px-3 tw-mb-1 tw-rounded-md tw-font-medium tw-text-gray-900 tw-bg-[#E7F0FA] tw-rounded-bl-none">
                                                    {{ message.body }}
                                                </p>
                                                <p class="tw-text-xs tw-text-gray-500 tw-text-left">
                                                    {{ message.created_time }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div v-for="message in all_messages" :key="message.id">
                                    <div v-if="auth.id == message.from" class="sent-message tw-flex tw-justify-end">
                                        <div class="tw-mb-4 md:tw-max-w-[70%]">
                                            <p class="tw-text-block tw-text-base tw-py-2 tw-px-3 tw-mb-1 tw-rounded-md tw-font-medium ">
                                                {{ message.body }}
                                            </p>
                                            <p class="tw-text-xs tw-text-gray-500 tw-text-right">
                                                {{ message.created_time }}
                                            </p>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <div class="received-message tw-flex tw-justify-start">
                                            <div class="tw-mb-4 md:tw-max-w-[70%]">
                                                <p class="tw-text-block tw-text-base tw-py-2 tw-px-3 tw-mb-1 tw-rounded-md tw-font-medium tw-text-gray-900 tw-bg-[#E7F0FA] tw-rounded-bl-none">
                                                    {{ message.body }}
                                                </p>
                                                <p class="tw-text-xs tw-text-gray-500 tw-text-left">
                                                    {{ message.created_time }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                        <div v-else class="tw-flex tw-justify-center tw-items-center tw-w-full tw-h-full">
                            <h2 class="tw-text-xl">{{ __('you_dont_have_select_any_message_till_now') }}</h2>
                        </div>
                    </div>
                    <div class="tw-p-4 tw-bg-[#F1F2F4] chate-box__detail-bottom" id="chat-form" v-if="selectedUser">
                        <form @submit.prevent="sendMessage" class="message-form tw-flex tw-gap-3 tw-items-center">
                            <div class="tw-flex-grow tw-relative">
                                <textarea placeholder="Write your message"
                                    class="tw-pl-[52px] hover:tw-border-[#0A65CC] focus:tw-bg-[#E7F0FA] hover:tw-bg-[#E7F0FA]"
                                    v-model="message" @keydown.shift.enter="addNewLine"></textarea>
                                <span
                                    class="tw-inline-flex tw-justify-center tw-items-center tw-absolute tw-left-4 tw-top-5">
                                    <ChatIcon />
                                </span>
                            </div>
                            <div>
                                <button :disabled="loading || !message.trim()"
                                    :class="writingStart ? '!tw-bg-[#0A65CC]' : '!tw-cursor-not-allowed'" type="submit"
                                    class="btn hover:tw-text-white tw-border-none tw-px-6 tw-py-3 tw-bg-[#474C54] tw-inline-flex tw-gap-3 tw-items-center tw-text-white">
                                    <span>
                                        {{ __('send') }}
                                        <loading-icon v-if="loading" />
                                        <send-icon v-else />
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile modal  -->
        <CandidateResumeModal v-if="showCandidateResumeModal" :response="candidateResumeInformation" :answers="answers"
            :show="showCandidateResumeModal" @close-modal="showCandidateResumeModal = false"
            :language="languageTranslation" :job="job" :messagebutton="false" />
    </div>
</template>

<script>
    import SendIcon from '../SvgIcon/SendIcon.vue';
    import LoadingIcon from '../SvgIcon/LoadingIcon.vue';
    import ChatIcon from '../SvgIcon/ChatIcon.vue';
    import BriefcaseIcon from '../SvgIcon/BriefcaseIcon.vue';
    import LeftArrowIcon from '../SvgIcon/LeftArrowIcon.vue';
    import CandidateResumeModal from "./CandidateResumeModal.vue";

    export default {
        components: {
            SendIcon,
            LoadingIcon,
            ChatIcon,
            BriefcaseIcon,
            LeftArrowIcon,
            CandidateResumeModal
        },
        props: {
            users: Object,
            auth: Object,
            jobs: Array,
        },
        data() {
            return {
                users_list: this.users,
                isMessageClick: false,
                loading: false,
                messages: [],
                selectedUser: '',
                message: '',
                languageTranslation: [],
                total_unread_count: 0,
                showCandidateResumeModal: false,
                candidateResumeInformation: '',
                answers: [],

                filter: {
                    job: '',
                    role: this.auth.role,
                },

                writingStart: false,
            }
        },
        watch: {
            message(newValue, oldValue) {
                if (this.message.length > 0) {
                    this.writingStart = true
                } else {
                    this.writingStart = false
                }
            },
        },
        methods: {
            previewResume(username) {
                if (!username) {
                    alert('Something went wrong while trying to preview resume! Please try again')
                }

                axios.get("/candidate/application/profile/details", {
                    params: {
                        username: username,
                    }
                }).then((response) => {
                    this.showCandidateResumeModal = true;
                    this.candidateResumeInformation = response.data
                }).catch((err) => {
                    this.errors = err.response.data.errors;
                });
            },
            scrollToBottom() {
                this.$nextTick(function () {
                    var container = this.$refs.chatbox;
                    container.scrollTop = container.scrollHeight;
                });
            },
            playAudio() {
                const sound = new Audio('/frontend/assets/sound.mp3')
                sound.play()
            },
            async fetchTranslateData() {
                let data = await axios.get('/translated/texts');
                this.languageTranslation = data.data
            },
            __(key) {
                if (this.languageTranslation) {
                    return this.languageTranslation[key] || key;
                }

                return key;
            },
            scrollToBottom() {
                this.$nextTick(function () {
                    var container = this.$refs.chatbox;
                    container.scrollTop = container.scrollHeight + 120;
                });
            },
            async filterData() {
                let response = await axios.get('/get/users', { params: this.filter })
                this.users_list = response.data
            },
            async getMessages(user) {
                this.isMessageClick = !this.isMessageClick
                if (this.auth.role == 'company') {
                    var username = user?.candidate?.user?.username || null
                } else {
                    var username = user?.company?.user?.username || null
                }

                this.selectedUser = user
                const section = document.getElementById("chat-box-card");
                console.log(section)
                if (section) {
                    window.scroll({
                        behavior: 'smooth',
                        left: 0,
                        top: section.offsetTop
                    });
                }

                if (username) {
                    let response = await axios.get('/get/messages/' + username)
                    this.messages = response.data
                    user.unread_count = null
                    user.last_message_from_me = true

                    this.loadUnreadMessage();

                    if (this.total_unread_count == 0) {
                        document.querySelector('.unread-message-part').className = 'circle d-none unread-message-part'
                    }
                } else {
                    alert('User not found');
                }
            },
            async sendMessage(e) {
                if (!this.message.length || this.loading) { return; }
                this.loading = true
                if (this.auth.role == 'company') {
                    var to_id = this.selectedUser?.candidate?.user?.id || null
                } else {
                    var to_id = this.selectedUser?.company?.user?.id || null
                }

                if (!to_id) { alert('User not found'); return; }

                try {
                    let response = await axios.post('/send/message', {
                        message: this.message,
                        to: to_id,
                        chat_id: this.selectedUser.id,
                    })

                    this.messages.push(response.data)
                    this.message = ''
                    this.scrollToBottom();
                    this.loading = false
                    this.syncMessageUserList();
                } catch (error) {
                    alert('Something went wrong');
                }
            },
            addNewLine(event) {
                if (event.shiftKey && event.key === 'Enter') {
                    event.preventDefault();
                    this.message += '\n';
                }
            },
            backPreviousPage() {
                this.isMessageClick = !this.isMessageClick
                this.messages = []
                this.selectedUser = ''
            },
            async syncMessageUserList() {
                let response = await axios.get('/sync/user-list')
                this.users_list = response.data
            },
            async loadUnreadMessage() {
                let response = await axios.get('/load-unread-count')
                this.total_unread_count = response.data
            },
            showUnreadList() {
                if (this.$refs.unreadCheckbox.checked == true) {
                    var unread_lists = this.users.filter(function (user) {
                        return !user.last_message_from_me && user.unread_count != 0;
                    });

                    if (unread_lists && unread_lists.length != 0) {
                        this.users_list = unread_lists
                    }
                } else {
                    this.syncMessageUserList();
                }
            }
        },
        updated() {
            this.scrollToBottom();
        },
        mounted() {
            this.fetchTranslateData();

            Echo.private('chat')
                .listen('ChatMessage', (e) => {
                    if (e.chatMessage.to == this.auth.id) {
                        this.playAudio();
                        this.messages.push(e.chatMessage);
                        this.syncMessageUserList();
                        this.loadUnreadMessage();
                    }
                });
        },
    }
</script>
