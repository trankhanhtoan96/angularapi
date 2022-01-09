import {NgModule} from '@angular/core';
import {BrowserModule, Meta, Title} from '@angular/platform-browser';

import {AppComponent} from './app.component';
import {AppRoutingModule} from "./app-routing.module";
import {GlobalComponentsModule} from "./globalcomponents/GlobalComponents.module";
import {UsersModule} from "./modules/users/users.module";
import {Backend} from "./services/Backend.service";
import {HttpClientModule} from "@angular/common/http";
import {LoginCheck, Session} from "./services/Session.service";
import {Broadcast} from "./services/Broadcast.service";
import {Utils} from "./services/Utils.service";
import {Metadata} from "./services/Metadata.service";
import {FooterService} from "./services/Footer.service";
import {SpinnerDottedModule} from "spinners-angular/spinner-dotted";
import {AdministrationModule} from "./modules/administration/Administration.module";
import {Model} from "./services/Model.service";
import {NgSelect2Module} from "ng-select2";


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
        AdministrationModule,
        SpinnerDottedModule,
        NgSelect2Module
    ],
    providers: [
        Title,
        Meta,
        Backend,
        Session,
        Broadcast,
        Utils,
        Metadata,
        FooterService,
        LoginCheck,
        Model
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
}
