import './bootstrap';

import Vue from 'vue/dist/vue.js';
import '../app.css'

import KanbanBoard from './components/Company/ApplicationKanbanBoard.vue';
import ChatBox from './components/Company/ChatBox.vue';

Vue.component("application-kanban-board", KanbanBoard);
Vue.component("chat-box", ChatBox);

// const app2 = new Vue({
//     el: '#companyChatBox'
// });
// const app = new Vue({
//     el: '#app'
// });
document.addEventListener('DOMContentLoaded', function() {
    const companyChatBox = document.getElementById('companyChatBox');
    const appElement = document.getElementById('app');

    if (companyChatBox) {
        const app2 = new Vue({
            el: '#companyChatBox'
            // Add other options as needed
        });
    }

    if (appElement) {
        const app = new Vue({
            el: '#app'
            // Add other options as needed
        });
    }
});
