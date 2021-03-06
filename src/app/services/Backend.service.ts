import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders, HttpParams} from "@angular/common/http";
import {Subject, Observable} from 'rxjs';
import {Router} from '@angular/router';
import {Session} from "./Session.service";
import {environment} from "../../environments/environment";
import {Metadata} from "./Metadata.service";
import {ToastComponent} from "../globalcomponents/components/toast.component";

@Injectable()
export class Backend {
    constructor(
        private http: HttpClient,
        private session: Session,
        private router: Router,
        private metadata: Metadata,
        private toast: ToastComponent
    ) {
    }

    private getHeaders(): HttpHeaders {
        let headers = this.session.getSessionHeader();
        headers = headers.set('Accept', 'application/json');
        headers = headers.set('apikey', this.session.authData.apiKey);
        return headers;
    }


    private prepareParams(params: object): HttpParams {
        let output = new HttpParams();
        if (params) {
            Object.keys(params).forEach((key: string) => {
                // @ts-ignore
                let value = params[key];
                if (typeof value !== 'undefined' && value !== null) {
                    if (typeof value === 'object') {
                        output = output.append(key, JSON.stringify(value));
                    } else if (typeof value === 'boolean') {
                        output = output.append(key, value ? '1' : '0');
                    } else if (typeof value === 'number') {
                        output = output.append(key, value + '');
                    } else {
                        output = output.append(key, value.toString());
                    }
                }
            });
        }
        output = output.append('apikey', this.session.authData.apiKey.toString());
        return output;
    }

    getRequestNoAuth(route: string = "", params: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        let headers = this.getHeaders();
        headers = headers.set('apikey', environment.apiKey);
        this.http.get(environment.apiUrl + "/" + encodeURI(route),
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: err => {
                // @ts-ignore
                this.handleError(err, route, 'GET', {getParams: params});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    getRequest(route: string = "", params: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        let headers = this.getHeaders();
        this.http.get(environment.apiUrl + "/" + encodeURI(route),
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: err => {
                // @ts-ignore
                this.handleError(err, route, 'GET', {getParams: params});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    auth(authType: 'basic' | 'form', route: string, params: { username: string, password: string }): Observable<any> {
        let subject = new Subject<any>();
        let theaders = new HttpHeaders();
        theaders = theaders.set('Accept', 'application/json');
        // theaders = theaders.set('Authorization', 'Basic ' + window.btoa(params.username + ':' + params.password));
        theaders = theaders.set('apiup', window.btoa(params.username + ':' + params.password));
        this.http.get(environment.apiUrl + "/" + encodeURI(route), {headers: theaders, observe: "response"}).subscribe({
            next: (res) => {
                subject.next(res.body);
                subject.complete();
            },
            error: err => {
                // @ts-ignore
                this.handleError(err, route, 'GET', {getParams: params});
                subject.next(err.status);
                subject.complete();
                this.toast.error(err.statusText);
            }
        });
        return subject.asObservable();
    }

    auth3rd(route: string, body: {
        email: string,
        firstName: string,
        lastName: string,
        id: string,
        idToken: string,
        photoUrl: string,
        provider: string
    }): Observable<any> {
        let subject = new Subject<any>();
        let theaders = new HttpHeaders();
        theaders = theaders.set('Accept', 'application/json');
        this.http.post(environment.apiUrl + "/" + encodeURI(route), body, {headers: theaders, observe: "response"}).subscribe({
            next: (res) => {
                subject.next(res.body);
                subject.complete();
            },
            error: err => {
                // @ts-ignore
                this.handleError(err, route, 'POST', {getParams: {}, body: body});
                subject.next(err.status);
                subject.complete();
                this.toast.error(err.statusText);
            }
        });
        return subject.asObservable();
    }


    postRequestNoAuth(route: string = "", params: any = {}, body: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();

        let headers = this.getHeaders();
        if (body) {
            headers = headers.set("Content-Type", "application/json");
        } else {
            headers = headers.set("Content-Type", "application/x-www-form-urlencoded");
        }
        headers = headers.set('apikey', environment.apiKey);
        this.http.post(environment.apiUrl + "/" + encodeURI(route), body,
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: err => {
                // @ts-ignore
                this.handleError(err, route, 'POST', {getParams: params, body: body});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    postRequest(route: string = "", params: any = {}, body: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();

        let headers = this.getHeaders();
        if (body) {
            headers = headers.set("Content-Type", "application/json");
        } else {
            headers = headers.set("Content-Type", "application/x-www-form-urlencoded");
        }
        this.http.post(environment.apiUrl + "/" + encodeURI(route), body,
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: err => {
                // @ts-ignore
                this.handleError(err, route, 'POST', {getParams: params, body: body});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    putRequestNoAuth(route: string = "", params: any = {}, body: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        let headers = this.getHeaders();
        headers = headers.set('apikey', environment.apiKey);
        this.http.put(
            environment.apiUrl + "/" + route,
            body,
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: (err) => {
                // @ts-ignore
                this.handleError(err, route, 'PUT', {getParams: params, body: body});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    putRequest(route: string = "", params: any = {}, body: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        let headers = this.getHeaders();
        this.http.put(
            environment.apiUrl + "/" + route,
            body,
            {headers: headers, observe: "response", params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res.body);
                responseSubject.complete();
            },
            error: (err) => {
                // @ts-ignore
                this.handleError(err, route, 'PUT', {getParams: params, body: body});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    deleteRequestNoAuth(route: string = "", params: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        let headers = this.getHeaders();
        headers = headers.set('apikey', environment.apiKey);
        this.http.delete(
            environment.apiUrl + "/" + route,
            {headers: headers, params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res ? res : true);
                responseSubject.complete();
            },
            error: (err) => {
                // @ts-ignore
                this.handleError(err, route, 'DELETE', {getParams: params});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    deleteRequest(route: string = "", params: any = {}): Observable<any> {
        let responseSubject = new Subject<any>();
        let headers = this.getHeaders();
        this.http.delete(
            environment.apiUrl + "/" + route,
            {headers: headers, params: this.prepareParams(params)}
        ).subscribe({
            next: (res) => {
                responseSubject.next(res ? res : true);
                responseSubject.complete();
            },
            error: (err) => {
                // @ts-ignore
                this.handleError(err, route, 'DELETE', {getParams: params});
                responseSubject.error(err);
            }
        });
        return responseSubject.asObservable();
    }

    private handleError(err: { status: any; }, route: string, method: string, data = null) {
        switch (err.status) {
            case 401:
                this.session.endSession();
                this.router.navigate(["/login"]);
                break;
            case 0:
                console.log(err, route, method, data);
                break;
        }
    }

    repair() {
        this.metadata.spinnerLoading().then(ref => {
            this.getRequest('admin/repair/sql').subscribe(res => {
                this.getRequest('admin/repair/cache').subscribe(res => {
                    ref.instance.self.destroy();
                    window.location.reload();
                });
            });
        });
    }
}
