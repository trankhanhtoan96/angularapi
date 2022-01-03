import {NgModule} from '@angular/core';
import {BrowserModule, Meta, Title} from '@angular/platform-browser';

import {AppComponent} from './app.component';
import {AppRoutingModule} from "./app-routing.module";
import {GlobalComponentsModule} from "./globalcomponents/GlobalComponents.module";
import {HomeModule} from "./home/Home.module";
import {EditorModule, TINYMCE_SCRIPT_SRC} from '@tinymce/tinymce-angular';


@NgModule({
    declarations: [
        AppComponent,
    ],
    imports: [
        BrowserModule.withServerTransition({appId: 'serverApp'}),
        AppRoutingModule,
        GlobalComponentsModule,
        HomeModule,
        EditorModule
    ],
    providers: [
        Title, Meta,
        {provide: TINYMCE_SCRIPT_SRC, useValue: 'tinymce/tinymce.min.js'}
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
