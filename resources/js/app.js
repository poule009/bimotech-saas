import './bootstrap';

import Alpine from '@alpinejs/csp';
import registerComponents from './alpine-components';

window.Alpine = Alpine;

registerComponents(Alpine);

Alpine.start();
