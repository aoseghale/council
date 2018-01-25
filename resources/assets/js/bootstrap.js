
window._ = require('lodash');

import InstantSearch from 'vue-instantsearch';

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

window.Vue = require('vue');

Vue.use(InstantSearch);

// window.Vue.prototype.authorize = function(handler) {
//     //  Additional admin privilege if wanted
//     let user = window.App.user;
//
//     return user ? handler(user) : false;
// };

let authorizations = require('./authorizations');

window.Vue.prototype.authorize = function(...params) {
    if (! window.App.signedIn) return false;

    if (typeof params[0] === 'string') {
        return authorizations[params[0]](params[1]);
    }

    return params[0](window.App.user);
};

Vue.prototype.signedIn = window.App.signedIn;

/**
 * We'll load highlight.js library which allows us to easily enable syntax
 * highlighting within <pre><code> blocks. It also allows highlighting
 * within custom html blocks with a wide variety of color schemes.
 */

let Highlighter = require('highlight.js');
require('highlight.js/styles/foundation.css'); // load Foundation style

Vue.prototype.highlight = function (block) {
    if (! block) return;

    block.querySelectorAll('pre').forEach(
        node => Highlighter.highlightBlock(node)
    );
};

window.events = new Vue();

window.flash = function (message, level = 'success') {
    window.events.$emit('flash', { message, level });
};
