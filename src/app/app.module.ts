import {NgModule} from '@angular/core';
import {BrowserModule, Meta, Title} from '@angular/platform-browser';

import {AppComponent} from './app.component';
import {HomeComponent} from './home/home.component';
import {AboutComponent} from './about/about.component';
import {AppRoutingModule} from "./app-routing.module";

@NgModule({
    declarations: [
        AppComponent,
        HomeComponent,
        AboutComponent
    ],
    imports: [
        BrowserModule,
        BrowserModule.withServerTransition({appId: 'serverApp'}),
        AppRoutingModule
    ],
    providers: [
        Title, Meta
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
