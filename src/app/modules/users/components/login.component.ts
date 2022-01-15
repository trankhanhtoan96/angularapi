import {Component, OnInit} from '@angular/core';
import {Backend} from "../../../services/Backend.service";
import {Session} from "../../../services/Session.service";
import {Router} from "@angular/router";
import {Metadata} from "../../../services/Metadata.service";

@Component({
    selector: 'LoginComponent',
    templateUrl: '../templates/login.html'
})
export class LoginComponent implements OnInit {
    public username: string = '';
    public password: string = '';

    constructor(
        private backend: Backend,
        public session: Session,
        private router: Router,
        private metadata: Metadata
    ) {
    }

    ngOnInit(): void {
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
}
