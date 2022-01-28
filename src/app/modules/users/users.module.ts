import {NgModule} from '@angular/core';
import {LoginComponent} from "./components/login.component";
import {AppRoutingModule} from "../../app-routing.module";
import {FormsModule} from "@angular/forms";
import {UserEditComponent} from "./components/UserEdit.component";
import {GlobalComponentsModule} from "../../globalcomponents/GlobalComponents.module";
import {UserListComponent} from "./components/UserList.component";
import {SystemModule} from "../../system/System.module";
import {CommonModule} from "@angular/common";
import {SocialLoginModule, SocialAuthServiceConfig, GoogleLoginProvider, FacebookLoginProvider} from 'angularx-social-login';
import {environment} from "../../../environments/environment";

@NgModule({
    declarations: [
        LoginComponent,
        UserEditComponent,
        UserListComponent
    ],
    exports: [
        LoginComponent,
        UserEditComponent,
        UserListComponent
    ],
    imports: [
        AppRoutingModule,
        FormsModule,
        GlobalComponentsModule,
        SystemModule,
        CommonModule,
        SocialLoginModule
    ],
    providers: [
        {
            provide: 'SocialAuthServiceConfig',
            useValue: {
                autoLogin: false,
                providers: [
                    {
                        id: GoogleLoginProvider.PROVIDER_ID,
                        provider: new GoogleLoginProvider(environment.GoogleClientID)
                    },
                    {
                        id: FacebookLoginProvider.PROVIDER_ID,
                        provider: new FacebookLoginProvider(
                            environment.FacebookAppID
                        )
                    }
                ]
            } as SocialAuthServiceConfig,
        }
    ]
})
export class UsersModule {
}
