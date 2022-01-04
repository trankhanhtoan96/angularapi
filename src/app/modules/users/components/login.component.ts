import {Component, OnInit} from '@angular/core';
import {Backend} from "../../../services/Backend.service";
import {Session} from "../../../services/Session.service";
import {Router} from "@angular/router";
import {Meta, Title} from "@angular/platform-browser";

@Component({
    selector: 'LoginComponent',
    templateUrl: '../templates/login.html'
})
export class LoginComponent implements OnInit {
    public username: string = 'admin';
    public password: string = 'Tkt!29101996';

    constructor(
        private backend: Backend,
        private session: Session,
        private router: Router,
        private title: Title,
        private meta: Meta
    ) {
    }

    ngOnInit(): void {
        this.title.setTitle('Đăng nhập');
        this.meta.addTags([
            {name: 'keyword', content: 'Đăng nhập'},
            {name: 'description', content: 'Đăng nhập'}
        ]);
    }

    login() {
        this.backend.auth('basic', 'authentication/login', {username: this.username, password: this.password}).subscribe(res => {
            if (res.id) {
                console.log(res);
                this.session.setSessionData({
                    admin: res.admin,
                    email: res.email,
                    first_name: res.first_name,
                    id: res.id,
                    last_name: res.last_name,
                    user_image: res.user_image,
                    user_name: res.user_name,
                    userid: res.userid
                });
                this.router.navigate(['/']);
            }
        });
    }
}
