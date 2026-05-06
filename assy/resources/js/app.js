import './bootstrap';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
import Alpine from 'alpinejs';
import TomSelect from 'tom-select';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Make TomSelect available globally
window.TomSelect = TomSelect;

// Import FontAwesome for icons (so <i class="fas ..."> works)
import '@fortawesome/fontawesome-free/js/all.js';
