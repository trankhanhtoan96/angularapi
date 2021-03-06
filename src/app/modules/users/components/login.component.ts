import {Component, HostListener, OnInit} from '@angular/core';
import {Backend} from "../../../services/Backend.service";
import {Session} from "../../../services/Session.service";
import {Router} from "@angular/router";
import {Metadata} from "../../../services/Metadata.service";
import {FacebookLoginProvider, GoogleLoginProvider, SocialAuthService, MicrosoftLoginProvider} from "angularx-social-login";
import { UserAgentApplication } from 'msal';
import {environment} from "../../../../environments/environment";
import {ToastComponent} from "../../../globalcomponents/components/toast.component";

@Component({
    selector: 'LoginComponent',
    templateUrl: '../templates/login.html'
})
export class LoginComponent implements OnInit {
    public username: string = '';
    public password: string = '';
    private userAgentMSApplication: UserAgentApplication;
    private msalConfig = {
        auth: {
            clientId: environment.MSClientID
        }
    };
    constructor(
        private backend: Backend,
        public session: Session,
        private router: Router,
        private metadata: Metadata,
        private socialAuthService: SocialAuthService,
        private toast: ToastComponent
    ) {
        this.userAgentMSApplication = new UserAgentApplication(this.msalConfig);
        this.userAgentMSApplication.handleRedirectCallback((error, response) => {
            console.log('ms-redirect',response);
        });
    }

    ngOnInit(): void {
        this.socialAuthService.authState.subscribe((user)=>{
            this.metadata.spinnerLoading().then(ref => {
                this.backend.auth3rd('authentication/login',{
                    email: user.email,
                    firstName: user.firstName,
                    lastName: user.lastName,
                    id: user.id,
                    idToken: user.idToken,
                    photoUrl: user.photoUrl,
                    provider: user.provider
                }).subscribe(res=>{
                    if (res.id) {
                        this.session.setSessionData({
                            admin: res.admin,
                            email: res.email,
                            first_name: res.first_name,
                            id: res.id,
                            last_name: res.last_name,
                            user_image: res.user_image,
                            user_name: res.user_name,
                            userid: res.userid,
                            apiKey: res.apikey,
                            role: res.t_role
                        });
                        this.router.navigate(['/']);
                    }
                    ref.instance.self.destroy();
                })
            });
        });
    }

    login() {
        this.metadata.spinnerLoading().then(ref => {
            this.backend.auth('basic', 'authentication/login', {username: this.username, password: this.password})
                .subscribe(res => {
                    if (res.id) {
                        this.session.setSessionData({
                            admin: res.admin,
                            email: res.email,
                            first_name: res.first_name,
                            id: res.id,
                            last_name: res.last_name,
                            user_image: res.user_image,
                            user_name: res.user_name,
                            userid: res.userid,
                            apiKey: res.apikey,
                            role: res.t_role
                        });
                        this.router.navigate(['/']);
                    }
                    ref.instance.self.destroy();
                });
        });
    }

    loginWithGoogle() {
        this.socialAuthService.signIn(GoogleLoginProvider.PROVIDER_ID);
    }

    loginWithFacebook(){
        this.socialAuthService.signIn(FacebookLoginProvider.PROVIDER_ID);
    }

    loginWithMicrosoft() {
        // this.socialAuthService.signIn(MicrosoftLoginProvider.PROVIDER_ID);
        var loginRequest = {
            scopes: ["user.read", "mail.send"] // optional Array<string>
        };

        this.userAgentMSApplication
            .loginPopup(loginRequest)
            .then(response => {
                // handle response
                // console.log('loginpopup',response);
                const user = response.account;
                this.metadata.spinnerLoading().then(ref => {
                    this.backend.auth3rd('authentication/login',{
                        email: user.userName,
                        firstName: user.name,
                        lastName: '',
                        id: user.accountIdentifier,
                        idToken: '',
                        photoUrl: '',
                        provider: 'MICROSOFT'
                    }).subscribe(res=>{
                        if (res.id) {
                            this.session.setSessionData({
                                admin: res.admin,
                                email: res.email,
                                first_name: res.first_name,
                                id: res.id,
                                last_name: res.last_name,
                                user_image: res.user_image,
                                user_name: res.user_name,
                                userid: res.userid,
                                apiKey: res.apikey,
                                role: res.t_role
                            });
                            this.router.navigate(['/']);
                        }
                        ref.instance.self.destroy();
                    })
                });
            })
            .catch(err => {
                // handle error
                this.toast.error('Kh??ng th??? ????ng nh???p v???i Microsoft!');
            });
    }

    @HostListener('document:keypress', ['$event'])
    keyEvent(event: KeyboardEvent) {
        if (event.keyCode === 13) {
            this.login();
        }
    }
}
