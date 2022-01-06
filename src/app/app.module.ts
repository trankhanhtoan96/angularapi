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
import {Metadata} from "./services/Metadata.service";
import {FooterService} from "./services/Footer.service";
import {SpinnerDottedModule} from "spinners-angular/spinner-dotted";
import {AdministrationModule} from "./modules/administration/Administration.module";


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
        HomeModule,
        AdministrationModule,
        SpinnerDottedModule
    ],
    providers: [
        Title,
        Meta,
        Backend,
        Session,
        Broadcast,
        Utils,
        Metadata,
        FooterService
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
