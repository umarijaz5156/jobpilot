<template>
    <div>
        <div class="mt-5 application-wrapper-bottom position-relative">
            <img :src="loaderImg" alt="" width="100px" height="100px" class="loader_position" v-if="loading">

            <div class="all-application-column column mb-3"
                 v-for="applicationGroup in applicationsGroupData"

                :key="applicationGroup.id">
                <div class="height-100vh">
                    <div class="column-title tw-px-5 tw-pt-4 d-flex justify-content-between align-items-center">
                        <h4>{{ applicationGroup.name }} ({{ applicationGroup.applications.length }})</h4>
                        <group-dropdown v-if="applicationGroup.is_deleteable" :application-group="applicationGroup"
                            @edit-group="editGroup" />
                    </div>
                    <draggable class="list-group kanban-column scrollbar-hidden tw-px-5" v-model="applicationGroup.applications"
                        v-bind="taskDragOptions" @end="handleTaskMoved" ghost-class="ghost" drag-class="dragg">
                        <div class="application-card" v-for="application in applicationGroup.applications"
                            :key="application.id">

                            <div class="appliaction-card-top" data-toggle="modal" @click.prevent="previewResume(application.candidate.user.username,application.answers)">

                                <div class="profile-img" v-if="application.candidate.user">
                                    <img width="48px" height="48px" class="tw-object-cover	"
                                        :src="application.candidate.user.image_url" alt="image">
                                </div>
                                <div class="profile-info">
                                    <a href="" class="name" v-if="application.candidate.user">
                                        {{ application.candidate.user.name }}
                                    </a>
                                    <h4 class="designation" v-if="application.candidate.profession">
                                        {{ application.candidate.profession.name }}
                                    </h4>
                                </div>
                            </div>
                            <hr>
                            <div class="application-card-bottom">
                                <ul class="lists">
                                    <li v-if="application.candidate.experience">
                                        {{ __('experience') }}: {{ application.candidate.experience.name }}
                                    </li>
                                    <li v-if="application.candidate.education">
                                        {{ __('education') }}: {{ application.candidate.education.name }}
                                    </li>
                                </ul>
                                <div class="download-cv-btn" v-if="application.candidate_resume_id">
                                    <a @click="downloadCv(application.candidate_resume_id)" href="javascript:void(0)"
                                        class="btn">
                                        <span>{{ __('download_cv') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </draggable>
                </div>
            </div>
        </div>
        <!-- Update group modal  -->
        <div v-if="showModal">
            <transition name="fade">
                <div class="modal fade show modal-edit" style="display: block;" id="editColumnModal">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{ __('update') }} {{ __('group') }}</h5>
                                <button type="button" class="btn-close" @click="showModal = false"></button>
                            </div>
                            <div class="modal-body">
                                <form @submit.prevent="updateGroup">
                                    <div class="form-group">
                                        <label for="name">{{ __('group_name') }}</label>
                                        <input v-model="name" type="text" id="name" placeholder="Name"
                                            class="form-control col-name">
                                        <span class="text-danger" v-if="errors.name">{{ errors.name[0] }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3 mb-5">
                                        <button type="button" class="btn btn-secondary" @click="showModal = false">{{
                                            __('cancel') }}</button>
                                        <button class="btn btn-primary">{{ __('update') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>

        <!-- Profile modal  -->
        <CandidateResumeModal
            v-if="showCandidateResumeModal"
            :response="candidateResumeInformation"
            :answers="answers"
            :show="showCandidateResumeModal"
            @close-modal="showCandidateResumeModal = false"
            :language="languageTranslation"
            :job="job"
        />

    </div>
</template>

<script>
    import draggable from "vuedraggable";
    import GroupDropdown from "../GroupDropdown.vue";
    import CandidateResumeModal from "./CandidateResumeModal.vue";

    export default {
        props: {
            applicationGroups: Array,
            job: Object,
        },
        components: {
            draggable,
            GroupDropdown,
            CandidateResumeModal,
        },
        data() {
            return {
                showModal: false,
                applicationsGroupData: [],
                name: "",
                groupId: "",
                errors: {},
                loading: false,
                loaderImg: "/frontend/assets/images/loader.gif",
                answers : [],
                showCandidateResumeModal: false,
                candidateResumeInformation: '',

                languageTranslation: [],
            };
        },
        methods: {
            handleTaskMoved() {
                this.loading = true;
                axios
                    .put("/company/applications/sync", {
                        applicationGroups: this.applicationsGroupData,
                    })
                    .then((response) => {
                        this.loading = false;
                    })
                    .catch((err) => {
                        this.loading = false;
                    });
            },
            editGroup(name, id) {
                this.showModal = true;
                this.name = name;
                this.groupId = id;
            },
            updateGroup() {
                axios
                    .put("/company/applications/group/update", {
                        name: this.name,
                        id: this.groupId,
                    })
                    .then((response) => {
                        if (response.data.success) {
                            window.location.reload();
                        }
                    })
                    .catch((err) => {
                        this.errors = err.response.data.errors;
                    });
            },
            downloadCv(resumeId) {
                axios({
                    method: "get",
                    url: "/candidates/download/cv/" + resumeId,
                    responseType: "blob",
                }).then(function (response) {
                    var fileURL = window.URL.createObjectURL(
                        new Blob([response.data])
                    );
                    var fileLink = document.createElement("a");
                    fileLink.href = fileURL;
                    fileLink.setAttribute("download", response.headers.filename);
                    document.body.appendChild(fileLink);
                    fileLink.click();
                });
            },
            previewResume(username,answers){

                if (!username) {
                    alert('Something went wrong while trying to preview resume! Please try again')
                }

                this.answers = answers ;

                this.username = username
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
            async fetchTranslateData() {
                let data = await axios.get('/translated/texts');
                this.languageTranslation = data.data
            },
            __(key) {
                if (this.languageTranslation) {
                    return this.languageTranslation[key] || key;
                }

                return key;
            }
        },
        computed: {
            taskDragOptions() {
                return {
                    animation: 200,
                    group: "task-list",
                    dragClass: "status-drag",
                };
            },
        },
        mounted() {
            this.applicationsGroupData = JSON.parse(
                JSON.stringify(this.applicationGroups)
            );
            // console.log(this.applicationsGroupData);

            this.fetchTranslateData();
        },
    };
</script>

<style scoped>
    .dragg {
        transform: rotate(50deg);
    }

    .ghost {
        color: transparent;
        background: none;
        border: 2px dashed rgba(0, 0, 0, 0.2);
        box-shadow: none;
        user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        cursor: grabbing;
    }

    .kanban-column {
        min-height: 88%;
        max-height: 88%;
        overflow-y: scroll;
    }

    .scrollbar-hidden::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hidden {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .status-drag {
        transition: transform 0.5s;
        transition-property: all;
    }


    .modal-edit {
        background: url("http://bin.smwcentral.net/u/11361/BlackTransparentBackground.png");
        z-index: 1100;
    }


    #editColumnModal .modal-dialog {
        max-width: 536px !important;
    }

    #editColumnModal .modal-header {
        border: transparent;
        padding-bottom: 0px !important;
        margin-bottom: 16px !important;
    }

    #editColumnModal .modal-header .modal-title {
        font-weight: 500;
        font-size: 18px;
        line-height: 28px;
        color: var(--gray-900);
    }

    #editColumnModal .modal-body {
        padding: 0px 32px !important;
    }

    .loader_position {
        position: absolute;
        top: 50%;
        left: 50%;
    }

    .height-auto {
        height: auto !important;
    }

    .height-100vh {
        height: 100vh;
    }
</style>
