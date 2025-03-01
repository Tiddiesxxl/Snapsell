import { library } from '@fortawesome/fontawesome-svg-core'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { 
    faCamera, 
    faTachometerAlt,
    faCloudUploadAlt,
    faImages,
    faCalendarAlt,
    faShoppingCart,
    faComments,
    faChartLine,
    faCog,
    faUser,
    faShieldAlt,
    faBell,
    faCreditCard,
    faSignOutAlt,
    faSearch,
    faEye,
    faEyeSlash,
    faEdit,
    faTrash,
    faTimes,
    faChevronRight,
    faPaperclip,
    faPaperPlane,
    faVideo,
    faPhone,
    faInfoCircle,
    faDownload,
    faDollarSign,
    faArrowUp,
    faArrowDown,
    faPlus
} from '@fortawesome/free-solid-svg-icons'

// Add all icons to the library
library.add(
    faCamera, 
    faTachometerAlt,
    faCloudUploadAlt,
    faImages,
    faCalendarAlt,
    faShoppingCart,
    faComments,
    faChartLine,
    faCog,
    faUser,
    faShieldAlt,
    faBell,
    faCreditCard,
    faSignOutAlt,
    faSearch,
    faEye,
    faEyeSlash,
    faEdit,
    faTrash,
    faTimes,
    faChevronRight,
    faPaperclip,
    faPaperPlane,
    faVideo,
    faPhone,
    faInfoCircle,
    faDownload,
    faDollarSign,
    faArrowUp,
    faArrowDown,
    faPlus
)

export default defineNuxtPlugin((nuxtApp) => {
    nuxtApp.vueApp.component('Fa', FontAwesomeIcon)
})