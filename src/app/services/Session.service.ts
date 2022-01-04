import {Injectable} from '@angular/core';
import {HttpHeaders} from "@angular/common/http";
import {environment} from "../../environments/environment";

interface IFAuthData {
    id: string;
    userid: string;
    user_name: string;
    user_image: string;
    last_name: string;
    first_name: string;
    email: string;
    admin: boolean;
}

@Injectable()
export class Session {
    public authData: IFAuthData = {
        admin: false,
        email: "",
        first_name: "",
        id: "",
        last_name: "",
        user_image: "",
        user_name: "",
        userid: ""
    };

    getSessionHeader(): HttpHeaders {
        if(!this.authData.id) this.getSessionData();
        let headers = new HttpHeaders();
        headers = headers.set('OAuth-Token', this.authData.id);
        return headers;
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
            userid: ""
        };
        localStorage.clear();
    }

    isAdmin(): boolean {
        return this.authData.admin;
    }
}
