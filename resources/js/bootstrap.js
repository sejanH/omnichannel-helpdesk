import axios from 'axios';
import $ from 'jquery';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.$ = window.jQuery = $;
window.Pusher = Pusher;

const scheme = import.meta.env.VITE_REVERB_SCHEME || (window.location.protocol === 'https:' ? 'https' : 'http');
const isSecure = scheme === 'https';
const port = parseInt(import.meta.env.VITE_REVERB_PORT || '9881');

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'bq4kweky886lkzul4zr1',
    wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname || 'localhost',
    wsPort: port,
    wssPort: port,
    forceTLS: isSecure,
    enabledTransports: isSecure ? ['wss'] : ['ws'],
});
