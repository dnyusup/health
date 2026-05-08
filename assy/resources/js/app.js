import './bootstrap';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';
import { Html5Qrcode } from 'html5-qrcode';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Make TomSelect available globally
window.TomSelect = TomSelect;

// Make Html5Qrcode available globally for blade views
window.Html5Qrcode = Html5Qrcode;

// Import FontAwesome for icons (so <i class="fas ..."> works)
import '@fortawesome/fontawesome-free/js/all.js';
