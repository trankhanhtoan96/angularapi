import {NgModule} from '@angular/core';
import {BrowserModule, Meta, Title} from '@angular/platform-browser';

import {AppComponent} from './app.component';
import {AppRoutingModule} from "./app-routing.module";
import {GlobalComponentsModule} from "./globalcomponents/GlobalComponents.module";
import {HomeModule} from "./home/Home.module";
import {UsersModule} from "./modules/users/users.module";
import {Backend} from "./services/Backend.service";
import {HttpClientModule} from "@angular/common/http";
import {Session} from "./services/Session.service";
import {Broadcast} from "./services/Broadcast.service";
import {Utils} from "./services/Utils.service";


@NgModule({
    declarations: [
        AppComponent,
    ],
    imports: [
        HttpClientModule,
        BrowserModule.withServerTransition({appId: 'serverApp'}),
        AppRoutingModule,
        GlobalComponentsModule,
        UsersModule,
        HomeModule
    ],
    providers: [
        Title,
        Meta,
        Backend,
        Session,
        Broadcast,
        Utils
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
