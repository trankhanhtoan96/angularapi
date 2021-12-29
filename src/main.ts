import {enableProdMode} from '@angular/core';
import {platformBrowserDynamic} from '@angular/platform-browser-dynamic';

import {AppModule} from './app/app.module';
import {environment} from './environments/environment';

if (environment.production) {
    enableProdMode();
}

function bootstrap() {
    platformBrowserDynamic().bootstrapModule(AppModule)
        .catch(err => console.error(err));
};


if (document.readyState === 'complete') {
    bootstrap();
    const body = document.getElementsByTagName('body')[0];
    body.classList.add('antialiased');
} else {
    document.addEventListener('DOMContentLoaded', bootstrap);
}

