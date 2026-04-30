import axios from 'axios';

/**
 * Qui configuriamo Axios, una libreria che ci permette di fare richieste al server.
 * Impostiamo un'intestazione (header) per far capire al server che le nostre
 * richieste sono di tipo "XMLHttpRequest" (AJAX).
 */
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
