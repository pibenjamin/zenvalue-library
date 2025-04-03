import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';



$(document).ready(function() {
    $('#mountedActionsData.0.status-keep_at_home').click(function() {
        alert('keep_at_home');
    });

});

