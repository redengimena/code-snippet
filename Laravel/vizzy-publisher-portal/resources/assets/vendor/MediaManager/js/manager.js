import Vue from 'vue'
/*                Libs                */
import VueClipboard from 'vue-clipboard2'
import Vue2Filters  from 'vue2-filters'

Vue.use(Vue2Filters)
Vue.use(VueClipboard)
Vue.use(require('vue-ls'))
window.EventHub = require('vuemit')
window.keycode  = require('keycode')
window.Fuse     = require('fuse.js')

// vue-tippy
Vue.use(require('vue-tippy').default, {
    popperOptions: {
        modifiers: {
            hide: {enabled: false}
        }
    }
})

// v-touch
let VueTouch = require('vue-touch')
VueTouch.registerCustomEvent('dbltap', {type: 'tap', taps: 2})
VueTouch.registerCustomEvent('hold', {type: 'press', time: 500})
Vue.use(VueTouch)

// axios
window.axios                  = require('axios').default
axios.defaults.headers.common = {
    'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    'X-Requested-With' : 'XMLHttpRequest'
}
axios.interceptors.response.use(
    (response) => response,
    (error) => Promise.reject(error.response)
)

// Echo
// import EchoLib from 'laravel-echo'
// window.Echo = new EchoLib({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

// vue-awesome
require('./modules/icons')
Vue.component('Icon', require('vue-awesome/components/Icon').default)
Vue.component('IconTypes', require('./components/utils/icon-types.vue').default)

/*                Components                */
Vue.component('MediaManager', require('./components/manager.vue').default)
Vue.component('MyNotification', require('vue-notif').default)
Vue.component('MyDropdown', require('./components/utils/dropdown.vue').default)
Vue.component('VizzyImageField', require('./components/vizzy-image-field.vue').default)

/*                Events                */
if ('connection' in navigator) {
    if (!navigator.connection.saveData) {
        require('./modules/events')
    }
}
