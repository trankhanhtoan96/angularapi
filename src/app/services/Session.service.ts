import {Injectable} from '@angular/core';
import {HttpHeaders} from "@angular/common/http";
import {environment} from "../../environments/environment";
import {CanActivate, Router} from "@angular/router";

interface IFAuthData {
    id: string;
    userid: string;
    user_name: string;
    user_image: string;
    last_name: string;
    first_name: string;
    email: string;
    admin: boolean;
    apiKey:string;
    role:string;
}

@Injectable()
export class Session {
    fields: any;
    lang: any;
    enum: any;
    setting:any={
        ads_auto: "",
        ads_midimage: "",
        ads_midtime: "",
        ads_sideimage: "",
        ads_sidetime: "",
        ads_topimage: "",
        ads_toptime: "",
        system_favicon: '',
        system_image: '',
        system_info: '',
        system_name: '',
        ads_topgg:'',
        ads_toplink: '',
        ads_midgg:'',
        ads_midlink: '',
        ads_sidegg:'',
        ads_sidelink: ''
    };
    public authData: IFAuthData = {
        admin: false,
        email: "",
        first_name: "",
        id: "",
        last_name: "",
        user_image: "",
        user_name: "",
        userid: "",
        apiKey:"",
        role:''
    };

    getSessionHeader(): HttpHeaders {
        if (!this.authData.id) this.getSessionData();
        let headers = new HttpHeaders();
        // headers = headers.set('OAuth-Token', this.authData.id);
        headers = headers.set('apikey', this.authData.apiKey);
        return headers;
    }

    checkRole(roleName:string[]):boolean{
        return roleName.indexOf(this.authData.role) >= 0;

    }

    setSessionData(data: IFAuthData): void {
        this.authData = data;
        localStorage.setItem(environment.apiKey, window.btoa(encodeURIComponent(JSON.stringify(data))));
    }

    clearSessionData(): void {
        localStorage.removeItem(environment.apiKey);
    }

    getSessionData(): IFAuthData | false {
        if (this.authData.id) return this.authData;
        try {
            // @ts-ignore
            this.authData = JSON.parse(decodeURIComponent(window.atob(localStorage.getItem(environment.apiKey))));
            return this.authData;
        } catch (e) {
            return false;
        }
    }

    endSession(): void {
        this.authData = {
            admin: false,
            email: "",
            first_name: "",
            id: "",
            last_name: "",
            user_image: "",
            user_name: "",
            userid: "",
            apiKey:"",
            role:''
        };
        localStorage.clear();
    }

    isAdmin(): boolean {
        return this.authData.admin;
    }

    isLogined(): boolean {
        this.getSessionData();
        return !!this.authData.id;
    }
}

@Injectable()
export class LoginCheck implements CanActivate {
    constructor(private session: Session, private router: Router) {
    }

    public canActivate(route: any, state: { url: string; }) {
        if (!this.session.isLogined()) {
            if (state.url != '/login') {
                this.router.navigate(['/login']);
            }
            return false;
        } else {
            return true;
        }
    }
}
