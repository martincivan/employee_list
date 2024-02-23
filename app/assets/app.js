import './bootstrap.js';
import './styles/app.css';
import './vendor/bootstrap/dist/css/bootstrap.min.css'

import { startStimulusApp } from '@symfony/stimulus-bundle';

const app = startStimulusApp();

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
