import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
    faCamera, 
    faTachometerAlt, 
    faUser, 
    faComments,
    faChartLine,
    faFolder,
    faShoppingCart,
    faHeart,
    faCog,
    faSignOutAlt,
    faTimes,
    faChevronRight
} from '@fortawesome/free-solid-svg-icons'

library.add(
    faCamera, 
    faTachometerAlt, 
    faUser, 
    faComments,
    faChartLine,
    faFolder,
    faShoppingCart,
    faHeart,
    faCog,
    faSignOutAlt,
    faTimes,
    faChevronRight
)

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.vueApp.component('Fa', FontAwesomeIcon)
})